<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Users_packages_model;

use function PHPUnit\Framework\fileExists;

class Businesses extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
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
            $data['page'] = FORMS . "business";
            $data['title'] = "Businesses - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }

    public function save_business()
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {

            $status = subscription();

            if ($status == 'active') {

                $id = $this->ionAuth->getUserId();

                $subscription = check_subscription($id);

                if ($subscription) {
                    $allow = '0';
                    if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {
                        $allow = '1';
                    }
                    if ($subscription['no_of_businesses'] == "1" || $allow == '1') {

                        if (isset($_POST) && !empty($_POST)) {

                            $this->validation->setRule('name', 'Name', 'trim|required');
                            $this->validation->setRule('address', 'Address', 'trim|required');
                            $this->validation->setRule('email', 'Email', 'trim|required|valid_email');
                            $this->validation->setRule('tax_name', 'Tax Name', 'trim|required');
                            $this->validation->setRule('tax_value', 'Tax Value', 'trim|required');
                            $this->validation->setRule('bank_details', 'Bank Details', 'trim|required');

                            $this->validation->setRule('description', 'Description', 'trim|required|min_length[3]|max_length[255]');
                            $this->validation->setRule('website', 'Website', 'trim|valid_url', errors: [
                                'valid_url' => 'Please enter a valid website URL.'
                            ]);
                            $this->validation->setRule('contact', 'Contact', 'trim|required|numeric|greater_than_equal_to[0]', errors: [
                                'required' => 'Contact Number is required.',
                                'numeric' => 'Contact Number must be a number.',
                                'greater_than_equal_to' => 'Contact Number must be a non-negative number.',
                            ]);


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
                                $users_package_model = new Users_packages_model();
                                $id = $this->ionAuth->getUserId();
                                $users_packages = $users_package_model->get_package($id);
                                $no_of_buinesses = array_column($users_packages, 'no_of_businesses');
                                $business_model = new Businesses_model();

                                $icon = $this->request->getVar('icon');
                                $old_icon = $this->request->getVar('old_icon');

                                $business_id = (isset($_POST['business_id'])) ? $_POST['business_id'] : "";



                                if (isset($_POST['status'])) {
                                    $status = "1";
                                } else {
                                    $status = "0";
                                }

                                $icon = isset($_POST['business_input_image']) && !empty($_POST['business_input_image']) ? $_POST['business_input_image'] : (isset($_POST['edit_business_input_image']) && !empty($_POST['edit_business_input_image']) ? $_POST['edit_business_input_image'] : '');

                                $business = array(
                                    'id' => $business_id,
                                    'user_id' => $id,
                                    'name' => $this->request->getVar('name'),
                                    'icon' => $icon,
                                    'description' => $this->request->getVar('description'),
                                    'address' => $this->request->getVar('address'),
                                    'email' => $this->request->getVar('email'),
                                    'contact' => $this->request->getVar('contact'),
                                    'website' => $this->request->getVar('website'),
                                    'tax_name' => $this->request->getVar('tax_name'),
                                    'tax_value' => $this->request->getVar('tax_value'),
                                    'bank_details' => $this->request->getVar('bank_details'),
                                    'status' => $status
                                );
                                $business_model->save($business);
                                $response = [
                                    'error' => false,
                                    'message' => 'Business Saved succesfully',
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                        }
                    } else {

                        $response = [
                            'error' => true,
                            'message' => ['You have Exceeded limit of adding business'],
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = ['You have Exceeded limit of adding business'];
                        $_SESSION['toastMessageType'] = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                } else {
                    $response = [
                        'error' => true,
                        'message' => ['Please Buy Subscription to proceed ahead!'],
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }
    public function business_table()
    {
        $business_model = new Businesses_model();
        $user_id = $this->ionAuth->getUserId();

        $businesses = $business_model->get_businesses($user_id);
        $rows = [];

        foreach ($businesses['data'] as $business) {
            $business_id = $business['id'];

            // Status badge
            $status_badge = $business['status'] == "1"
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            // Default business radio input
            $checked = $business['default_business'] == "1" ? "checked" : "";
            $default_business =
                '<label class="custom-switch default_business">
                    <input type="radio" name="default_business" data-id="' . $business_id . '" value="' . $business_id . '" onclick="update_default_business(this)" class="custom-switch-input" ' . $checked . '>
                    <span class="custom-switch-indicator"></span>
                </label>';

            // Icon HTML
            if (file_exists($business['icon'])) {
                $image = base_url($business['icon']);
            } else {
                $image = base_url('public/backend/assets/img/no-image.jpg');
            }
            $icon = "<div><img class='img-fluid' src='$image'></div>";

            // Edit button
            $edit_button =
                '<button onclick="edit_business(this)" data-business_id="' . $business_id . '" class="btn btn-primary btn-sm">
                 <i class="bi bi-pencil"></i>
                 </button>';

            // Add row
            $rows[] = [
                'id' => $business_id,
                'name' => $business['name'],
                'icon' => $icon,
                'description' => $business['description'],
                'address' => $business['address'],
                'contact' => $business['contact'],
                'tax_name' => $business['tax_name'],
                'tax_value' => $business['tax_value'],
                'bank_details' => $business['bank_details'],
                'status' => $status_badge,
                'email' => $business['email'],
                'website' => $business['website'],
                'deafault_business' => $default_business,
                'action' => $edit_button
            ];
        }

        // Final response
        $response = [
            'total' => $businesses['total'],
            'rows' => $rows
        ];

        echo json_encode($response);
    }
    public function update_default_business()
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
        $status = subscription();
        if ($status == 'active') {
            $business_id = $_GET['id'];
            $vendor_id = $this->ionAuth->getUserId();
            $default_business = $_GET['default_business'];
            $business_model = new Businesses_model();
            $businesses = $business_model->get_businesses($vendor_id);
            if (is_array($businesses)) {
                foreach ($businesses['data'] as $business) {
                    if ($business['default_business'] == 1) {
                        $default_id = $business['id'];
                        if ($business_id != $default_id) {
                            update_details(['default_business' => $default_business], ['id' => $business_id], 'businesses');
                            update_details(['default_business' => "0"], ['id' => $default_id], 'businesses');
                        }
                    } else {
                        update_details(['default_business' => $default_business], ['id' => $business_id], 'businesses');
                    }
                }
                $response = [
                    'error' => false,
                    'message' => "Default business updated successfully!",
                    'data' => []
                ];
            } else {

                $response = [
                    'error' => true,
                    'message' => "default business can't be updated",
                    'data' => []
                ];
            }
            return $this->response->setJSON($response);
        }
        if ($status == 'upcoming') {
            $response = [
                'error' => true,
                'message' => ['Your subscription has not started yet!'],
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            $_SESSION['toastMessage'] = ['Your subscription has not started yet!'];
            $_SESSION['toastMessageType'] = 'error';
            $this->session->markAsFlashdata('toastMessage');
            $this->session->markAsFlashdata('toastMessageType');
            return $this->response->setJSON($response);
        }
        if ($status == 'expired') {
            $response = [
                'error' => true,
                'message' => ['Please Buy Subscription to proceed ahead!'],
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }

    public function edit_business($B_id)
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $business_id = $B_id;
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
            $data['page'] = FORMS . "business";
            $data['title'] = "Edit Business - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $business_array = fetch_details('businesses', ['id' => $business_id]);
            $business = (isset($business_array[0])) ? $business_array[0] : $business_array = [];
            $data['business'] = $business;
            $data['user'] = $this->ionAuth->user($id)->row();
            $response = [
                'error' => false,
                'business' => $business,
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }
}
