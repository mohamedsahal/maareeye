<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Users_packages_model;
use App\Models\Vendors_model;

class Subscriptions extends BaseController
{
    public $ionAuth;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
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
            $vendors_model = new Vendors_model();
            $data['page'] = FORMS . 'subscription';
            $data['title'] = "Subscriptions - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();

            $vendor_data = $vendors_model->get_vendors();
            $data['vendors'] = $vendor_data['data'];
            $data['packages'] = get_plans_tenures();
            return view("admin/template", $data);
        }
    }

    public function tenures($package_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
        } else {
            $response['error'] = true;
            $response['data'] = [];
            $response['message'] = "No tenures found";

            if (is_numeric($package_id)) {
                $tenure = get_plans_tenures($package_id);
                if (!empty($tenure)) {
                    $tenure = array_column($tenure, "tenures");
                    $response['error'] = false;
                    $response['data'] = (isset($tenure[0])) ? $tenure[0] : [];
                    $response['message'] = "Tenures retrieved successfully";
                }
            } else {
                $tenure = get_plans_tenures();
                if (!empty($tenure)) {
                    $tenure = array_column($tenure, "tenures");
                    $tenure = array_merge(...array_values($tenure));
                    $response['error'] = false;
                    $response['data'] = (isset($tenure[0])) ? $tenure : [];
                    $response['message'] = "Tenures retrieved successfully";
                }
            }
            echo json_encode($response);
        }
    }

    public function add_subscription()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
        } else {
            $users_package_model = new Users_packages_model();
            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'user_identity' => 'required',
                    'user_name' => 'required|min_length[3]|max_length[255]',
                    'package_name' => 'required',
                    'package_tenure' => 'required',
                    'no_of_businesses' => 'required',
                    'no_of_delivery_boys' => 'required',
                    'no_of_products' => 'required',
                    'no_of_customers' => 'required',
                    'no_of_warehouse' => 'required',
                    'price' => 'required',
                    'starts_from' => 'required',
                    'ends_at' => 'required'
                ]);

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

                    $start_date = $this->request->getVar('starts_from');
                    $end_date = $this->request->getVar('ends_at');
                    $date = date('Y-m-d');
                    $status = check_package_status($start_date, $end_date, $date);

                    $user_id = $this->request->getVar('user_identity');
                    $db = \config\Database::connect();
                    $user_data = $db->table('users')->where(['id' => $user_id])->get()->getResultArray();
                    if (!empty($user_data)) {
                        $user_data = $user_data[0];
                    } else {
                        $response = [
                            'error' => true,
                            'message' => ['User not found !'],
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }

                    $user_plans = [];
                    $user_plans = $db->table('users_packages')->where(['user_id' => $user_id])->get()->getResultArray();
                    foreach ($user_plans as $plan) {
                        if ($plan['status']) {
                            $response = [
                                'error' => true,
                                'message' => ['Already user has active plan !'],
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }
                    }

                    $vendor = array(
                        'user_id' => $this->request->getVar('user_identity'),
                        'package_id' => $this->request->getVar('p_id'),
                        'package_name' => $this->request->getVar('package_name'),
                        'no_of_businesses' => $this->request->getVar('no_of_businesses'),
                        'no_of_delivery_boys' => $this->request->getVar('no_of_delivery_boys'),
                        'no_of_products' => $this->request->getVar('no_of_products'),
                        'no_of_customers' => $this->request->getVar('no_of_customers'),
                        'no_of_warehouse' => $this->request->getVar('no_of_warehouse'),
                        'tenure' => $this->request->getVar('tenure_name'),
                        'price' => $this->request->getVar('price'),
                        'months' => $this->request->getVar('months'),
                        'start_date' => $this->request->getVar('starts_from'),
                        'end_date' => $this->request->getVar('ends_at'),
                        'status' => $status
                    );

                    $users_package_model->save($vendor);
                    $response = [
                        'error' => false,
                        'message' => 'Users package added succesfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'Users package added succesfully';
                    $_SESSION['toastMessageType'] = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->to('admin/subscriptions');
            }
        }
    }
    public function subscription_table()
    {
        $users_package_model = new Users_packages_model();
        $users_packages = $users_package_model->findAll();
        $user_id = array_column($users_packages, "user_id");
        $vendors = $users_package_model->get_users_packages($user_id);

        $i = 0;
        $rows = [];
        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d');
        foreach ($vendors as $vendor) {
            $fullname = $vendor['first_name'] . ' ' . $vendor['last_name'];
            $start_date = date("Y-m-d", strtotime($vendor['start_date']));
            $end_date = date("Y-m-d", strtotime($vendor['end_date']));
            $status = check_package_status($start_date, $end_date, $date);
            if ($status == "1") {
                if ($date < $start_date && $start_date < $end_date) {
                    $vendor['status'] = "<span class='badge badge-info' >Upcoming</span>";
                } else {
                    $vendor['status'] = "<span class='badge badge-primary' >Active</span>";
                }
            } else {
                $vendor['status'] = "<span class='badge badge-danger' >Expired</span>";
                $id = $vendor['id'];
                update_details(['status' => $status], ['id' => $id], 'users_packages');
            }
            $rows[$i] = [
                'user_id' => $vendor['user_id'],
                'package_name' => $vendor['package_name'],
                'no_of_customers' => $vendor['no_of_customers'],
                'tenure' => $vendor['tenure'],
                'price' => $vendor['price'],
                'months' => $vendor['months'],
                'start_date' => $vendor['start_date'],
                'end_date' => $vendor['end_date'],
                'no_of_businesses' => ($vendor['no_of_businesses'] == -1) ? "Unlimited" : $vendor['no_of_businesses'],
                'no_of_delivery_boys' => ($vendor['no_of_delivery_boys'] == -1) ? "Unlimited" : $vendor['no_of_delivery_boys'],
                'no_of_products' => ($vendor['no_of_products'] == -1) ? "Unlimited" : $vendor['no_of_products'],
                'no_of_warehouse' => ($vendor['no_of_warehouse'] == -1) ? "Unlimited" : $vendor['no_of_warehouse'],
                'full_name' => $fullname,
                'status' => $vendor['status']
            ];
            $i++;
        }
        if (is_array($users_packages)) {
            $array['total'] = count($users_packages);
        }

        $array['rows'] = $rows;
        echo json_encode($array);
    }
}
