<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Services_model;
use App\Models\Tax_model;
use App\Models\Units_model;


class Services extends BaseController
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
            $data['page'] = VIEWS . "services_table";
            $data['title'] = "Services - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }
    public function Add_service()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                return redirect()->to("vendor/businesses");
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
                $data['page'] = FORMS . "services";
                $data['title'] = "Add Services - " . $company_title;
                $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
                $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
                $id = $_SESSION['user_id'];
                $data['user'] = $this->ionAuth->user($id)->row();
                $units_model = new Units_model();
                $data['units'] = $units_model->get_units_for_forms($id);
                $tax_model = new Tax_model();
                $data['taxes'] = $tax_model->findAll();
                return view("vendor/template", $data);
            }
        }
    }
    public function save_services()
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
                if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                    return redirect()->to("vendor/businesses");
                } else {
                    if (isset($_POST) && !empty($_POST)) {

                        $service_id = $this->request->getVar('service_id');
                        if (empty($service_id)) {
                            if ($this->ionAuth->isTeamMember()) {

                                if (!userHasPermission('services', 'can_create', session('user_id'))) {

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

                                if (!userHasPermission('services', 'can_update', session('user_id'))) {
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

                        $this->validation->setRules([
                            'price' => 'required',
                            'cost_price' => 'required',
                        ]);
                        $this->validation->setRule('name', 'Name', 'trim|required');
                        $this->validation->setRule('description', 'Description', 'trim|required');
                        $this->validation->setRule('price', 'Price', 'trim|required|greater_than_equal_to[0]|numeric', errors: [
                            'numeric' => 'Price must be a number.',
                            'greater_than_equal_to' => 'Price must be a non-negative number.',
                        ]);
                        $this->validation->setRule('cost_price', 'Cost Price', 'trim|required|greater_than_equal_to[0]|numeric', errors: [
                            'numeric' => 'Cost Price must be a number.',
                            'greater_than_equal_to' => 'Cost Price must be a non-negative number.',
                        ]);

                        if (isset($_POST['is_recursive']) && $_POST['is_recursive'] == 'on') {
                            $this->validation->setRule('recurring_days', 'Recurring Days', 'trim|required|greater_than_equal_to[0]|numeric', errors: [
                                'numeric' => 'Recurring Days must be a number.',
                                'greater_than_equal_to' => 'Recurring Days must be a non-negative number.',
                            ]);
                            $this->validation->setRule('recurring_price', 'Recurring Price', 'trim|required|greater_than_equal_to[0]|numeric', errors: [
                                'numeric' => 'Recurring Price must be a number.',
                                'greater_than_equal_to' => 'Recurring Price must be a non-negative number.',
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
                            $service_model = new Services_model();
                            $vendor_id = $_SESSION['user_id'];
                            if ($this->ionAuth->isTeamMember()) {
                                $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                            } else {
                                $vendor_id = $_SESSION['user_id'];
                            }
                            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                            $is_tax_included = isset($_POST['is_tax_inlcuded']) ? "1" : "0";
                            $tax_ids = '[]';

                            if ($is_tax_included === "0") {
                                $tax_ids_input = $this->request->getVar('service_taxes');
                                if ($tax_ids_input) {
                                    $tax_ids_input = json_decode($tax_ids_input);
                                    $tax_ids = [];
                                    if (is_array($tax_ids_input)) {
                                        foreach ($tax_ids_input as $tax) {
                                            $tax_ids[] = $tax->id;
                                        }
                                    }
                                    $tax_ids = json_encode($tax_ids);
                                }
                            }

                            if (isset($_POST['is_recursive'])) {
                                $is_recursive = "1";
                            } else {
                                $is_recursive = "0";
                            }
                            if (isset($_POST['status'])) {
                                $status = "1";
                            } else {
                                $status = "0";
                            }
                            $edit_service_id = isset($_POST['service_id']) ? $_POST['service_id'] : "";
                            $service = array(
                                'id' => $edit_service_id,
                                'vendor_id' => $vendor_id,
                                'business_id' => $business_id,
                                'name' => $this->request->getVar('name'),
                                'description' => $this->request->getVar('description'),
                                'price' => $this->request->getVar('price'),
                                'cost_price' => $this->request->getVar('cost_price'),
                                'tax_ids' => $tax_ids,
                                'unit_id' => $this->request->getVar('unit_id'),
                                'is_tax_included' => $is_tax_included,
                                'is_recursive' => $is_recursive,
                                'recurring_days' => $this->request->getVar('recurring_days'),
                                'recurring_price' => $this->request->getVar('recurring_price'),
                                'image' => $this->request->getVar('service_input_image'),
                                'status' => $status,
                            );

                            $service_model->save($service);
                            $response = [
                                'error' => false,
                                'message' => $edit_service_id ? 'Service updated successfully' : 'Service added successfully',
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $_SESSION['toastMessage'] = 'Service added successfully';
                            $_SESSION['toastMessageType'] = 'success';
                            $this->session->markAsFlashdata('toastMessage');
                            $this->session->markAsFlashdata('toastMessageType');
                            return $this->response->setJSON($response);
                        }
                    } else {
                        return redirect()->back();
                    }
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
    public function service_table()
    {
        $service_model = new Services_model();
        $units_model = new Units_model();
        $tax_model = new Tax_model();

        $business_id = $_SESSION['business_id'] ?? '';
        $business_name = $_SESSION['business_name'] ?? '';

        // Get paginated services with total count
        $services = $service_model->fetch_services($business_id);

        $rows = [];
        $i = 0;
        foreach ($services['data'] as $service) {
            // Get unit name
            $unit = $units_model->find($service['unit_id']);
            $unit_name = $unit['name'] ?? '';

            // Get tax name
            $tax_name = '';
            if (!empty($service['tax_id'])) {
                $tax = $tax_model->find($service['tax_id']);
                $tax_name = $tax['name'] ?? '';
            }

            // Format status
            $status = $service['status'] == 1
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            // Format tax inclusion
            $is_tax_included = $service['is_tax_included'] == 1 ? "Included" : "Excluded";

            // Format recursive
            $is_recursive = $service['is_recursive'] == 1 ? "Yes" : "No";

            // Edit button
            $edit_url = site_url('vendor/services/edit_service/' . $service['id']);
            $edit_product = "<a href='{$edit_url}' class='btn btn-primary btn-sm' data-toggle='tooltip' title='Edit'><i class='bi bi-pencil'></i></a>";

            $rows[$i++] = [
                'id' => $service['id'],
                'vendor_id' => $service['vendor_id'],
                'name' => ucwords($service['name']),
                'description' => $service['description'],
                'price' => currency_location(decimal_points($service['price'])),
                'cost_price' => currency_location(decimal_points($service['cost_price'])),
                'recurring_days' => $service['recurring_days'],
                'recurring_price' => currency_location(decimal_points($service['recurring_price'])),
                'is_recursive' => $is_recursive,
                'is_tax_included' => $is_tax_included,
                'status' => $status,
                'business_name' => $business_name,
                'unit_id' => $unit_name,
                'tax_id' => $tax_name,
                'action' => $edit_product
            ];
        }

        $array = [
            'total' => $services['total'],
            'rows' => $rows
        ];

        echo json_encode($array);
    }

    public function edit_service($service_id = "")
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
            $service_model = new Services_model();
            $uri = current_url(true);
            $services = $service_model->find($uri->getSegment(4));


            $data['services'] = $services;


            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "services";
            $data['title'] = "Edit Services - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            $units_model = new Units_model();
            $data['units'] = $units_model->get_units_for_forms($id);
            if (isset($services['unit_id']) && !empty($services['unit_id'])) {
                $service_unit_row = $units_model->find($services['unit_id']);
                $data['product_unit_name'] = $service_unit_row['name'] ?? '';
            }
            $service_unit_row = [];
            $tax_model = new Tax_model();
            $services_tax_ids = json_decode($services['tax_ids']);
            $services_tax_value = [];

            if (gettype($services_tax_ids) != "array") {
                if ($services_tax_ids != 0) {
                    $tax = $tax_model->find($services_tax_ids);
                    $services_tax_value[] = [
                        'value' => $tax['name'],
                        'id' => $tax['id'],
                    ];
                }
            } else {
                foreach ($services_tax_ids as $tax_id) {
                    $tax = $tax_model->find($tax_id);
                    $services_tax_value[] = [
                        'value' => $tax['name'],
                        'id' => $tax['id'],
                    ];
                }
            }
            $data['services_tax_value'] = json_encode($services_tax_value);

            if (isset($services['tax_id']) && !empty($services['tax_id'])) {
                $tax_name = $tax_model->find($services['tax_id']);
                $data['tax_name'] = $tax_name['name'];
                $data['percentage'] = $tax_name['percentage'];
                $data['taxes'] = $tax_model->findAll();
            }

            return view("vendor/template", $data);
        }
    }
    public function json()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $data = $_GET;

        $data['business_id'] = $business_id;
        $rules = [
            'business_id' => 'required|trim|numeric',
            'search' => 'trim',
        ];
        if ($this->request->getGet('limit')) {
            $rules['limit'] = 'trim|numeric|greater_than_equal_to[1]|less_than[250]';
        }
        if ($this->request->getGet('offset')) {
            $rules['offset'] = 'trim|numeric|greater_than_equal_to[0]';
        }

        $this->validation->setRules($rules);
        if (!$this->validation->run($data)) {
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
            $business_id = $data['business_id'];
            $limit = (!empty($data['limit'])) ? $data['limit'] : 10;
            $offset = (!empty($data['offset'])) ? $data['offset'] : 0;
            $sort = (!empty($data['sort'])) ? $data['sort'] : 'id';
            $order = (!empty($data['order'])) ? $data['order'] : 'DESC';
            $search = (!empty($data['search'])) ? $data['search'] : '';
            $services = fetch_services($business_id, $search, $limit, $offset, $sort, $order);
            $final_product_list = array();
            $temp_arr = $services['services'];
            if (isset($temp_arr) && !empty($temp_arr)) {
                foreach ($temp_arr as $val) {

                    if (file_exists($val['image'])) {
                        $image = base_url($val['image']);
                    } else {
                        $image = base_url('public/backend/assets/img/no-image.jpg');
                    }
                    $val['image'] = $image;

                    $tax_ids = json_decode($val['tax_ids'], true);

                    // Note percentage and percentages are different ;
                    $percentage = 1;
                    $percentages = [];

                    // checking if the tax_ids is array or int
                    if (gettype($tax_ids) != "array") {
                        if ($tax_ids != 0) {
                            $taxes = fetch_details("tax", ['id' => $tax_ids]);
                            $percentage = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                        }
                    } else {
                        // if tax_ids is array then get get percentage;
                        foreach ($tax_ids as $tax) {
                            $taxes = fetch_details("tax", ['id' => $tax]);
                            $per = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                            $percentages[] = $per;
                        }
                    }

                    $percentage = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "0";
                    $is_tax_inlcuded = $val['is_tax_included'];
                    $is_tax_inlcuded = $val['is_tax_included'];
                    if ($is_tax_inlcuded != "1") {

                        $sale_price = $val['price'];
                        $taxable_amount_price = 0;
                        if (!empty($percentages)) {

                            foreach ($percentages as $prec) {

                                $taxable_amount_price += floatval($sale_price) * (floatval($prec) / 100);
                            }
                        } else {
                            $taxable_amount_price = floatval($sale_price) * (floatval($percentage) / 100);
                        }

                        $price = floatval($sale_price) + $taxable_amount_price;
                        $val['price'] = $price;
                    } else {
                        $val;
                    }
                    $final_product_list[] = $val;
                }
            }

            $response['error'] = (!empty($services['services'])) ? false : true;
            $response['message'] = (!empty($services['services'])) ? "Services fetched successfully" : "No service found!";
            $response['total'] = $services['total'];
            $response['data'] = $final_product_list;
            return $this->response->setJSON($response);
        }
    }
}
