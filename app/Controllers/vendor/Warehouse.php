<?php

namespace App\Controllers\Vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\WarehouseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Warehouse extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                // business id is not set 
                $business_model = new Businesses_model();
                $allbusiness = $business_model->select()->where(['user_id' => session('user_id')])->get();
                if (empty($allbusiness)) {
                    session()->setFlashdata('message', 'Please create a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('vendor/businesses');
                } else {
                    session()->setFlashdata('message', 'Please select a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('vendor/businesses');
                }
            }

            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'warehouse';
            $data['title'] = "Warehouse - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data['user'] = $this->ionAuth->user($id)->row();
            if (!isset($_SESSION['business_id'])) {
                $default_business = fetch_details('businesses', ['default_business' => "1", 'user_id' => $id]);
                $business_id = isset($default_business[0]['id']) ? $default_business[0]['id'] : "";
                check_data_in_table('businesses', $business_id);
                $business_name = isset($default_business[0]['name']) ? $default_business[0]['name'] : "";
                $this->session->set('business_id', $business_id);
                $this->session->set('business_name', $business_name);
            } else {
                $business_id = $_SESSION['business_id'];
            }

            return view("vendor/template", $data);
        }
    }

    public function WarehouseTable()
    {

        $limit = (int) ($this->request->getGet('limit') ?? '');
        $offset = (int) ($this->request->getGet('offset') ?? '');
        $search = trim($this->request->getGet('search') ?? '');

        $business_id = $_SESSION['business_id'] ?? '';
        $warehouse_model = new WarehouseModel();

        // Apply filters
        $warehouse_model->where('business_id', $business_id);

        if (!empty($search)) {
            $warehouse_model->groupStart()
                ->like('name', $search)
                ->orLike('city', $search)
                ->orLike('country', $search)
                ->orLike('zip_code', $search)
                ->orLike('address', $search)
                ->groupEnd();
        }

        // Get total results for pagination
        $total = $warehouse_model->countAllResults(false);

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $warehouse_model->limit($limit, $offset);
        }
        // Get paginated results
        $warehouses = $warehouse_model->findAll();

        // Generate response rows
        $data = [];
        $route = base_url('/vendor/warehouse/get-warehouse/');

        foreach ($warehouses as $warehouse) {
            $id = $warehouse['id'];
            $isDefault = $warehouse['default_warehouse'] == "1" ? "checked" : "";

            $default_warehouse =
                '<label class="custom-switch default_business">
                    <input type="radio" name="default_warehouse" data-id="' . $id . '" value="' . $id . '" onclick="update_default_warehouse(this)" class="custom-switch-input" ' . $isDefault . '>
                    <span class="custom-switch-indicator"></span>
                </label>';

            $action = '
            <button type="button" class="btn btn-primary btn-sm" 
                data-toggle="tooltip" 
                data-bs-placement="bottom" 
                title="Edit Warehouse" 
                onclick="editWarehouse(' . $id . ', \'' . $route . '\')">
                <i class="bi bi-pencil"></i>
            </button>';

            $data[] = [
                'id' => $id,
                'name' => $warehouse['name'],
                'city' => $warehouse['city'],
                'country' => $warehouse['country'],
                'zip_code' => $warehouse['zip_code'],
                'address' => $warehouse['address'],
                'default_warehouse' => $default_warehouse,
                'action' => $action,
            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $data
        ]);
    }

    public function save()
    {

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor())) {
            return redirect()->to('login');
        } else {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $response = [
                    'error' => true,
                    'message' => [DEMO_MODE_ERROR],
                    'csrfName' => csrf_token(),
                    'csrfHash' => csrf_hash(),
                    'data' => []
                ];
                return $this->response->setJSON($response);
            }

            $this->validation->setRules([
                'name' => [
                    'rules' => 'required',
                    'label' => 'Name'
                ],
                'country' => [
                    'rules' => 'required',
                    'label' => 'Country'
                ],
                'city' => [
                    'rules' => 'required',
                    'label' => 'City'
                ],
                'zip_code' => [
                    'rules' => 'required',
                    'label' => 'Zip Code'
                ],
                'address' => [
                    'rules' => 'required',
                    'label' => 'Address'
                ]
            ]);
            if ($this->validation->withRequest($this->request)->run()) {
                $id = $this->ionAuth->getUserId();
                $vendor_id = 0;
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($id);
                } else {
                    $vendor_id = $id;
                }

                $db = \Config\Database::connect();
                $user_packages = $db->table('users_packages up')->select('up.*')->where(['user_id' => $vendor_id])->get()->getResultArray();
                $warehouse_model = new WarehouseModel();
                $no_of_warehouse = $warehouse_model->where('vendor_id', $vendor_id)->countAllResults();
                $allowed_warehouse = 0;
                $can_create = true;


                if (!empty($user_packages)) {

                    foreach ($user_packages as $p) {
                        $status = subscription_status($p['id']);
                        if ($status == 'active') {
                            $allowed_warehouse = $p["no_of_warehouse"];
                            break;
                        }
                    }
                }

                $status = subscription();
                if ($status == 'active' && $allowed_warehouse != '-1' && $no_of_warehouse >= $allowed_warehouse) {
                    $can_create = false;
                }


                $business_id = session('business_id');

                $warehouse_id = $this->request->getVar('id');

                $data = [
                    'name' => $this->request->getVar('name'),
                    'country' => $this->request->getVar('country'),
                    'city' => $this->request->getVar('city'),
                    'zip_code' => $this->request->getVar('zip_code'),
                    'address' => $this->request->getVar('address'),
                    'vendor_id' => $vendor_id,
                    'business_id' => $business_id,
                ];


                if (!empty($warehouse_id)) {
                    $data['id'] = $warehouse_id;
                }


                if (!isset($data['id']) && !$can_create) {
                    return $this->response->setJSON([
                        'error' => true,
                        'message' => ["You have reached the limit for createing warehouses!"],
                        'csrf_token' => csrf_token(),
                        'csrf_hash' => csrf_hash(),
                        'data' => []
                    ]);
                }


                $saved = $warehouse_model->save($data);

                if ($saved) {
                    $response = [
                        'error' => false,
                        'message' => ["Warehouse saved successfully"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $response = [
                        'error' => true,
                        'message' => ["Failed to create Warehouse"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                $errors = $this->validation->getErrors();
                $response = [
                    'error' => true,
                    'message' => $errors,
                    'data' => []
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }

    public function getWarehouse($id)
    {
        $warehouse_model = new WarehouseModel();

        $warehouse = $warehouse_model->find($id);


        if (empty($warehouse)) {
            $response = [
                'error' => true,
                'message' => ["Warehouse not Found !"],
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        } else {
            $response = [
                'error' => false,
                'message' => [],
                'data' => $warehouse
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }

    public function update_default_warehouse()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $response = [
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];

            return $this->response->setJSON($response);
        }

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $default_warehouse = fetch_details('warehouses', ['default_warehouse' => 1, 'business_id' => $business_id], 'id');
        $warehouse_id = $_GET['id'];
        $warehouse = fetch_details('warehouses', ['id' => $warehouse_id], 'id,name');
        $data = ['default_warehouse' => 1];


        // Check if there is an existing default warehouse
        if (isset($default_warehouse) && !empty($default_warehouse)) {

            // Step 1: Reset the previous default warehouse by setting 'default_warehouse' to 0
            update_details(['default_warehouse' => 0], ['id' => $default_warehouse[0]['id']], 'warehouses');

            // Step 2: Set the selected warehouse as the new default by updating its data
            update_details($data, ['id' => $warehouse_id], 'warehouses');

            // Prepare and return a success response
            $response = [
                'error' => false,
                'message' => "Default warehouse updated successfully!",
                'data' => []
            ];

        } else {
            // If no previous default warehouse exists, directly update the selected warehouse
            update_details($data, ['id' => $warehouse_id], 'warehouses');

            // Prepare and return a success response
            $response = [
                'error' => false,
                'message' => "Default warehouse updated successfully!",
                'data' => []
            ];
        }

        $sessionData = [
            'default_warehouse_id' => $warehouse[0]['id'],
            'default_warehouse_name' => $warehouse[0]['name'],
        ];

        $this->session->set($sessionData);

        return $this->response->setJSON($response);
    }

}
