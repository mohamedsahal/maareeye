<?php

namespace App\Controllers\Vendor;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Tax_model;

class Tax extends BaseController
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
        //   $this->session = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isVendor()) {
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
            $data['page'] = VIEWS . 'tax';
            $data['title'] = "Team-members - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }

    }

    public function taxTable()
    {
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isVendor()) {
            return redirect()->to('login');
        } else {
            if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isVendor()) {
                return redirect()->to('login');
            }

            $tax_model = new Tax_model();
            $taxes = $tax_model->get_taxes();

            // Prepare data rows
            $rows = [];
            foreach ($taxes['data'] as $value) {
                $rows[] = [
                    'id' => $value['id'],
                    'name' => $value['name'],
                    'percentage' => $value['percentage'],
                    'status' => "<span class='" . ($value['status'] ? 'badge badge-success' : 'badge badge-danger') . "'>" . ($value['status'] ? "Active" : "Not Active") . "</span>"
                ];
            }

            // Final response
            return $this->response->setJSON([
                'total' => $taxes['total'],
                'rows' => $rows
            ]);
        }
    }
}
