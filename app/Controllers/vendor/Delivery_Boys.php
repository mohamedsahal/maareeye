<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Delivery_boys_model;

class Delivery_Boys extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;

    public $data;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'delivery_boys';
            $data['title'] = "Delivery Boys - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['vendor_id'] = $id;
            $businesses = fetch_details("businesses", ['user_id' => $_SESSION['user_id']]);
            $data['businesses'] = isset($businesses) ? $businesses : "";
            $data['user'] = $this->ionAuth->user($id)->row();
            $data['business_id'] = $business_id;
            return view("vendor/template", $data);
        }
    }

    protected $validationListTemplate = 'list';
    protected $ionAuthModel;
    public function save()
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
        if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
            // business id is not set 
            $business_model = new Businesses_model();
            $allbusiness = $business_model->findAll();
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {

            if (isset($_POST) && !empty($_POST)) {
                $vendor_id = $_SESSION['user_id'];

                $delivery_boys_model = new Delivery_boys_model();
                $this->validation->setRule('first_name', lang('Auth.edit_user_validation_fname_label'), 'trim|required');
                $this->validation->setRule('identity', "Mobile", 'trim|required');
                $this->validation->setRule('email', lang('Auth.edit_user_validation_email_label'), 'trim|required');
                $this->validation->setRule('business_id', 'business', 'required');

                $this->validation->setRule('identity', 'Mobile Number', 'trim|required|numeric|greater_than_equal_to[0]', errors: [
                    'required' => 'Mobile Number is required.',
                    'numeric' => 'Mobile Number must be a number.',
                    'greater_than_equal_to' => 'Mobile Number must be a non-negative number.',
                ]);

                if ($this->request->getMethod() === 'POST') {
                    if ($this->validation->withRequest($this->request)->run()) {
                        $userData = [
                            "first_name" => $this->request->getVar('first_name'),
                            "phone" => $this->request->getVar('identity'),
                            "email" => $this->request->getVar('email'),
                        ];

                        $password = $this->request->getPost('password');
                        if (!empty($password)) {
                            $password = password_hash($password, algo: PASSWORD_DEFAULT);
                            $userData["password"] = $password;
                        }

                        $delivery_boy_id = $this->request->getVar("delivery_boy_id");

                        if (empty($delivery_boy_id)) {

                            $tables = $this->configIonAuth->tables;
                            $identityColumn = $this->configIonAuth->identity;
                            $this->data['identity_column'] = $identityColumn;

                            $email = strtolower($userData['email']);
                            $identity = ($identityColumn === 'email') ? trim(strtolower($userData['email'])) : trim($userData['phone']);
                            ;
                            $group_id_arry = fetch_details("groups", ['name' => 'delivery_boys'], "id");
                            $group_id = [$group_id_arry[0]['id']];

                            $additionalData = [
                                'first_name' => $userData['first_name'],
                                'phone' => $userData['phone'],
                            ];
                            $new_user_id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);

                            $status = empty($this->request->getVar('status')) ? 0 : 1;

                            $permissions = [
                                'customer_permission' => $this->request->getVar('customer_permission') == "on" ? "1" : "0",
                                'transaction_permission' => $this->request->getVar('transaction_permission') == "on" ? "1" : "0",
                                'orders_permission' => $this->request->getVar('orders_permission') == "on" ? "1" : "0"
                            ];

                            $delivery_boy_data = [
                                "vendor_id" => $vendor_id,
                                "user_id" => $new_user_id,
                                "permissions" => json_encode($permissions),
                                "status" => $status
                            ];
                            $business_ids = $this->request->getVar('business_id');
                            foreach ($business_ids as $business_id) {

                                $delivery_boy_data['business_id'] = (int) $business_id;

                                $new_delivery_boy = $delivery_boys_model->insert((object) $delivery_boy_data);
                            }
                        } else {

                            $user_id = $delivery_boys_model->find($delivery_boy_id);
                            $user_id = $user_id['user_id'];

                            $this->ionAuth->update($user_id, $userData);
                            $status = empty($this->request->getVar('status')) ? 0 : 1;
                            $permissions = [
                                'customer_permission' => $this->request->getVar('customer_permission') == "on" ? "1" : "0",
                                'transaction_permission' => $this->request->getVar('transaction_permission') == "on" ? "1" : "0",
                                'orders_permission' => $this->request->getVar('orders_permission') == "on" ? "1" : "0"
                            ];
                            $delivery_boy_data = [
                                "vendor_id" => $vendor_id,
                                "user_id" => $user_id,
                                "permissions" => json_encode($permissions),
                                "status" => $status
                            ];
                            $business_ids = $this->request->getVar('business_id');
                            foreach ($business_ids as $business_id) {
                                $delivery_boy_data['business_id'] = (int) $business_id;
                                // Check if record already exists
                                $existing_record = $delivery_boys_model
                                    ->where('vendor_id', $vendor_id)
                                    ->where('user_id', $user_id)
                                    ->where('business_id', $business_id)
                                    ->first();

                                if ($existing_record) {
                                    // Update existing record
                                    $delivery_boy_data['id'] = $existing_record['id']; // Add primary key for update
                                } else {
                                    // Ensure the primary key is not set, so it will insert a new record
                                    unset($delivery_boy_data['id']);
                                }
                                $delivery_boys_model->save($delivery_boy_data);
                            }
                        }

                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['error'] = false;
                        $response['message'] = isset($delivery_boy_id) && !empty($delivery_boy_id) ? ['Delivery boy updated successfully'] : ['Delivery boy added successfully'];
                        return $this->response->setJSON($response);
                    } else {
                        $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = $this->validation->getErrors();
                        return $this->response->setJSON($response);
                    }
                } else {
                    return redirect()->back()->withInput();
                }
            } else {
                return redirect()->back()->withInput();
            }
        }
    }


    public function count($id = "")
    {
        if (!empty($_GET)) {
            $id = $_GET['id'];
            $delivery_boys_model = new Delivery_boys_model();
            $assigned_businesses = $delivery_boys_model->assigned_businesses($id);
            $response['business_id'] = $assigned_businesses;
            $response['error'] = false;
            return json_encode($response);
        }
    }
    public function delivery_boys_table()
    {
        $business_id = $_SESSION['business_id'] ?? "";

        $delivery_boys_model = new Delivery_boys_model();
        $delivery_boys = $delivery_boys_model->get_delivery_boys($business_id);

        $business_model = new Businesses_model();
        $rows = [];

        foreach ($delivery_boys['data'] as $delivery_boy) {
            $user_id = $delivery_boy['user_id'];
            $delivery_boy_id = $delivery_boy['id'];

            // Assigned business IDs
            $assigned_businesses = $delivery_boys_model->assigned_businesses($user_id);
            $assigned_business_ids = array_column($assigned_businesses, 'business_id');
            $delivery_boy['assigned_business_ids'] = $assigned_business_ids;

            // User details
            $userData = fetch_details('users', ['id' => $user_id])[0] ?? [];
            $name = ucwords($userData['first_name'] ?? '');
            $email = $userData['email'] ?? '';
            $mobile = $userData['mobile'] ?? '';

            // Status badge
            $status = ($delivery_boy['status'] == 1)
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            // Business IDs as string (you can replace this with business names if needed)
            $business_names = array_map(function ($bid) use ($business_model) {
                $business = $business_model->find($bid);
                return $business['id'] ?? '';
            }, $assigned_business_ids);
            $business_string = implode(", ", array_filter($business_names));

            // Permissions (decoded from JSON)
            $permissions = json_decode($delivery_boy['permissions'], true) ?? [];

            $rows[] = [
                'id' => $delivery_boy_id,
                'user_id' => $user_id,
                'name' => $name,
                'mobile' => $mobile,
                'email' => $email,
                'assigned_b_id' => $business_string,
                'permissions' => $permissions,
                'status' => $status,
                'active' => $delivery_boy['status'],
            ];
        }

        echo json_encode([
            'total' => $delivery_boys['total'],
            'rows' => $rows,
        ]);

    }
}
