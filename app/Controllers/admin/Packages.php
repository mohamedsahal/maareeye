<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Packages_model;
use App\Models\Packages_tenures_model;

class Packages extends BaseController
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
        $this->session       = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn()  || !$this->ionAuth->isAdmin()) {
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
            $data['page'] = VIEWS . "package";
            $data['title'] = "Packages - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "View package - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['currency'] =  $currency;
            $packages = get_plans_tenures();
            $data['packages'] = $packages;
           
            $data['tenure'] = !empty($packages[0]['tenures']) ? $packages[0]['tenures'] : "";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();

            return view("admin/template", $data);
        }
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
            $data['page'] = FORMS . "create-package";
            $data['title'] = "Create Packages - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Create package - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function insert_package()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {

                $package_model = new Packages_model();
                $this->validation->setRules([
                    'title' => 'required|min_length[3]|max_length[255]',
                    'no_of_businesses' => 'required',
                    'description' => 'required|min_length[2]|max_length[1024]',
                    'package_type' => 'required',
                    'tenure' => 'required',
                    'months' => 'required',
                    'price' => 'required'
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
                    if (isset($_POST['status'])) {
                        $status = "1";
                    } else {
                        $status = "0";
                    }
                    $package = array(
                        'title' => $this->request->getVar('title'),
                        'no_of_businesses' => $this->request->getVar('no_of_businesses'),
                        'no_of_delivery_boys' => $this->request->getVar('no_of_delivery_boys'),
                        'no_of_customers' => $this->request->getVar('no_of_customers'),
                        'no_of_products' => $this->request->getVar('no_of_products'),
                        'no_of_warehouse' => $this->request->getVar('no_of_warehouse'),
                        'no_of_brands' => $this->request->getVar('no_of_brands'),
                        'description' => $this->request->getVar('description'),
                        'type' => $this->request->getVar('package_type'),
                        'status' => $status
                    );


                    $package_model->save($package);
                    $package_id = $package_model->getInsertID();
                    if (is_array($_POST['tenure'])) {
                        $tenure_count = count($_POST['tenure']);
                        for ($i = 0; $i < $tenure_count; $i++) {
                            $tenure['package_id'] = $package_id;
                            $tenure['tenure'] = $this->request->getVar('tenure')[$i];
                            $tenure['price'] = $this->request->getVar('price')[$i];
                            $tenure['discounted_price'] = $this->request->getVar('discounted_price')[$i];
                            $tenure['months'] = $this->request->getVar('months')[$i];
                            $package_model->add_package_tenures($tenure);
                        }
                    } else {
                        $tenure['package_id'] = $package_id;
                        $tenure['tenure'] = $this->request->getVar('tenure');
                        $tenure['price'] = $this->request->getVar('price');
                        $tenure['discounted_price'] = $this->request->getVar('discounted_price');
                        $tenure['months'] = $this->request->getVar('months');
                        $package_model->add_package_tenures($tenure);
                    }
                    $response = [
                        'error' => false,
                        'message' => ['Package successfuly added.'],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    redirect()->to('admin/packages')->withCookies();
                    return $this->response->setJSON($response);
                }
            }
            return redirect()->to(base_url('admin/packages/create'));
        }
    }
    public function edit_package($id)
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
            $packages = get_plans_tenures_edit($id);
            $data['packages'] = $packages;
            $data['page'] = FORMS . "edit-package";
            $data['title'] = "Edit Package - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Create package - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['tenure'] = !empty($packages[0]['tenures']) ? $packages[0]['tenures'] : [];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function update_package()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $response = [
                    'error' => true,
                    'message' => DEMO_MODE_ERROR,
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                    'data' => []
                ];
                return $this->response->setJSON($response);
            }
            if (isset($_POST) && !empty($_POST)) {
                $package_model = new Packages_model();
                $this->validation->setRules([
                    'title' => 'required|min_length[3]|max_length[255]',
                    'no_of_businesses' => 'required',
                    'no_of_delivery_boys' => 'required',
                    'no_of_products' => 'required',
                    'no_of_customers' => 'required',
                    'no_of_warehouse' => 'required',
                    'no_of_brands' => 'required',
                    'package_type' => 'required',
                    'description' => 'required|min_length[2]|max_length[1024]',
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
                    if (isset($_POST['status'])) {
                        $status = "1";
                    } else {
                        $status = "0";
                    }
                    $package_id =  $_POST['id'];
                    $package = array(
                        'id' => $package_id,
                        'title' => $this->request->getVar('title'),
                        'no_of_businesses' => $this->request->getVar('no_of_businesses'),
                        'no_of_delivery_boys' => $this->request->getVar('no_of_delivery_boys'),
                        'no_of_customers' => $this->request->getVar('no_of_customers'),
                        'no_of_products' => $this->request->getVar('no_of_products'),
                        'no_of_warehouse' => $this->request->getVar('no_of_warehouse'),
                        'no_of_brands' => $this->request->getVar('no_of_brands'),
                        'description' => $this->request->getVar('description'),
                        'type' => $this->request->getVar('package_type'),
                        'status' => $status
                    );
                    $package_model->save($package);
                    // update tenures-------------------------------
                    if (!empty($_POST['tenure']) && isset($_POST['tenure'])) {
                        $Packages_tenures_model  = new Packages_tenures_model();
                        if (is_array($_POST['tenure'])) {
                            $tenure_count = count($_POST['tenure']);
                            if ($_POST['tenure'][0] != "" && $_POST['months'][0] != "" && $_POST['price'][0] != 0) {
                                $response = [
                                    'error' => true,
                                    'message' => 'One of the tenure can not be blank!',
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $_SESSION['toastMessage'] = 'One of the tenure can not be blank!';
                                $_SESSION['toastMessageType']  = 'error';
                                $this->session->markAsFlashdata('toastMessage');
                                $this->session->markAsFlashdata('toastMessageType');
                                return $this->response->setJSON($response);
                            } else {
                                for ($i = 1; $i < $tenure_count; $i++) {
                                    if (is_array($_POST['tenure_id'])) {
                                        for ($j = 0; $j < $i; $j++) {
                                            $tenures_id = (isset($_POST['tenure_id'][$j])) ? $_POST['tenure_id'][$j] : "";
                                        }
                                    }
                                    $tenure = array(
                                        'id' => $tenures_id,
                                        'package_id' => $package_id,
                                        'tenure' => $this->request->getVar('tenure')[$i],
                                        'price' => $this->request->getVar('price')[$i],
                                        'discounted_price' => $this->request->getVar('discounted_price')[$i],
                                        'months' => $this->request->getVar('months')[$i],
                                    );
                                    $Packages_tenures_model->save($tenure);
                                }
                            }
                        } else {
                            $tenure_id = (isset($_POST['tenure_id'])) ? $_POST['tenure_id'] : "";
                            $tenure = array(
                                'id' => $tenure_id,
                                'package_id' => $package_id,
                                'tenure' => $this->request->getVar('tenure'),
                                'price' => $this->request->getVar('price'),
                                'discounted_price' => $this->request->getVar('discounted_price'),
                                'months' => $this->request->getVar('months'),
                            );
                            $Packages_tenures_model->save($tenure);
                        }
                    }
                    $response = [
                        'error' => false,
                        'message' => ['Package updated succesfully'],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'Package updated succesfully';
                    $_SESSION['toastMessageType']  = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    redirect()->to('admin/packages')->withCookies();
                    return $this->response->setJSON($response);
                }
            }
        }
    }
    public function remove_tenure($tenure_id)
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $tenure_model = new Packages_tenures_model();
            $status = $tenure_model->where("id", $tenure_id)->delete();
            if ($status) {
                $response = [
                    'error' => false,
                    'message' => 'Package tenure removed succesfully',
                    'data' => []
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'tenure does not exist...',
                    'data' => []
                ];
            }
            return $this->response->setJSON($response);
        }
    }

    public function delete_plan()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $response = [
                'error' => true,
                'message' => DEMO_MODE_ERROR,
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
        }
        if ($this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $db = \Config\Database::connect();
            $plan_id =  $_POST['plan_id'];
            $builder = $db->table('packages')->delete(['id' => $plan_id]);
            $builder = $db->table('packages_tenures')->delete(['package_id' => $plan_id]);
            $response = [
                'error' => false,
                'message' => 'Package deleted successfully!',
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
                "id" => $_POST['plan_id']
            ];
            return $this->response->setJSON($response);
        } else {
            return redirect()->back();
        }
    }
}
