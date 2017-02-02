<?php

namespace App\Controllers;

use App\Controllers\Frontend;

class terms_and_conditions extends Frontend
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
        $data['title'] = "T&C &mdash; $this->appName ";
        $data['page'] = VIEWS . 'terms_and_conditions';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        if ($this->ionAuth->loggedIn()) {
            $data['logged'] = true;
        }

        $setting = get_settings('terms_and_conditions', true);
        $data['text'] = isset($setting['terms_and_conditions']) ? $setting['terms_and_conditions'] : '';
        return view("frontend/template", $data);
    }
}
