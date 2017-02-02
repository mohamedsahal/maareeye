<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;


class Home extends BaseController
{

    public $ionAuth ;
    public $validation;
    public $configIonAuth;
    public $session;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'function']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }
    public function index()
    {  
        if (!$this->ionAuth->loggedIn()  || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $db = \Config\Database::connect();
            $company_title = (isset($settings['title'])) ? $settings['title'] : "Maareeye";
            $data['page'] = "index";
            $data['title'] = "Welcome to - ". $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
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
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
          
            $id = $_SESSION['user_id'];
            $group =  get_group('vendors');
            $group_id = $group[0]['group_id'];
            $packages = $db->table('packages')->select('count(id) as total')->get()->getResultArray()[0]['total'];
            $sold_packages = $db->table('users_packages')->select('count(id) as total')->get()->getResultArray()[0]['total'];
            $earning = floatval($db->table('transactions')->select('sum(amount) as total')->get()->getResultArray()[0]['total']);
            $vendor_count = $db->table('users_groups')->select('count(group_id) as total')->where('group_id', $group_id)->get()->getResultArray()[0]['total'];
            $data['earning'] = $earning;
            $data['total_packages'] = $packages;
            $data['sold_packages'] = $sold_packages;
            $data['vendors_count'] = $vendor_count;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function login()
    {
        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['title'] = "Login to -" . $company_title;
        return view("login", $data);
    }
    public function register()
    {
        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['title'] = "Login to -" . $company_title;
        $data['page'] = FORMS . "register";
        $data['title'] = "Register - Register to Subscribers";
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Register - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        return view("admin/pages/forms/register", $data);
    }
    function update_user($data)
    {
        $data = escape_array($data);

        if (isset($data['edit_system_user'])) {

            $user_data = [
                'ip_address' => $this->input->ip_address(),
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'username' => $data['username'],
                'active' => 1
            ];
            if (isset($data['password']) && !empty($data['password'])) {
                $password = $this->ion_auth->hash_password($data['password']);
                $user_data['password'] = $password;
            }
            $permission_data = [
                'role' => $data['role']
            ];
            if ($data['role'] > 0) {
                $permission_data['permissions'] = json_encode($data['permissions']);
            } else {
                $permission_data['permissions'] = NULL;
            }
            $this->db->set($permission_data)->where('user_id', $data['edit_system_user'])->update('user_permissions');
            $this->db->set($user_data)->where('id', $data['edit_system_user'])->update('users');
        } else {

            $password = $this->ion_auth->hash_password($data['password']);

            $user_data = [
                'ip_address' => $this->input->ip_address(),
                'mobile' => $data['mobile'],
                'email' => $data['email'],
                'username' => $data['username'],
                'password' => $password,
                'active' => 1
            ];

            $permission_data = [
                'role' => $data['role']
            ];

            if ($data['role'] > 0) {
                $permission_data['permissions'] = json_encode($data['permissions']);
            } else {
                $permission_data['permissions'] = NULL;
            }

            $this->db->insert('users', $user_data);
            $last_id = $this->db->insert_id();
            $this->db->insert('users_groups', ['user_id' => $last_id, 'group_id' => '1']);
            $permission_data['user_id'] = $last_id;
            $this->db->insert('user_permissions', $permission_data);
        }
    }

    public function fetch_sales()
    {
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {

            $sales[] = array();
            $db = \Config\Database::connect();

            $month_res = $db->table('users_packages')
                ->select('SUM(price) AS total_sale,DATE_FORMAT(created_at,"%b") AS month_name ')
                ->groupBy('year(CURDATE()),MONTH(created_at)')
                ->orderBy('year(CURDATE()),MONTH(created_at)')
                ->get()->getResultArray();

            $month_wise_sales['total_sale'] = array_map('intval', array_column($month_res, 'total_sale'));
            $month_wise_sales['month_name'] = array_column($month_res, 'month_name');

            $sales = $month_wise_sales;
            print_r(json_encode($sales));
        }
    }
    public function fetch_data()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {

            $statistics[] = array();
            $db = \Config\Database::connect();
            $group =  get_group('vendors');
            $group_id = $group[0]['group_id'];
            $sold_packages = $db->table('users_packages')->select('count(id) as total')->get()->getResultArray()[0]['total'];
            $earnings = $db->table('transactions')->select('count(id) as total')->get()->getResultArray()[0]['total'];
            $vendors = $db->table('users_groups')->select('count(group_id) as total')->where('group_id', $group_id)->get()->getResultArray()[0]['total'];
            $count['vendors'] = $vendors;
            $count['sold_packages'] = $sold_packages;
            $count['earnings'] = $earnings;
            $statistics = $count;
            print_r(json_encode($statistics));
        }
    }
}
