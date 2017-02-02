<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Categories_model;

class Categories extends BaseController
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
            $data['page'] = FORMS . 'categories';
            $data['title'] = "Categories -" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $category_model = new Categories_model();
            $data['categories'] = $category_model->findAll();
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function save_categories()
    {
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
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
                    $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";
                    if (isset($_POST['status']) && !empty($_POST['status'])) {
                        $status = "1";
                    } else {
                        $status = "0";
                    }
                    $categories = array(
                        'id' => $category_id,
                        'parent_id' => $this->request->getVar('parent_id'),
                        'vendor_id' => $vendor_id,
                        'name' => $this->request->getVar('name'),
                        'status' => $status,
                    );
                    $category_model->save($categories);
                    $response = [
                        'error' => false,
                        'message' => 'Category saved successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->to('admin/categories');
            }
        }
    }
    public function category_table()
    {
        $category_model = new Categories_model();
        $categories = $category_model->get_categories();

        $rows = [];

        foreach ($categories['data'] as $category) {

            $delete_route = base_url('admin/categories/delete-category');

            if ($category['parent_id'] != 0) {
                $parent_category = fetch_details('categories', ['id' => $category['parent_id']], 'name');
                $parent_category = $parent_category[0]['name'];
            } else {
                $parent_category = "-";
            }

            $action = "
                    <div class=\"d-flex gap-4\">
                        <button type='button' class='btn btn-primary btn-sm editCategory' data-toggle='tooltip' data-bs-placement='bottom' title='Edit Category' data-bs-toggle='modal' data-bs-target='#update_cayrgory_modal' data-id='" . $category['id'] . "'> <i class='bi bi-pencil'></i> </button>
                        <button type='button' class='btn btn-danger btn-sm ' data-toggle='tooltip' data-bs-placement='bottom' title='Delete Category' onclick='deleteCategory(" . $category['id'] . ", \"" . $delete_route . "\")'> <i class='bi bi-trash'></i> </button>
                    </div>";
            $statusLabel = $category['status'] == 1
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge badge-danger'>Deactive</span>";

            $rows[] = [
                'id' => $category['id'],
                'active' => $category['status'],
                'vendor_id' => $category['vendor_id'],
                'parent_id' => $category['parent_id'],
                'parent_category' => $parent_category,
                'name' => ucwords($category['name']),
                'status' => $statusLabel,
                'action' => $action
            ];
        }

        echo json_encode([
            'total' => $categories['total'],
            'rows' => $rows
        ]);

    }
    public function edit_category($category_id = "")
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
            $data['page'] = FORMS . "categories";
            $data['title'] = "Edit Category-" . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $category_model = new Categories_model();
            $uri = current_url(true);
            $data['category'] = $category_model->find($uri->getSegment(4));
            $data['categories'] = $category_model->findAll();
            if (!empty($data['category'])) {

                $parent_id = $data['category']['parent_id'];
                $data['parent_category'] = $category_model->find($parent_id);
            }
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function delete()
    {
        $csrf = [
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash()
        ];

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => "Please login!",
                'data' => []
            ], $csrf));
        }

        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'data' => []
            ], $csrf));
        }

        $this->validation->setRules([
            'id' => [
                'rules' => 'required|is_natural_no_zero',
                'label' => 'Category'
            ],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => $this->validation->getErrors(),
                'data' => []
            ], $csrf));
        }

        $id = $this->request->getPost('id');
        $categories_model = new Categories_model();
        $unit = $categories_model->where(['id' => $id])->first();

        if (!$unit) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => ["Category not found!"],
                'data' => []
            ], $csrf));
        }

        // Check if unit is used in products
        $db = \Config\Database::connect();
        $productExists = $db->table('products')
            ->where('category_id', $id)
            ->countAllResults();

        if ($productExists > 0) {
            return $this->response->setJSON(array_merge([
                'error' => true,
                'message' => ["You cannot delete this unit as it's used in one or more products."],
                'data' => []
            ], $csrf));
        }

        // Delete the unit
        $categories_model->delete($id);

        return $this->response->setJSON(array_merge([
            'error' => false,
            'message' => ["Category deleted successfully."],
            'data' => []
        ], $csrf));
    }

    public function get_category()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $response = [
                'error' => true,
                'message' => "Please login !",
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
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


            $this->validation->setRules([
                'id' => [
                    'rules' => 'required',
                    'label' => 'Category'
                ],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $id = $this->request->getPost('id');

                $categories_model = new Categories_model();
                $category = $categories_model->where(['id' => $id])->get()->getResultArray();
                if (empty($category)) {
                    $response = [
                        'error' => true,
                        'message' => ["category not found !"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
                $category = $category[0];
                $response = [
                    'error' => false,
                    'message' => "Success !",
                    'data' => [$category]
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            } else {
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
        }
    }
}
