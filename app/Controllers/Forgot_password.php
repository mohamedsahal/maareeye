<?php

namespace App\Controllers;


use App\Controllers\Frontend;

class Forgot_password extends Frontend
{
    public $ionAuth ;
    public function __construct()
    {
      
        parent::__construct();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }
    public function index()
    {
      
        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "Maareeye";
        $data['company'] = $company_title;
        $data['title'] = "Reset Password &mdash; $this->appName ";
        $data['page'] = VIEWS . 'forgot_password';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";
        return view("frontend/template", $data);
    }
}
