<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Categories_model;

class Categories extends BaseController
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'categories';
            $data['title'] = "Categories - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['id'] = $id;
            $category_model = new Categories_model();
            $categories_data = $category_model->get_categories($id, $business_id);
            $data['categories'] = $categories_data['data'];
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }

    public function save_categories()
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

                    $business_id = $_SESSION['business_id'];
                    if (isset($_POST['vendor_id']) && $_POST['vendor_id'] == $_SESSION['user_id']) {
                        $category_model = new Categories_model();
                        if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                            $this->validation->setRules([
                                'name' => 'required',
                            ]);
                        } else {
                            $this->validation->setRules([
                                'name' => 'required|is_unique[categories.name]',
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
                            $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : "";
                            $user_id = $_SESSION['user_id'];
                            $id = 0;
                            if ($this->ionAuth->isTeamMember()) {
                                $id = get_vendor_for_teamMember($user_id);
                            } else {
                                $id = $user_id;
                            }
                            $vendor_id = $id;
                            if ($this->ionAuth->isTeamMember()) {
                                if (!empty($category_id)) {
                                    if (!userHasPermission('categories', 'can_update', session('user_id'))) {
                                        $response = [
                                            'error' => true,
                                            'message' => ['You do not have permission for this action'],
                                            'data' => []
                                        ];
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                } else {
                                    if (!userHasPermission('categories', 'can_create', session('user_id'))) {
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
                            if (isset($_POST['status']) && !empty($_POST['status'])) {
                                $status = "1";
                            } else {
                                $status = "0";
                            }
                            $categories = array(
                                'id' => $category_id,
                                'business_id' => $business_id,
                                'parent_id' => $this->request->getVar('parent_id'),
                                'vendor_id' => $vendor_id,
                                'name' => $this->request->getVar('name'),
                                'status' => $status,
                            );
                            $category_model->save($categories);
                            $response = [
                                'error' => false,
                                'message' => 'Category added successfully',
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $_SESSION['toastMessage'] = 'Category added successfully';
                            $_SESSION['toastMessageType'] = 'success';
                            $this->session->markAsFlashdata('toastMessage');
                            $this->session->markAsFlashdata('toastMessageType');
                            return $this->response->setJSON($response);
                        }
                    }
                } else {
                    return redirect()->to('vendor/categories');
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
    public function category_table()
    {
        $category_model = new Categories_model();
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        $user_id = $_SESSION['user_id'];
        $id = 0;
        if ($this->ionAuth->isTeamMember()) {
            $id = get_vendor_for_teamMember($user_id);
        } else {
            $id = $user_id;
        }
        $categories = $category_model->get_categories($id, $business_id);

        $i = 0;
        $rows = [];
        foreach ($categories['data'] as $category) {
            $category_id = $category['id'];
            if ($category['status'] == 1) {
                $status = "<span class='badge badge-primary' >Active</span>";
            } else {
                $status = "<span class='badge badge-danger' >Deactive</span>";
            }
            $edit_category = ($category['vendor_id'] == $id) ? "<a href=" . site_url('vendor/categories/edit_category') . "/" . $category_id . " class='btn btn-primary btn-sm' ><i class='bi bi-pencil'></i></a>" : "";
            $rows[$i] = ['id' => $category['id'], 'vendor_id' => $category['vendor_id'], 'parent_id' => $category['parent_id'], 'name' => ucwords($category['name']), 'status' => $status, 'action' => $edit_category];
            $i++;
        }
        $array['total'] = $categories['total'];
        $array['rows'] = $rows;
        echo json_encode($array);
    }
    public function edit_category($category_id = "")
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
            $data['page'] = FORMS . "categories";
            $data['title'] = "Edit Category - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $data['id'] = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $id = $data['id'];
            $category_model = new Categories_model();
            $data['category'] = $category_model->find($category_id);
            $categories_data = $category_model->get_categories();
            $data['categories'] = $categories_data['data'];

            $parent_id = $data['category']['parent_id'];
            $data['parent_category'] = $category_model->find($parent_id);
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }
}
