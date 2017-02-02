<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Customers_model;

class Customers extends BaseController
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
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
            $data['page'] = FORMS . "customers";
            $data['title'] = "Customers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $data['delivery_boy_id'] = $id;
            $data['businesses'] = $businesses;
            $data['user'] = $this->ionAuth->user($id)->row();
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            return view("delivery-man/template", $data);
        }
    }
    public function customers_table()
    {
        $business_id = $_SESSION['business_id'] ?? '';
        $customers_model = new Customers_model();
        $customers = $customers_model->get_customers_details($business_id);

        $rows = [];

        foreach ($customers['data'] as $customer) {
            $customer_id = (int) $customer['id'];

            // Status badge
            $status = $customer['status'] == 1
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            // Edit button
            $edit_button = "
                <a href='javascript:void(0)' 
                    data-id='" . htmlspecialchars($customer_id, ENT_QUOTES, 'UTF-8') . "' 
                    class='btn btn-warning btn-sm' 
                    data-toggle='tooltip' 
                    title='Status update' 
                    data-bs-toggle='modal' 
                    data-bs-target='#customer_status'>
                    <i class='bi bi-pen'></i>
                </a>";

            // Build row
            $rows[] = [
                'id' => $customer_id,
                'name' => ucwords($customer['first_name']),
                'email' => htmlspecialchars($customer['email'] ?? '', ENT_QUOTES, 'UTF-8'),
                'mobile' => htmlspecialchars($customer['mobile'] ?? '', ENT_QUOTES, 'UTF-8'),
                'balance' => number_format((float) $customer['balance'], 2),
                'status' => $status,
                'action' => $edit_button
            ];
        }

        // JSON Response
        echo json_encode([
            'total' => $customers['total'] ?? count($rows),
            'rows' => $rows
        ]);
    }

}
