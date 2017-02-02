<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Purchases_model;

class Purchases_Report extends BaseController
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
            $data['page'] = VIEWS . "purchases_report";
            $data['title'] = "Purchases Report - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $purchases_model = new Purchases_model();
            $data['supplier'] = $purchases_model->supplier_details($business_id);
            return view("vendor/template", $data);
        }
    }

    public function purchases_report_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $purchases = new Purchases_model();
        $purchases_report = $purchases->purchases_report_table($business_id);
        echo json_encode($purchases_report);
    }
}
