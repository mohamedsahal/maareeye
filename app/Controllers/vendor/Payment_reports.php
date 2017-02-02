<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Payment_reports_model;


class Payment_reports extends BaseController
{
    public $ionAuth ;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
        $this->payment_reports_model = new Payment_reports_model();
    }

    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            if (isset($_SESSION['business_id'])) {
                if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                    return redirect()->to("vendor/businesses");
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
            $data['page'] = VIEWS . "payment_reports";
            $data['title'] = "Payment Reports - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();


            return view("vendor/template", $data);
        }
    }
    public function payment_reports_table()
    {
        $payment_reports_model = new Payment_reports_model();
        $payment_reports = $payment_reports_model->get_payment_reports_list();
        echo json_encode($payment_reports);
    }
}
