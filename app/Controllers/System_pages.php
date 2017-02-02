<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class System_pages extends BaseController
{
    public $ionAuth ;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }
    public function index()
    {
        return redirect()->to('login');
    }
    public function about_us()
    {
        $general = get_settings('general', true);
        $setting = get_settings('about_us', true);
        $company_title = get_app_display_name($general['title'] ?? '');
        $data['company'] = $company_title;
        $data['page'] = "about_us";
        $data['title'] = "About Us - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Maareeye, an digital solution for your subscription based daily problems";
        $data['text'] = isset($setting['about_us']) ? $setting['about_us'] : '';
        $data['logo'] = (isset($general['logo'])) ? $general['logo'] : "";
        $data['half_logo'] = (isset($general['half_logo'])) ? $general['half_logo'] : "";
        $data['favicon'] = (isset($general['favicon'])) ? $general['favicon'] : "";
        $about_us = isset($setting['about_us']) ? $setting['about_us'] : '';
        $response = [
            'error' => false,
            'text' => $about_us,
            'header' => 'About Us'
        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }
    public function privacy_policy()
    {
        $general = get_settings('general', true);
        $setting = get_settings('privacy_policy', true);
        $company_title = get_app_display_name($general['title'] ?? '');
        $data['company'] = $company_title;
        $data['page'] = "privacy_policy";
        $data['title'] = "Privacy Policy - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Maareeye, an digital solution for your subscription based daily problems";
        $data['text'] = isset($setting['privacy_policy']) ? $setting['privacy_policy'] : '';
        $data['logo'] = (isset($general['logo'])) ? $general['logo'] : "";
        $data['half_logo'] = (isset($general['half_logo'])) ? $general['half_logo'] : "";
        $data['favicon'] = (isset($general['favicon'])) ? $general['favicon'] : "";
        $privacy_policy = isset($setting['privacy_policy']) ? $setting['privacy_policy'] : '';
        $response = [
            'error' => false,
            'text' => $privacy_policy,
            'header' => 'Privacy Policy'

        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }
    public function refund_policy()
    {
        $general = get_settings('general', true);
        $setting = get_settings('refund_policy', true);
        $company_title = get_app_display_name($general['title'] ?? '');
        $data['company'] = $company_title;
        $data['page'] = "refund_policy";
        $data['title'] = "Refund Policy - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Maareeye, an digital solution for your subscription based daily problems";
        $data['text'] = isset($setting['refund_policy']) ? $setting['refund_policy'] : '';
        $data['logo'] = (isset($general['logo'])) ? $general['logo'] : "";
        $data['half_logo'] = (isset($general['half_logo'])) ? $general['half_logo'] : "";
        $data['favicon'] = (isset($general['favicon'])) ? $general['favicon'] : "";
        $refund_policy = isset($setting['refund_policy']) ? $setting['refund_policy'] : '';
        $response = [
            'error' => false,
            'text' => $refund_policy,
            'header' => 'Refund Policy'

        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }
    public function terms_and_conditions()
    {
        $general = get_settings('general', true);
        $setting = get_settings('terms_and_conditions', true);
        $company_title = get_app_display_name($general['title'] ?? '');
        $data['company'] = $company_title;
        $data['page'] = "terms_and_conditions";
        $data['title'] = "Terms And Conditions - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Maareeye, an digital solution for your subscription based daily problems";
        $data['text'] = isset($setting['terms_and_conditions']) ? $setting['terms_and_conditions'] : '';
        $data['logo'] = (isset($general['logo'])) ? $general['logo'] : "";
        $data['half_logo'] = (isset($general['half_logo'])) ? $general['half_logo'] : "";
        $data['favicon'] = (isset($general['favicon'])) ? $general['favicon'] : "";
        $terms_and_conditions = isset($setting['terms_and_conditions']) ? $setting['terms_and_conditions'] : '';
        $response = [
            'error' => false,
            'text' => $terms_and_conditions,
            'header' => 'Terms And Conditions'
        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }
}
