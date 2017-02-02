<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Vendors_model;

class Vendors extends BaseController
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
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
            $data['page'] = VIEWS . 'vendors';
            $data['title'] = "Vendors - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function vendor_table()
    {
        $vendors_model = new Vendors_model();
        $vendors = $vendors_model->get_vendors();

        $rows = [];

        foreach ($vendors['data'] as $vendor) {

            $isActive = (int) $vendor['active'];

            $statusBadge = $isActive
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            $icon_class = $isActive ? "fas fa-user-alt-slash" : "fas fa-user";

            $actionBtn = '<button type="button" class="btn btn-primary text-white changeVendorStatus" 
                data-userid="' . (int) $vendor['id'] . '" 
                data-toggle="tooltip" 
                title="' . ($isActive ? 'Deactivate Vendor' : 'Activate Vendor') . '">
                <i class="' . $icon_class . '"></i>
            </button>';

            $rows[] = [
                'id' => (int) $vendor['id'],
                'first_name' => ucwords($vendor['first_name']),
                'last_name' => ucwords($vendor['last_name']),
                'mobile' => $vendor['mobile'],
                'email' => $vendor['email'],
                'status' => $statusBadge,
                'action' => $actionBtn
            ];
        }

        echo json_encode([
            'total' => $vendors['total'],
            'rows' => $rows
        ]);

    }
    public function create()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
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
            $data['page'] = FORMS . 'create-vendor';
            $data['title'] = "Create Vendor-" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function changeVendorStatus()
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $response = [
                'error' => true,
                'message' => 'UnAuthorizied Action !',
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        } else {
            $rules = [
                'user_id' => 'required|trim',
            ];
            $this->validation->setRules($rules);

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

                $vendor_id = $this->request->getVar('user_id');

                $db = \config\Database::connect();

                $user = $db->table('users')->where(['id' => $vendor_id])->get()->getResultArray();
                if (empty($user)) {
                    $response = [
                        'error' => true,
                        'message' => 'Vendor not found !',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }

                $user_current_status = $user[0]['active'];
                $data = [
                    'active' => !$user_current_status,
                ];
                $check = $db->table('users')->where(['id' => $vendor_id])->update($data);
                if ($check) {
                    $response = [
                        'error' => false,
                        'message' => 'Vendor Status updated Successfully !',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $response = [
                        'error' => true,
                        'message' => 'Unable to update status ',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            }
        }
    }
}
