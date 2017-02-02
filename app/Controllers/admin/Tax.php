<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Tax_model;

class Tax extends BaseController
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
            $data['page'] = FORMS . 'tax';
            $data['title'] = "Tax -" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function save_tax()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
        } else {
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
            if (isset($_POST) && !empty($_POST)) {
                $tax_model = new Tax_model();
                if (isset($_POST['tax_id']) && !empty($_POST['tax_id'])) {
                    $this->validation->setRules([
                        'name' => 'required',
                        'percentage' => 'required|less_than[100]|greater_than[0]',
                    ]);
                } else {
                    $this->validation->setRules([
                        'name' => 'required|is_unique[tax.name]',
                        'percentage' => 'required|less_than[100]|greater_than[0]'
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
                }
                if (isset($_POST['status']) && !empty($_POST['status'])) {
                    $status = "1";
                } else {
                    $status = "0";
                }
                $tax_id = isset($_POST['tax_id']) ? $_POST['tax_id'] : "";
                $tax = array(
                    'id' => $tax_id,
                    'name' => $this->request->getVar('name'),
                    'percentage' => $this->request->getVar('percentage'),
                    'status' => $status,
                );
                $tax_model->save($tax);

                $response['error'] = false;
                $response['message'] = isset($tax_id) && !empty($tax_id) ? 'Tax updated successfully' : 'Tax added successfully';
                $response['data'] = [];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();


                return $this->response->setJSON($response);
            } else {
                return redirect()->to('admin/tax');
            }
        }
    }

    public function tax_table()
    {
        $tax_model = new Tax_model();
        $taxes = $tax_model->get_taxes();

        $i = 0;
        if (!empty($taxes)) {

            foreach ($taxes['data'] as $tax) {
                $tax_id = $tax['id'];
                if ($tax['status'] == 1) {
                    $status = "<span class='badge badge-primary' >Active</span>";
                } else {
                    $status = "<span class='badge badge-danger' >Deactive</span>";
                }
                $edit_tax = "<a href=" . site_url('admin/tax/edit_tax') . "/" . $tax_id . " class='btn btn-primary btn-sm' ><i class='bi bi-pencil'></i></a>";
                $rows[$i] = ['id' => $tax['id'], 'name' => ucwords($tax['name']), 'percentage' => $tax['percentage'], 'status' => $status, 'action' => $edit_tax];
                $i++;
            }
            $array['total'] = $taxes['total'];
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }

    public function edit_tax($tax_id = "")
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $session = session();

            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "tax";
            $data['title'] = "Edit Tax-" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $tax_model = new Tax_model();
            $data['tax'] = $tax_model->find($tax_id);
            $data['taxes'] = $tax_model->findAll();
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
}
