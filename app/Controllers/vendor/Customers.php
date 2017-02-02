<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
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
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                // business id is not set 
                $business_model = new Businesses_model();
                $allbusiness = $business_model->select()->where(['user_id' => session('user_id')])->get();
                if (empty($allbusiness)) {
                    session()->setFlashdata('message', 'Please create a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('vendor/businesses');
                } else {
                    session()->setFlashdata('message', 'Please select a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('vendor/businesses');
                }
            }
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'customers';
            $data['title'] = "Customers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $customers = fetch_details("customers", ['business_id' => $business_id]);
            $data['customers'] = isset($customers) ? $customers : "";
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }
    public function save_status()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $response = [
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];

            return $this->response->setJSON($response);
        }
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {
                    $this->validation->setRules([
                        'customer_id' => 'required|trim',
                        'status' => 'required|trim'
                    ]);
                }
                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors = $this->validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {

                    update_details(['status' => $_POST['status']], ['user_id' => $_POST['customer_id']], 'customers');
                    $response = [
                        'error' => false,
                        'message' => 'Customers status updated successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'Customers status updated successfully';
                    $_SESSION['toastMessageType'] = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $_SESSION['toastMessage'] = ['Your subscription has not started yet!'];
                $_SESSION['toastMessageType'] = 'error';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return $this->response->setJSON($response);
            }
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $_SESSION['toastMessage'] = ['Please Buy Subscription to proceed ahead!'];
                $_SESSION['toastMessageType'] = 'error';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return $this->response->setJSON($response);
            }
        }
    }

    public function customers_table()
    {
        $business_id = $_SESSION['business_id'] ?? '';
        $customers_model = new Customers_model();
        $customers = $customers_model->get_customers_details($business_id); // Assuming this uses the combined function

        $rows = [];
        foreach ($customers['data'] as $i => $customer) {
            $customer_id = $customer['user_id'];

            // Status badge
            $status = ($customer['status'] == 1)
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            // Full name
            $name = ucwords($customer['first_name']);

            // Edit action button
            $edit_customer = "<a href='javascript:void(0)' data-id='" . htmlspecialchars($customer_id) . "' class='btn btn-primary btn-sm' data-toggle='tooltip' title='Status update' data-bs-toggle='modal' data-bs-target='#customer_status'>
                        <i class='bi bi-pen'></i>
                      </a>";

            $rows[] = [
                'id' => $customer['user_id'],
                'customer_id' => $customer['id'],
                'name' => $name,
                'email' => $customer['email'],
                'mobile' => $customer['mobile'],
                'balance' => currency_location(decimal_points($customer['balance'])),
                'status' => $status,
                'active' => $customer['status'],
                'address' => $customer['address'],
                'action' => $edit_customer
            ];
        }

        $array = [
            'total' => $customers['total'],
            'rows' => $rows
        ];

        echo json_encode($array);

    }
}
