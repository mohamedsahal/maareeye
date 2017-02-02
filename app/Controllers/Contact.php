<?php

namespace App\Controllers;

use App\Controllers\Frontend;

class Contact extends Frontend
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
        $data['title'] = "Contact us &mdash; $this->appName ";
        $data['page'] = VIEWS . 'contact';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        if ($this->ionAuth->loggedIn()) {
            $data['logged'] = true;
        }

        $setting = get_settings('about_us', true);
        $data['text'] = isset($setting['about_us']) ? $setting['about_us'] : '';
        return view("frontend/template", $data);
    }

    public function sendMail()
    {
        $setting = get_settings('general', true);
        $mail = (isset($setting['support_email']) ? $setting['support_email'] : "infinitietechnologies04@gmail.com");
        
        $email_config = array(
            'charset' => 'iso-8859-1',
            'mailType' => 'html'
        );
        $template = "
        Contact - us Data </br>
        Name : " . $_POST['name'] . "</br>
        Email : " . $_POST['email'] . "</br>
        Subject : " . $_POST['subject'] . "</br>
        Message : " . $_POST['message'] . "</br>

        ";
        $email = \Config\Services::email();
        $email->initialize($email_config);

        $email->setTo(trim($mail));
        $email->setSubject($_POST['subject']);
        $email->setMessage($template);
        if ($email->send()) {
            return $this->response->setJSON([
                "error" => false,
                "message" => "Thank you for contacting us.",
                "data" => [],
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                "error" => true,
                "message" => "Something went wrong Please try again after some time.",
                "data" => [
                    'console' => "console.log(" . $email->printDebugger() . ");"
                ],
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        }
    }
}
