<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Subscription_model;

class Cron_job extends BaseController
{
    public $ionAuth ;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }

    public function renew_service()
    {
        $model = new Subscription_model();
        $id =  $model->renew();
        if (isset($id) && !empty($id)) {
            $service_info = $model->renew_message($id);
            if (!empty($service_info)) {
                foreach ($service_info as $info) {
                    $email_id = $info['email'];
                    $email_config = array(
                        'charset' => 'iso-8859-1',
                        'mailType' => 'html'
                    );
                    $subject = "Renewal of subscription";
                    $template = "
                    Renew Service </br>
                    Custoner Name : " . $info['first_name'] . "</br>
                    Service : " . $info['service_name'] . "</br>
                    Amount : " . $info['final_total'] . "</br>
                    Starts On : " . date("d-M-Y h:i A", strtotime($info['starts_on'])) . "</br>
                    Ends On : " . date("d-M-Y h:i A", strtotime($info['ends_on'])) . "</br>";
                    $email = \Config\Services::email();
                    $email->initialize($email_config);
                    $email->setTo(trim($email_id));
                    $email->setSubject($subject);
                    $email->setMessage($template);
                    if ($email->send()) {
                        echo "Email sent!";
                    } else {
                        $data = $email->printDebugger(['headers']);
                        print_r($data);
                    }
                }
            }
        }
    }
}
