<?php

namespace App\Controllers\Vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Expenses_model;
use App\Models\Expenses_Type_model;
use App\Models\Products_model;

class Expenses extends BaseController
{
    public $expenses_model;
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
        $this->expenses_model = new Expenses_model();
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
            if (isset($_SESSION['business_id'])) {
                if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                    return redirect()->to("vendor/businesses");
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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "expenses_table";
            $data['title'] = "Expenses - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $expenses_type_model = new Expenses_Type_model();
            $expenses_type_data = $expenses_type_model->get_expenses_type($id);

            $data['expenses_type'] = $expenses_type_data['data'];
            return view("vendor/template", $data);
        }
    }

    public function add()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                return redirect()->to("vendor/businesses");
            } else {
                if (isset($_SESSION['business_id'])) {
                    if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                        return redirect()->to("vendor/businesses");
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
                $settings = get_settings('general', true);
                $company_title = (isset($settings['title'])) ? $settings['title'] : "";
                $data['page'] = FORMS . "expenses";
                $data['title'] = "Add Expenses - " . $company_title;
                $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
                $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
                $vendor_id = $_SESSION['user_id'];
                $id = $_SESSION['user_id'];
                $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                $data['business_id'] = $business_id;
                $data['user'] = $this->ionAuth->user($id)->row();

                $data['expenses_type'] = fetch_details('expenses_type', ['vendor_id' => $vendor_id], ['id', 'title']);
                $expenses = new Expenses_model();
                $expenses_data = $expenses->get_expenses($business_id);
                $data['expenses'] = $expenses_data['data'];
                return view("vendor/template", $data);
            }
        }
    }

    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                $id = $this->ionAuth->getUserId();
                $subscription = check_subscription($id);
                if ($subscription) {


                    $vendor_id = 0;
                    if ($this->ionAuth->isTeamMember()) {
                        $vendor_id = get_vendor_for_teamMember($id);
                    } else {
                        $vendor_id = $id;
                    }
                    $db = \Config\Database::connect();
                    $user_packages = $db->table('users_packages up')->select('up.*')->where(['user_id' => $vendor_id])->get()->getResultArray();

                    $allowed_products = 0;
                    $can_create = true;

                    $products_model = new Products_model();
                    $no_of_products = $products_model->where('vendor_id', $vendor_id)->countAllResults();

                    if (!empty($user_packages)) {

                        foreach ($user_packages as $p) {
                            $status = subscription_status($p['id']);
                            if ($status == 'active') {
                                $allowed_products = $p["no_of_products"];
                                break;
                            }
                        }
                    }

                    if ($status == 'active' && $allowed_products != '-1' && $no_of_products >= $allowed_products) {
                        $can_create = false;
                    }

                    if ($subscription['no_of_products'] == "1") {

                        if (isset($_POST) && !empty($_POST)) {

                            $this->validation->setRules([
                                'expenses_type' => 'required',
                                'expenses_date' => 'required',
                                'amount' => 'required|greater_than[0]',
                                'note' => 'required',
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
                                $vendor_id = $_SESSION['user_id'];
                                if (isset($_SESSION['business_id'])) {
                                    $business_id = $_SESSION['business_id'];
                                } else {
                                    $business_id = "";
                                }
                            }
                            $id = isset($_POST['id']) ? $_POST['id'] : "";
                            $expenses = [
                                'vendor_id' => $vendor_id,
                                'business_id' => $business_id,
                                'id' => $id,
                                'expenses_title' => $this->request->getVar('expenses_title'),
                                'note' => $this->request->getVar('note'),
                                'expenses_id' => $this->request->getVar('expenses_type'),
                                'expenses_date' => $this->request->getVar('expenses_date'),
                                'amount' => $this->request->getVar('amount'),
                            ];

                            $this->expenses_model->save($expenses);

                            $response = [
                                'error' => false,
                                'message' => 'Expense saved successfully',
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }
                    }
                } else {
                    $response = [
                        'error' => true,
                        'message' => ['You have Exceeded limit of adding products'],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return false;
            }
        }
        if ($status == 'upcoming') {
            $response = [
                'error' => true,
                'message' => ['Your subscription has not started yet!'],
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
        if ($status = 'expired') {
            $response = [
                'error' => true,
                'message' => ['Please Buy Subscription to proceed ahead!'],
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }

    public function expenses_table()
    {
        $business_id = $_SESSION['business_id'] ?? '';
        $vendor_id = $_SESSION['user_id'] ?? '';

        $expenses_model = new Expenses_model();
        $expenses = $expenses_model->get_expenses($vendor_id);

        $rows = [];
        foreach ($expenses['data'] as $expense) {
            $edit_url = site_url('vendor/expenses/edit_expenses/' . $expense['id']);
            $rows[] = [
                'id' => $expense['id'],
                'note' => $expense['note'],
                'expenses_id' => $expense['expenses_id'],
                'amount' => currency_location(decimal_points($expense['amount'])),
                'expenses_date' => date_formats(strtotime($expense['expenses_date'])),
                'business_id' => $business_id,
                'vendor_id' => $expense['vendor_id'],
                'action' => "<a href='$edit_url' class='btn btn-primary btn-sm' data-toggle='tooltip' title='Edit'><i class='bi bi-pencil'></i></a>",
                'expenses_type' => $expense['title'],
            ];
        }

        echo json_encode([
            'total' => $expenses['total'] ?? 0,
            'rows' => $rows
        ]);

    }

    public function edit($expenses_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
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
            $data['page'] = FORMS . "expenses";
            $data['title'] = "Edit Expenses - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $vendor_id = $_SESSION['user_id'];
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['user'] = $this->ionAuth->user($id)->row();
            $expenses_type_model = new Expenses_Type_model();
            $data['expenses_type'] = fetch_details('expenses_type', ['vendor_id' => $vendor_id], ['id', 'title']);
            $expenses = new Expenses_model();
            $data['expenses'] = $expenses->find($expenses_id);

            return view("vendor/template", $data);
        }
    }
}
