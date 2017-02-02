<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Suppliers_model;

class Suppliers extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    protected $validationListTemplate = 'list';
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
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . 'suppliers_table';
            $data['title'] = "Suppliers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['business_id'] = session('business_id');
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }

    public function create()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('suppliers', ['vendor_id' => $_GET['edit_id']]);
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'suppliers';
            $data['title'] = "Suppliers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }
    public function edit($supplier_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
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
            $data['page'] = FORMS . 'suppliers';
            $data['title'] = "Edit Suppliers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            $supplier_model = new Suppliers_model();
            $supplier = $supplier_model->edit_supplier($supplier_id)[0];
            $data['supplier'] = $supplier;
            $data['user_id'] = $supplier_id;

            return view("vendor/template", $data);
        }
    }

    public function suppliers_table()
    {
        $suppliers_model = new Suppliers_model();
        $user_id = $_SESSION['user_id'];

        // Determine actual vendor_id
        $vendor_id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;

        // Fetch supplier data with total
        $suppliers_data = $suppliers_model->get_suppliers($vendor_id);
        $suppliers = $suppliers_data['data'] ?? [];
        $total = $suppliers_data['total'] ?? 0;

        $rows = [];
        foreach ($suppliers as $supplier) {
            // Action button
            $edit_url = site_url('vendor/suppliers/edit/' . $supplier['user_id']);
            $edit_btn = "<a href='{$edit_url}' class='btn btn-primary btn-sm' data-toggle='tooltip' title='Edit'><i class='bi bi-pencil'></i></a>";

            // Status badge
            $status = $supplier['status'] == "1"
                ? "<span class='badge badge-success'>Active</span>"
                : "<span class='badge badge-danger'>Deactive</span>";

            $rows[] = [
                'id' => $supplier['id'] ?? '',
                'name' => ucwords($supplier['name'] ?? ''),
                'email' => $supplier['email'] ?? '',
                'mobile' => $supplier['mobile'] ?? '',
                'balance' => currency_location(decimal_points($supplier['balance'] ?? 0)),
                'status' => $status,
                'action' => $edit_btn
            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $rows
        ]);
    }


    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }

            if (isset($_POST) && !empty($_POST)) {
                $suppliers_model = new Suppliers_model();
                if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
                    $user_id = $_POST['user_id'];
                    $user = $this->ionAuth->user($user_id)->row();
                    $this->validation->setRule('name', lang('Auth.edit_user_validation_fname_label'), 'trim|required');
                    $this->validation->setRule('identity', "Mobile", 'trim|required', errors: [
                        'required' => 'The Mobile field is required.',
                    ]);
                    $this->validation->setRule('email', lang('Auth.edit_user_validation_email_label'), 'trim|required');

                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                        $data = [
                            'username' => $this->request->getPost('name'),
                            'email' => $this->request->getPost('email'),
                            'identity' => $this->request->getPost('identity'),
                        ];
                        // update the password if it was posted
                        if ($this->request->getPost('password')) {
                            $data['password'] = $this->request->getPost('password');
                        }
                        if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == "on") {
                            $status = "1";
                        } else {
                            $status = "0";
                        }
                        $suppliers = [
                            'user_id' => $this->request->getPost('user_id'),
                            'vendor_id' => $id,
                            'balance' => $this->request->getPost('balance'),
                            'billing_address' => $this->request->getPost('billing_address'),
                            'shipping_address' => $this->request->getPost('shipping_address'),
                            'credit_period' => $this->request->getPost('credit_period'),
                            'credit_limit' => $this->request->getPost('credit_limit'),
                            'tax_name' => $this->request->getPost('tax_name'),
                            'tax_num' => $this->request->getPost('tax_no'),
                            'status' => $status,
                        ];


                        $suppliers_model->update($_POST['supplier_id'], $suppliers);

                        $this->ionAuth->update($user->id, $data);
                        $response = [
                            'error' => false,
                            'message' => 'Supplier updated successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                } else {

                    $tables = $this->configIonAuth->tables;
                    $identityColumn = $this->configIonAuth->identity;
                    $this->data['identity_column'] = $identityColumn;

                    $this->validation->setRule('name', lang('Auth.create_user_validation_fname_label'), 'trim|required');
                    $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']', errors: [
                        'required' => 'The Mobile field is required.',
                        'is_unique' => 'This Mobile Number is already taken. Please choose a different one.',
                    ]);
                    $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'required|trim|valid_email|is_unique[' . $tables['users'] . '.email]');


                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

                        $email = strtolower($this->request->getPost('email'));
                        $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
                        $group_id_arry = fetch_details("groups", ['name' => 'suppliers'], "id");
                        $group_id = [$group_id_arry[0]['id']];
                        $additionalData = [
                            'first_name' => $this->request->getPost('name'),
                            'phone' => $this->request->getPost('phone'),
                        ];
                    }
                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                        $insert_id = $this->ionAuth->register($identity, '12345678', $email, $additionalData, $group_id);
                        if (isset($_POST['status']) && !empty($_POST['status'])) {
                            $status = "1";
                        } else {
                            $status = "0";
                        }


                        $suppliers = [
                            'user_id' => $insert_id,
                            'vendor_id' => $id,
                            'balance' => $this->request->getPost('balance'),
                            'billing_address' => $this->request->getPost('billing_address'),
                            'shipping_address' => $this->request->getPost('shipping_address'),
                            'credit_period' => $this->request->getPost('credit_period'),
                            'credit_limit' => $this->request->getPost('credit_limit'),
                            'tax_name' => $this->request->getPost('tax_name'),
                            'tax_num' => $this->request->getPost('tax_no'),
                            'status' => $status,
                        ];

                        $suppliers_model->save($suppliers);


                        // check to see if we are creating the user
                        // redirect them back to the admin page  
                        $response = [
                            'error' => false,
                            'message' => 'Supplier added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                }
                $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
                $response['error'] = true;
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['message'] = $this->validation->getErrors();
                return $this->response->setJSON($response);
            } else {
                return redirect()->back()->withInput();
            }
        }
    }
}
