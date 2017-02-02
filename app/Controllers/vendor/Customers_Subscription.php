<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Subscription_model;

class Customers_Subscription extends BaseController
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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . 'customers_subscription';
            $data['title'] = "Customers Subscription - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $customers = fetch_details("customers", ['business_id' => $business_id]);
            $data['customers'] = isset($customers) ? $customers : "";
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }

    public function customers_subscription_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $subscription_model = new Subscription_model();
        $customers = $subscription_model->get_customers($business_id);

        $i = 0;
        if (!empty($customers)) {
            foreach ($customers['data'] as $customer) {
                $customer_id = $customer['customer_id'];
                $count_subscription = $subscription_model->count_subscription($customer_id);
                $count = "<span class='badge badge-success'>" . $count_subscription[0]['total'] . "</span>";

                $view = "<a href='javascript:void(0)' data-customer_id=" . $customer_id . " class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='View' data-bs-toggle='modal' data-bs-target='#customers_services'><i class='bi bi-eye'></i></a>";

                $rows[$i] = [
                    'name' => ucwords($customer['name']),
                    'count_subscription' => $count,
                    'action' => $view
                ];

                $i++;
            }
            if (isset($rows) && !empty($rows)) {
                $array['rows'] = $rows;
                $array['total'] = $customers['total'];
            }
            echo json_encode($array);
        }
    }

    public function customers_services_table($customer_id = "")
    {
        $subscription_model = new Subscription_model();
        if (!empty($_GET['customer_id'])) {
            $subscriptions = $subscription_model->get_subscription($_GET['customer_id']);
            $total = $subscription_model->count_subscription($_GET['customer_id']);
            $i = 0;
            if (!empty($subscriptions)) {
                foreach ($subscriptions as $subscription) {
                    $status = check_package_status($subscription['starts_on'], $subscription['ends_on']);
                    if ($status == "1")
                        ; {
                        $status = "<span class='badge badge-primary'>Active</span>";
                    }
                    $action = "<button class='btn btn-icon btn-danger remove-variant-item remove_subscription' data-sub_id = '" . $subscription['sub_id'] . "' name='remove_subscription' onclick ='remove_subscription(this)' ><i class='fas fa-trash'></i></button>";

                    if ($subscription['is_recursive'] == "1") {
                        $renewable = "<span class='badge badge-success'>Renewable</span>";
                    }
                    $rows[$i] = [
                        'service_id' => $subscription['service_id'],
                        'service_name' => ucwords($subscription['service_name']),
                        'price' => currency_location(decimal_points($subscription['price'])),
                        'is_recursive' => $renewable,
                        'recurring_days' => $subscription['recurring_days'],
                        'starts_on' => $subscription['starts_on'],
                        'ends_on' => $subscription['ends_on'],
                        'status' => $status,
                        'action' => $action
                    ];
                    $i++;
                }
                $array['total'] = $total[0]['total'];
                if (isset($rows) && !empty($rows)) {
                    $array['rows'] = $rows;
                }
                echo json_encode($array);
            }
        }
    }

    public function recursive_services_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $subscription_model = new Subscription_model();
        $services = $subscription_model->get_services($business_id);
        $i = 0;
        if (!empty($services)) {
            foreach ($services['data'] as $service) {
                $service_id = $service['service_id'];
                $customers = $subscription_model->get_customers_of_services($service_id, $business_id);
                $count = "<span class='badge badge-success'>" . count($customers) . "</span>";
                $view = "<a href='javascript:void(0)' data-service_id=" . $service_id . " class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='View' data-bs-toggle='modal' data-bs-target='#recursive_services'><i class='bi bi-eye'></i></a>";
                $rows[$i] = [
                    'service_id' => $service['service_id'],
                    'service_name' => ucwords($service['name']),
                    'count' => $count,
                    'action' => $view,
                ];
                $i++;
            }
            if (isset($rows) && !empty($rows)) {
                $array['rows'] = $rows;
                $array['total'] = $services['total'];
            }
            echo json_encode($array);
        }
    }

    public function customers_list_of_services_table()
    {
        $subscription_model = new Subscription_model();
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        if (!empty($_GET['service_id'])) {
            $i = 0;
            $customers = $subscription_model->get_customers_of_services($_GET['service_id'], $business_id);
            foreach ($customers as $customer) {
                $rows[$i] = [
                    'customer_id' => $customer['customer_id'],
                    'name' => ucwords($customer['first_name']),
                ];
                $i++;
            }
            if (isset($rows) && !empty($rows)) {
                $array['rows'] = $rows;
                $array['total'] = count($customers);
            }
            echo json_encode($array);
        }
    }

    public function remove_subscription($sub_id)
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                $subscription_model = new Subscription_model();
                $status = $subscription_model->where("id", $sub_id)->delete();
                if ($status) {
                    $response = [
                        'error' => false,
                        'message' => 'Subscription removed succesfully',
                        'data' => []
                    ];
                } else {
                    $response = [
                        'error' => true,
                        'message' => 'Subscription does not exist...',
                        'data' => []
                    ];
                }
                return $this->response->setJSON($response);
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
}
