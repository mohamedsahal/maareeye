<?php

namespace App\Controllers;

use App\Controllers\Frontend;

class Privacy_policy extends Frontend
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
        $data['title'] = "Privacy Policy &mdash; $this->appName ";
        $data['page'] = VIEWS . 'privacy_policy';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        if ($this->ionAuth->loggedIn()) {
            $data['logged'] = true;
        }

        $setting = get_settings('privacy_policy', true);
        $data['text'] = isset($setting['privacy_policy']) ? $setting['privacy_policy'] : '';
        return view("frontend/template", $data);
    }
}
