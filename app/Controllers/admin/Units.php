<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Units_model;

class Units extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
        } else {
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
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'units';
            $data['title'] = "Units -" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $units_model = new Units_model();
            $data['units'] = $units_model->findAll();
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function save_unit()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('admin/home');
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
            if (isset($_POST) && !empty($_POST)) {
                $units_model = new Units_model();
                if (!isset($_POST['unit_id']) && empty($_POST['unit_id'])) {
                    $this->validation->setRules([
                        'name' => 'required|is_unique[units.name]',
                        'symbol' => 'required|is_unique[units.symbol]'
                    ]);
                } else {
                    $this->validation->setRules([
                        'name' => 'required',
                        'symbol' => 'required'
                    ]);
                }
                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors = $this->validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $vendor_id = "";
                    if (!empty($_POST['vendor_id']) && isset($_POST['vendor_id'])) {
                        $vendor_id = $_POST['vendor_id'] ? $_POST['vendor_id'] : "";
                    }

                    $units = array(
                        'id' => $this->request->getVar('unit_id'),
                        'vendor_id' => $vendor_id,
                        'parent_id' => $this->request->getVar('parent_id'),
                        'name' => $this->request->getVar('name'),
                        'symbol' => $this->request->getVar('symbol'),
                        'conversion' => $this->request->getVar('conversion'),
                    );
                    $units_model->save($units);
                    $response = [
                        'error' => false,
                        'message' => 'Unit saved successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->to('admin/units');
            }
        }
    }
    public function unit_table()
    {
        $units_model = new Units_model();
        $units = $units_model->get_units();

        $rows = [];

        foreach ($units['data'] as $unit) {
            $route = base_url('admin/units/get-unit');
            $delete_route = base_url('admin/units/delete-unit');

            if ($unit['parent_id'] != 0) {
                $parent_unit = $units_model->unit_name($unit['parent_id']);
                $parent_unit = $parent_unit[0]['name'];
            } else {
                $parent_unit = "-";
            }

            $action = "
                    <div class=\"d-flex gap-4\">
                        <button type='button' class='btn btn-primary btn-sm editUnit' data-toggle='tooltip' data-bs-placement='bottom' title='Edit Unit' data-bs-toggle='modal' data-bs-target='#update_unit_modal' data-id='" . $unit['id'] . "'> <i class='bi bi-pencil'></i> </button>
                        <button type='button' class='btn btn-danger btn-sm ' data-toggle='tooltip' data-bs-placement='bottom' title='Delete Unit' onclick='deleteUnit(" . $unit['id'] . ", \"" . $delete_route . "\")'> <i class='bi bi-trash'></i> </button>
                    </div>";
            $rows[] = [

                'id' => $unit['id'],
                'vendor_id' => $unit['vendor_id'],
                'parent_id' => $unit['parent_id'],
                'parent_unit' => $parent_unit,
                'name' => ucwords($unit['name']),
                'symbol' => $unit['symbol'],
                'conversion' => $unit['conversion'],
                'action' => $action
            ];
        }

        echo json_encode([
            'total' => $units['total'],
            'rows' => $rows
        ]);

    }

    public function edit_unit($unit_id = "")
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('admin/home/login');
        } else {
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "units";
            $data['title'] = "Edit Units-" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $units_model = new Units_model();
            $uri = current_url(true);
            $data['unit'] = $units_model->find($uri->getSegment(4));
            $parent_id = $data['unit']['parent_id'];
            $data['parent_unit'] = $units_model->find($parent_id);
            $data['units'] = $units_model->findAll();
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function delete()
    {
        $csrf = [
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash()
        ];

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => "Please login!",
                'data' => []
            ], $csrf));
        }

        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'data' => []
            ], $csrf));
        }

        $this->validation->setRules([
            'id' => [
                'rules' => 'required|is_natural_no_zero',
                'label' => 'Unit'
            ],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => $this->validation->getErrors(),
                'data' => []
            ], $csrf));
        }

        $id = $this->request->getPost('id');
        $units_model = new Units_model();
        $unit = $units_model->where(['id' => $id])->first();

        if (!$unit) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => ["Unit not found!"],
                'data' => []
            ], $csrf));
        }

        // Check if unit is used in products
        $db = \Config\Database::connect();
        $productExists = $db->table('products')
            ->where('unit_id', $id)
            ->countAllResults();

        if ($productExists > 0) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => ["You cannot delete this unit as it's used in one or more products."],
                'data' => []
            ], $csrf));
        }
        $productVariantExists = $db->table('products_variants')
            ->where('unit_id', $id)
            ->countAllResults();

        if ($productVariantExists > 0) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => ["You cannot delete this unit as it's used in one or more product variants."],
                'data' => []
            ], $csrf));
        }

        // Delete the unit
        $units_model->delete($id);

        return $this->response->setJSON(array_merge([
            'error' => false,
            'message' => ["Unit deleted successfully."],
            'data' => []
        ], $csrf));
    }

    public function get_unit()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $response = [
                'error' => true,
                'message' => "Please login !",
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
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
                'id' => [
                    'rules' => 'required',
                    'label' => 'Unit'
                ],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $id = $this->request->getPost('id');

                $units_model = new Units_model();
                $unit = $units_model->where(['id' => $id])->get()->getResultArray();
                if (empty($unit)) {
                    $response = [
                        'error' => true,
                        'message' => ["Unit not found !"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
                $unit = $unit[0];
                $response = [
                    'error' => false,
                    'message' => "Success !",
                    'data' => [$unit]
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
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
}
