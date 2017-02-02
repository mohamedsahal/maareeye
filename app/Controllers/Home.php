<?php

namespace App\Controllers;
use App\Controllers\Frontend;

class Home extends Frontend
{
    public $ionAuth ;
    public function __construct()
    {
        parent::__construct();
        $this->ionAuth = new \App\Libraries\IonAuth();

    }
    public function index()
    {
      
        $packages = get_plans_tenures();
        
        $data['packages'] = $packages;
        $data['tenures'] = !empty($packages[0]['tenures']) ? $packages[0]['tenures'] : "";
        $data['title'] = "Home &mdash; $this->appName";
        $data['page'] = VIEWS . 'index';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        if ($this->ionAuth->loggedIn() && $this->ionAuth->isVendor()) {
            $data['vendor'] = true;
        } 
       
        $user = '';
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            $user = "admin";
        } 
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isVendor()) {
            $user = "vendor";
        }
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isTeamMember()) {
            $user = "vendor";
        }
        $data["who_user_is"] = $user ;
        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        $data['currency'] =  $currency;
        return view("frontend/template", $data);
    }
}
