<?php

namespace App\Controllers;

use App\Controllers\Frontend;

class Pricing extends Frontend
{
    public $ionAuth ;
    public function __construct()
    {
        parent::__construct();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }
    public function index()
    {
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            $data['admin'] = true;
        } else {
            $data['admin'] = false;
        }
        $packages = get_plans_tenures();
        $data['packages'] = $packages;
        $data['tenures'] = !empty($packages[0]['tenures']) ? $packages[0]['tenures'] : "";
        $data['title'] = "Pricing &mdash; $this->appName ";
        $data['page'] = VIEWS . 'pricing';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        if ($this->ionAuth->loggedIn()) {
            $data['logged'] = true;
        }

        $setting = get_settings('general', true);
        $data['currency'] =  isset($setting['currency_symbol']) ? $setting['currency_symbol'] : '';
        return view("frontend/template", $data);
    }
}
