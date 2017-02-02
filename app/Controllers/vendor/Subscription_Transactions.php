<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;

use App\Models\Transactions_model;

class Subscription_Transactions extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
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
            $data['page'] = VIEWS . "subscription_transaction";
            $data['title'] = "Subscription Transaction - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }
    public function transactions_table()
    {
        $transactions_model = new Transactions_model();
        $id = $_SESSION['user_id'];

        $transactions = $transactions_model->get_transactions($id);
        $i = 0;
        $array = [];
        foreach ($transactions['data'] as $transaction) {
            $package = fetch_details("users_packages", ['user_id' => $transaction['user_id']])[0];

            $status = $transaction['status'];
            if ($status == 'pending') {
                $status = '<div class="badge badge-primary projects-badge">Pending</div>';
            }
            if ($status == 'captured') {
                $status = '<div class="badge badge-success projects-badge">Success</div>';
            }
            if ($status == 'failed') {
                $status = '<div class="badge badge-danger projects-badge">Failed</div>';
            }
            if ($status == 'Authorized') {
                $status = '<div class="badge badge-success projects-badge">Authorized</div>';
            }
            if ($status == 'successful') {
                $status = '<div class="badge badge-success projects-badge">Success</div>';
            }

            $rows[$i] = [
                'id' => $transaction['id'],
                'name' => ucwords($transaction['first_name'] . " " . $transaction['last_name']),
                'package_name' => $package['package_name'],
                'payment_method' => $transaction['payment_method'],
                'transaction_id' => $transaction['txn_id'],
                'amount' => $transaction['amount'],
                'created_on' => date("d-M-Y h:i A", strtotime($transaction['created_at'])),
                'mobile' => $transaction['mobile'],
                'email' => $transaction['email'],
                'status' => $status
            ];
            $i++;
        }
        if (!empty($transactions)) {

            $array['total'] = $transactions['total'];
            $array['rows'] = $rows;
        }
        echo json_encode($array);
    }
}
