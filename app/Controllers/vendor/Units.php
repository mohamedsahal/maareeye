<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Units_model;


class Units extends BaseController
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

            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'units';
            $data['title'] = "Units - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['id'] = $id;
            $units_model = new Units_model();
            $unit_data = $units_model->get_units($id);
            $data['units'] = $unit_data['data'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }
    public function save_unit()
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
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {
                    $units_model = new Units_model();
                    if (!isset($_POST['unit_id']) && empty($_POST['unit_id'])) {
                        $this->validation->setRules([
                            'name' => 'required|is_unique[units.name]',
                            'symbol' => 'required|is_unique[units.symbol]'
                        ]);
                    } else {
                        $this->validation->setRules([
                            'name' => 'required',
                            'symbol' => 'required'
                        ]);
                    }
                    $unit_id = $this->request->getVar('unit_id');

                    if (!empty($unit_id)) {
                        if ($this->ionAuth->isTeamMember()) {

                            if (!userHasPermission('units', 'can_update', session('user_id'))) {
                                $response = [
                                    'error' => true,
                                    'message' => ['You do not have permission for this action'],
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                        }
                    } else {
                        if ($this->ionAuth->isTeamMember()) {

                            if (!userHasPermission('units', 'can_create', session('user_id'))) {
                                $response = [
                                    'error' => true,
                                    'message' => ['You do not have permission for this action'],
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                        }
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
                        $vendor_id = "";
                        if (!empty($_POST['vendor_id']) && isset($_POST['vendor_id'])) {
                            $vendor_id = $_POST['vendor_id'] ? $_POST['vendor_id'] : "";
                        }

                        $units = array(
                            'id' => $this->request->getVar('unit_id'),
                            'vendor_id' => $vendor_id,
                            'parent_id' => $this->request->getVar('parent_id'),
                            'name' => $this->request->getVar('name'),
                            'symbol' => $this->request->getVar('symbol'),
                            'conversion' => $this->request->getVar('conversion'),
                        );
                        $units_model->save($units);
                        $response = [
                            'error' => false,
                            'message' => 'Unit added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Unit added successfully';
                        $_SESSION['toastMessageType'] = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                } else {
                    return redirect()->to('vendor/units');
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
    public function unit_table()
    {
        $units_model = new Units_model();
        $id = $_SESSION['user_id'];

        $units = $units_model->get_units($id);

        $i = 0;
        foreach ($units['data'] as $unit) {
            $unit_id = $unit['id'];
            $edit_unit = ($unit['vendor_id'] == $id) ? "<a href=" . site_url('vendor/units/edit_unit') . "/" . $unit_id . " class='btn btn-primary btn-sm' ><i class='bi bi-pencil'></i></a>" : "";
            $rows[$i] = ['id' => $unit['id'], 'vendor_id' => $unit['vendor_id'], 'parent_id' => $unit['parent_id'], 'name' => ucwords($unit['name']), 'symbol' => $unit['symbol'], 'conversion' => $unit['conversion'], 'action' => $edit_unit];
            $i++;
        }
        $array['total'] = $units['total'];
        $array['rows'] = $rows;
        echo json_encode($array);
    }
    public function edit_unit($unit_id = "")
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
            $data['page'] = FORMS . "units";
            $data['title'] = "Edit Units - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['id'] = $id;
            $units_model = new Units_model();
            $data['unit'] = $units_model->find($unit_id);
            $parent_id = $data['unit']['parent_id'];

            $unit_data = $units_model->get_units($id);
            
            $data['units'] = $unit_data['data'];
            $data['user'] = $this->ionAuth->user($id)->row();
            $data['parent_unit'] = $units_model->find($parent_id);
            return view("vendor/template", $data);
        }
    }
}
