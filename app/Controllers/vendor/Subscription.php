<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Users_packages_model;
use App\Libraries\Razorpay;
use App\Libraries\Stripe;

class Subscription extends BaseController
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
            $data['page'] = VIEWS . "subscription";
            $data['title'] = "Subscription - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "View package - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();

            return view("vendor/template", $data);
        }
    }

    public function packages()
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
            $data['page'] = VIEWS . "packages";
            $data['title'] = "Packages - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "View package - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $packages = get_plans_tenures();
            $data['packages'] = $packages;
            $data['tenure'] = !empty($packages[0]['tenures']) ? $packages[0]['tenures'] : "";
            $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['currency'] = $currency;
            $id = $_SESSION['user_id'];
            $data['user_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();

            return view("vendor/template", $data);
        }
    }
    public function checkout()
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $settings = get_settings('general', true);

            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $package_id = $_GET['package_id'];
            $tenure_id = $_GET['tenures'];
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['currency'] = $currency;
            $data['page'] = VIEWS . "checkout";
            $data['title'] = "Checkout - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "View package - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $package = get_plans_tenures($package_id);
            $data['package'] = isset($package) ? $package[0] : "";
            $data['tenure_id'] = $tenure_id;
            $tenures = get_tenures($package_id, $tenure_id);
            $data['tenures'] = !empty($tenures[0]) ? $tenures[0] : "";
            $user_id = $_SESSION['user_id'];
            $data['user_id'] = $user_id;
            $data['user'] = $this->ionAuth->user($user_id)->row();
            $data['vendor_name'] = $data['user']->first_name;
            $data['phone'] = $data['user']->mobile;
            $data['email'] = $data['user']->email;

            $payment = get_settings('payment_gateway', true);

            $razorpay = new Razorpay;
            $stripe = new Stripe;
            $data['payment'] = $payment;
            $data['stripe'] = false;
            $data['razorpay'] = false;
            $data['flutterwave'] = false;
            if ($payment['razorpay_status'] == "1") {
                $data['razorpay_status'] = $payment['razorpay_status'];
                $data['razorpay'] = true;
            }
            $data['razorpay_key'] = $razorpay->get_credentials()['key'];
            $data['razorpay_currency'] = $razorpay->get_credentials()['currency'];
            if ($payment['stripe_status'] == "1") {
                $data['stripe_status'] = $payment['stripe_status'];
                $data['stripe'] = true;
            }
            $data['stripe_key'] = $stripe->get_credentials()['publishable_key'];
            if ($payment['flutterwave_status'] == "1") {
                $data['flutterwave_status'] = $payment['flutterwave_status'];
                $data['flutterwave'] = true;
            }
            return view("vendor/template", $data);
        }
    }

    public function package_table()
    {
        $users_package_model = new Users_packages_model();
        $id = $_SESSION['user_id'];
        $users_packages = $users_package_model->get_package($id);
        $rows = [];
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');

        if (is_array($users_packages)) {
            foreach ($users_packages['data'] as $package) {
                $start_date = date("Y-m-d", strtotime($package['start_date']));
                $end_date = date("Y-m-d", strtotime($package['end_date']));
                $status = check_package_status($start_date, $end_date, $date);
                if ($status == "1") {
                    if ($date < $start_date && $start_date < $end_date) {
                        $package['status'] = "<span class='badge badge-info'>Upcoming</span>";
                    } else {
                        $package['status'] = "<span class='badge badge-primary'>Active</span>";
                    }
                } else {
                    $package['status'] = "<span class='badge badge-danger'>Expired</span>";
                }
                $rows[] = [
                    'id' => $package['id'],
                    'package_name' => $package['package_name'],
                    'no_of_customers' => $package['no_of_customers'],
                    'tenure' => $package['tenure'],
                    'price' => currency_location(decimal_points($package['price'])),
                    'months' => $package['months'],
                    'start_date' => date_formats(strtotime($package['start_date'])),
                    'end_date' => date_formats(strtotime($package['end_date'])),
                    'no_of_businesses' => ($package['no_of_businesses'] == -1) ? "Unlimited" : $package['no_of_businesses'],
                    'no_of_delivery_boys' => ($package['no_of_delivery_boys'] == -1) ? "Unlimited" : $package['no_of_delivery_boys'],
                    'no_of_products' => ($package['no_of_products'] == -1) ? "Unlimited" : $package['no_of_products'],
                    'no_of_warehouse' => ($package['no_of_warehouse'] == -1) ? "Unlimited" : $package['no_of_warehouse'],
                    'status' => $package['status']
                ];
            }
        }

        // Prepare response for bootstrap-table
        $array = [
            'total' => $users_packages['total'],
            'rows' => $rows
        ];

        // Send the JSON response
        echo json_encode($array);
    }


    public function free_subscription()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor())) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'user_id' => 'required',
                    'package_id' => 'required',
                    'tenure' => 'required',
                    'months' => 'required',
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
                    if ($sub_id = add_subscription($_POST['user_id'], $_POST['package_id'], $_POST['months'], $_POST['txn_id'], $_POST['price'], $_POST['tenure'])) {
                        $response = [
                            'error' => false,
                            'message' => 'Users package added succesfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    } else {
                        $response = [
                            'error' => true,
                            'message' => 'Something went wrong!',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                }
            } else {
                return redirect()->to('vendor/subscriptions');
            }
        }
    }
}
