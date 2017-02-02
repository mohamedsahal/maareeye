<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\BrandModel;
use App\Models\Businesses_model;
use App\Models\Categories_model;
use App\Models\Customers_model;
use App\Models\Customers_transactions_model;
use App\Models\Delivery_boys_model;
use App\Models\Orders_items_model;
use App\Models\Orders_model;
use App\Models\Orders_services_model;
use App\Models\Status_model;
use App\Models\Subscription_model;
use App\Models\Tax_model;
use App\Models\WarehouseModel;
use App\Models\WarehouseProductStockModel;

class Orders extends BaseController
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

            $default_warehouse_id = isset($_SESSION['default_warehouse_id']) ? $_SESSION['default_warehouse_id'] : "";
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "orders";
            $data['title'] = "Create Order - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data['user_id'] = $id;
            $category_model = new Categories_model();
            $categories_data = $category_model->get_categories($id, $business_id);
            $data['categories'] = $categories_data['data'];
            $data['brands'] = (new BrandModel())->where(['business_id' => $business_id])->findAll();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['status'] = isset($status) ? $status : "";
            $customers = fetch_details("customers", ['business_id' => $business_id]);
            $data['customers'] = isset($customers) ? $customers : "";
            $data['business_id'] = $business_id;
            $data['default_warehouse_id'] = $default_warehouse_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();
            $warehouses = fetch_details('warehouses', ['vendor_id' => $id, 'business_id' => $business_id]);
            $data['warehouses'] = isset($warehouses) && !empty($warehouses) ? $warehouses : [];

            return view("vendor/template", $data);
        }
    }
    public function orders()
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "orders_list";
            $orders = fetch_details('orders', ['business_id' => $business_id]);
            if (isset($orders) && !empty($orders)) {
                foreach ($orders as $order) {
                    if (floatval($order['amount_paid']) == floatval($order['final_total'])) {
                        update_details(['payment_status' => 'fully_paid'], ['id' => $order['id']], "orders");
                    }
                    if (floatval($order['amount_paid']) < floatval($order['final_total'])) {
                        update_details(['payment_status' => 'partially_paid'], ['id' => $order['id']], "orders");
                    }
                    if (floatval($order['amount_paid']) == 0.00) {
                        update_details(['payment_status' => 'unpaid'], ['id' => $order['id']], "orders");
                    }
                }
            }
            $data['title'] = "Orders List - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();
            return view("vendor/template", $data);
        }
    }
    public function orders_table()
    {
        $business_id = $_SESSION['business_id'] ?? "";
        $orders_model = new Orders_model();
        $orders = $orders_model->get_delivery_boy_orders_list($business_id);

        $rows = [];
        $customer_model = new Customers_model();

        foreach ($orders['data'] as $order) {
            $order_id = $order['id'];
            $delivery_boy_name = '';

            // Fetch delivery boy name (only first valid match needed)
            $order_items = $order['order_type'] === 'service'
                ? fetch_details('orders_services', ['order_id' => $order_id])
                : fetch_details('orders_items', ['order_id' => $order_id]);

            foreach ($order_items as $item) {
                if (!empty($item['delivery_boy'])) {
                    $delivery_boy_details = $this->ionAuth->user($item['delivery_boy'])->row();
                    $delivery_boy_name = $delivery_boy_details->first_name ?? '';
                    break; // stop at first match
                }
            }

            // Fetch customer details and wallet balance
            $customer_id = $order['customer_id'];
            $customer_data = $customer_model->where('user_id', $customer_id)->first();
            if (!$customer_data) {
                $customer_data = $customer_model->where('id', $customer_id)->first();
            }

            $balance = $customer_data['balance'] ?? 0.00;
            $user_id = $customer_data['user_id'] ?? $customer_id;
            $customer = $this->ionAuth->user($user_id)->row();
            $customer_name = $customer->first_name ?? '';

            // Payment status badge
            $status = match ($order['payment_status']) {
                'fully_paid' => "<span class='badge badge-success'>Fully Paid</span>",
                'partially_paid' => "<span class='badge badge-primary'>Partially Paid</span>",
                'unpaid' => "<span class='badge badge-warning'>Unpaid</span>",
                'cancelled' => "<span class='badge badge-danger'>Cancelled</span>",
                default => "<span class='badge badge-secondary'>Unknown</span>",
            };

            // Action buttons
            $view_order = "
            <a href='" . base_url("vendor/orders/view_orders/$order_id") . "' class='btn btn-warning btn-sm' title='View Orders'><i class='bi bi-eye'></i></a>
            <a href='" . base_url("vendor/invoices/invoice/$order_id") . "' class='btn btn-primary btn-sm' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>
            <a href='" . base_url("vendor/invoices/view_invoice/$order_id") . "' class='btn btn-danger btn-sm' target='_blank' title='Invoice PDF'><i class='bi bi-file-earmark-pdf'></i></a>";

            $rows[] = [
                'id' => $order_id,
                'order_type' => ucfirst($order['order_type']),
                'Order_date' => date_formats(strtotime($order['created_at'])),
                'vendor_id' => $order['vendor_id'],
                'customer_name' => $customer_name,
                'business_id' => $order['business_id'],
                'total' => currency_location(decimal_points($order['total'])),
                'balance' => currency_location(decimal_points($balance)),
                'delivery_charges' => currency_location(decimal_points($order['delivery_charges'])),
                'discount' => currency_location(decimal_points($order['discount'])),
                'final_total' => currency_location(decimal_points($order['final_total'])),
                'payment_status' => $status,
                'message' => $order['message'],
                'payment_method' => $order['payment_method'],
                'delivery_boy' => $delivery_boy_name,
                'action' => $view_order
            ];
        }

        $response = [
            'total' => $orders['total'] ?? count($rows),
            'rows' => $rows
        ];

        echo json_encode($response);
    }
    public function view_orders($order_id = "")
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

            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC');
            $data['version'] = !empty($version) ? $version[0]['version'] : null;

            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');

            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);
            $data['currency'] = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '₹';

            $company_title = isset($settings['title']) ? $settings['title'] : "";
            $data['page'] = VIEWS . "view_order";
            $data['title'] = "Order Details - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";

            $id = $this->ionAuth->getUserId();
            $data['vendor_id'] = $id;
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();

            $order = fetch_details("orders", ["id" => $order_id]);

            // Check if the query returned any results
            if (!empty($order) && isset($order[0]['business_id']) && $order[0]['business_id'] == $business_id) {
                $user_id = $order[0]['customer_id'];
                $customer_id = fetch_details('customers', ['id' => $user_id], ['user_id'])[0]['user_id'];

                $customers = $this->ionAuth->user($customer_id)->row();

                $customer_name = isset($customers->first_name) ? $customers->first_name : " ";
                $customer_mobile = isset($customers->mobile) ? $customers->mobile : "";
                $order[0]['customer_name'] = $customer_name;
                $order[0]['customer_mobile'] = $customer_mobile;
                $customer_model = new Customers_model();
                $customer = $customer_model->get_customer($customer_id);
                $balance = isset($customer[0]['balance']) ? $customer[0]['balance'] : "";
                $order[0]['balance'] = $balance;


                $data['order'] = $order[0];
                $status_model = new Status_model();
                $status = $status_model->get_status($business_id);
                $data['status'] = isset($status) ? $status : "";

                $delivery_boy_model = new Delivery_boys_model();
                $delivery_boys = $delivery_boy_model->delivery_boys($business_id);
                $data['delivery_boys'] = isset($delivery_boys) ? $delivery_boys : "";

                $orders_items = fetch_details("orders_items", ["order_id" => $order_id]);
                if (!empty($orders_items)) {
                    foreach ($orders_items as $key => $item) {
                        $product_id = $item['product_id'];
                        $product = fetch_details("products", ['id' => $product_id]);
                        $product_image = !empty($product) ? $product[0]['image'] : "";
                        $orders_items[$key]['image'] = $product_image;

                        $delivery_boy_id = $item['delivery_boy'];
                        $delivery_boys = fetch_details('users', ['id' => $delivery_boy_id]);
                        if (!empty($delivery_boys)) {
                            $name = $delivery_boys[0]['first_name'];
                            $orders_items[$key]['delivery_boy_name'] = $name;
                        }

                        $status_id = $item['status'];
                        $statuses = fetch_details("status", ['id' => $status_id]);
                        $status_name = !empty($statuses) ? $statuses[0]['status'] : "";
                        $orders_items[$key]['status_name'] = $status_name;
                    }
                    $data['items'] = $orders_items;
                } else {
                    $data['items'] = "";
                }

                $orders_services = fetch_details("orders_services", ["order_id" => $order_id]);
                if (!empty($orders_services)) {
                    foreach ($orders_services as $key => $service) {
                        $service_id = $service['service_id'];
                        $services = fetch_details("services", ['id' => $service_id]);
                        $orders_services[$key]['image'] = !empty($services) ? $services[0]['image'] : "";

                        $delivery_boy_id = $service['delivery_boy'];
                        $delivery_boys = fetch_details('users', ['id' => $delivery_boy_id]);
                        if (!empty($delivery_boys)) {
                            $name = $delivery_boys[0]['first_name'];
                            $orders_services[$key]['delivery_boy_name'] = $name;
                        }

                        $status_id = $service['status'];
                        $statuses = fetch_details("status", ['id' => $status_id]);
                        $status_name = !empty($statuses) ? $statuses[0]['status'] : "";
                        $orders_services[$key]['status_name'] = $status_name;
                    }
                    $data['services'] = $orders_services;
                } else {
                    $data['services'] = "";
                }
            } else {
                $data['items'] = "";
                $data['services'] = "";
                $this->session->setFlashdata('message', 'You don\'t have an order for this business!');
            }
            return view("vendor/template", $data);
        }
    }


    public function save_order()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {
                    if (!isset($_POST['data']) || empty($_POST['data'])) {
                        $response = [
                            'error' => true,
                            'message' => 'Please add order item',
                            'csrf_token' => csrf_token(),
                            'csrf_hash' => csrf_hash(),
                            'data' => []
                        ];
                        return $this->response->setJSON($response);
                    }

                    $amount_paid = '';
                    $order_items = json_decode($_POST['data']);

                    if (!isset($_POST['customer_id']) || empty($_POST['customer_id'])) {
                        $response = [
                            'error' => true,
                            'message' => "Please select the customer!",
                            'csrf_token' => csrf_token(),
                            'csrf_hash' => csrf_hash(),
                            'data' => []
                        ];

                        return $this->response->setJSON($response);
                    }
                    $rules = [
                        'total' => 'required|trim',
                        'final_total' => 'required|trim',
                        'warehouse_id' => 'required|trim',
                        'status' => 'required',
                    ];

                    if (isset($_POST['amount_paid']) && !empty($_POST['amount_paid'])) {
                        $rules = [
                            'amount_paid' => 'trim|greater_than_equal_to[0]',
                        ];
                    }

                    $payment_method = $this->request->getVar('payment_method');
                    if ($this->request->getPost('payment_status') != "unpaid" && $this->request->getPost('payment_status') != "cancelled") {
                        $payment_method = $payment_method;
                        if (isset($_POST['payment_method']) && $_POST['payment_method'] != 'cash' && $_POST['payment_method'] != 'wallet') {
                            $rules['transaction_id'] = 'trim|required';
                        }
                        if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'other') {
                            $rules['payment_method_name'] = 'trim|required';
                        }
                    } else {
                        $payment_method = null;
                    }

                    if ($this->request->getPost('quantity')) {
                        $rules['quantity'] = 'trim|numeric|greater_than_equal_to[0]';
                    }
                    if (isset($_POST['payment_method']) && $_POST['payment_method'] != 'cash') {
                        $rules['transaction_id'] = 'trim|required';
                    }
                    if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'other') {
                        $rules['payment_method_name'] = 'trim|required';
                    }
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
                        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                        $vendor_id = $this->ionAuth->getUserId();
                        $customer_id = $_POST['customer_id'];
                        $payment_type = $payment_method;
                        $payment_status = $this->request->getVar('payment_status');
                        $customer = fetch_details('customers', ['user_id' => $customer_id]);

                        $customer_id = $customer[0]['id'];
                        $customer_wallet_balance = !empty($customer) ? floatval($customer[0]['balance']) : "0";
                        $final_total = floatval($this->request->getVar('final_total'));
                        if ($payment_type == "wallet") {
                            if ($customer_wallet_balance < $final_total) {
                                $response = [
                                    'error' => true,
                                    'message' => 'You dont have sufficient wallet balance,Please recharge wallet!',
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();

                                return $this->response->setJSON($response);
                            }
                        }
                        // save order frome here
                        if ($payment_status == "fully_paid") {
                            $amount_paid = $this->request->getVar('final_total');
                        }
                        if ($payment_status == "partially_paid") {
                            $amount_paid = $this->request->getVar('amount_paid');
                            if ($final_total > $amount_paid) {
                                $amount_paid = $this->request->getVar('amount_paid');
                            } else {
                                $response = [
                                    'error' => true,
                                    'message' => 'Amount is more than order total please check!',
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                        }
                        if ($payment_type == "wallet") {
                            $amount_paid = $this->request->getVar('final_total');
                        }

                        $order = array(
                            'vendor_id' => $vendor_id,
                            'business_id' => $business_id,
                            'customer_id' => $customer_id,
                            'warehouse_id' => $this->request->getVar('warehouse_id'),  // saving null for pos orders as pos orders may have multiple variants with multiple warehouses ;
                            'created_by' => $_SESSION['user_id'],
                            'final_total' => $this->request->getVar('final_total'),
                            'total' => $this->request->getVar('total'),
                            'delivery_charges' => $this->request->getVar('delivery_charges'),
                            'discount' => $this->request->getVar('discount'),
                            'payment_status' => $payment_status,
                            'payment_method' => $this->request->getVar('payment_method'),
                            'order_type' => $this->request->getVar('order_type'),
                            'amount_paid' => $amount_paid,
                            'message' => $this->request->getVar('message'),
                        );

                        $product_variant_id = array_column($order_items, "product_variant_id");
                        $quantity = array_column($order_items, "quantity");
                        $check_current_stock_status = validate_stock($product_variant_id, $quantity);

                        if ($check_current_stock_status['error'] == true) {
                            $response['error'] = true;
                            $response['message'] = [$check_current_stock_status['message']];
                            $response['data'] = array();
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }


                        $order_model = new Orders_model();
                        $order_model->save($order);
                        $order_id = $order_model->getInsertID();
                        $customers_transactions_model = new Customers_transactions_model();
                        if ($payment_type == "wallet") {
                            $transaction = array(
                                'order_id' => $order_id,
                                'customer_id' => $customer_id,
                                'vendor_id' => $vendor_id,
                                'created_by' => $vendor_id,
                                'payment_type' => $payment_type,
                                'transaction_type' => 'credit',
                                'amount' => $amount_paid,
                                'transaction_id' => $this->request->getVar('transaction_id'),

                            );
                            $customers_transactions_model->save($transaction);
                            $balance = $customer_wallet_balance - $final_total;
                            update_details(['balance' => $balance], ['id' => $customer_id], "customers");
                        }
                        if ($payment_type == "cash") {

                            $transaction = array(
                                'order_id' => $order_id,
                                'customer_id' => $customer_id,
                                'vendor_id' => $vendor_id,
                                'created_by' => $vendor_id,
                                'payment_type' => $payment_type,
                                'transaction_type' => 'credit',
                                'amount' => $amount_paid,
                                'transaction_id' => $this->request->getVar('transaction_id'),

                            );
                            $customers_transactions_model->save($transaction);
                        } else if ($payment_type != "cash" && $payment_type != "wallet") {

                            $transaction = array(
                                'order_id' => $order_id,
                                'vendor_id' => $vendor_id,
                                'customer_id' => $customer_id,
                                'created_by' => $vendor_id,
                                'payment_type' => $payment_type,
                                'transaction_type' => 'credit',
                                'amount' => $amount_paid,
                                'transaction_id' => $this->request->getVar('transaction_id'),

                            );
                            $customers_transactions_model->save($transaction);
                        }
                        $tax_model = new Tax_model();
                        $orders_items_model = new Orders_items_model();
                        $orders_services_model = new Orders_services_model();
                        $warehouse_product_stock_model = new WarehouseProductStockModel();
                        foreach ($order_items as $item) {
                            $tax_ids = (isset($item->tax_id) && $item->tax_id != 0) ? $item->tax_id : [];
                            $tax_details = [];

                            if (gettype($tax_ids) == "string") {
                                $tax_ids = (array) $tax_ids;
                                $tax_ids = $tax_ids[0];
                            }
                            foreach ($tax_ids as $tax_id) {
                                $tax = $tax_model->find($tax_id);
                                $tax_details[] = [
                                    'tax_id' => $tax_id,
                                    'name' => $tax['name'],
                                    'percentage' => $tax['percentage']
                                ];
                            }
                            $tax_details = empty($tax_details) ? "[]" : json_encode($tax_details);
                            $sub_total = (floatval($item->price)) * (floatval($item->quantity));
                            if (isset($item->product_id) && !empty($item->product_id)) {
                                $orders_items = array(
                                    'order_id' => $order_id,
                                    'product_id' => $item->product_id,
                                    'product_variant_id' => $item->product_variant_id,
                                    'product_name' => $item->variant_name,
                                    'quantity' => $item->quantity,
                                    'price' => $item->price,
                                    'tax_name' => '',
                                    'tax_percentage' => '',
                                    'is_tax_included' => $item->is_tax_included,
                                    'tax_details' => $tax_details,
                                    'sub_total' => $sub_total,
                                    'status' => $this->request->getVar('status'),
                                );
                                $orders_items_model->save($orders_items);
                                $order_item_id = $orders_items_model->getInsertID();

                                update_stock($item->product_variant_id, $item->quantity);
                                $warehouse_stock = $warehouse_product_stock_model->where('product_variant_id', $item->product_variant_id)->get()->getResultArray();
                                $warehouse_item_max_stock = $warehouse_stock[0];
                                $max_stock = $warehouse_stock[0]['stock'];

                                foreach ($warehouse_stock as $warehouse_stock_item) {
                                    if ($max_stock < $warehouse_stock_item['stock']) {
                                        $max_stock = $warehouse_stock_item['stock'];
                                        $warehouse_item_max_stock = $warehouse_stock_item;
                                    }
                                }

                                updateWarehouseStocks($this->request->getVar('warehouse_id'), product_variant_id: $warehouse_item_max_stock['product_variant_id'], warehouse_stock: $item->quantity, type: 0);
                            }
                            if (isset($item->service_id) && !empty($item->service_id)) {
                                $recurring_days = find_days($item->starts_on, $item->ends_on);
                                $orders_items = array(
                                    'order_id' => $order_id,
                                    'service_id' => $item->service_id,
                                    'service_name' => $item->service_name,
                                    'price' => $item->price,
                                    'quantity' => $item->quantity,
                                    'unit_name' => $item->unit_name,
                                    'unit_id' => $item->unit_id,
                                    'tax_percentage' => '',
                                    'is_tax_included' => $item->is_tax_included,
                                    'tax_name' => '',
                                    'tax_details' => $tax_details,
                                    'is_recursive' => $item->is_recursive,
                                    'recurring_days' => $recurring_days,
                                    'starts_on' => $item->starts_on,
                                    'ends_on' => $item->ends_on,
                                    'sub_total' => $sub_total,
                                    'status' => $this->request->getVar('status'),
                                );
                                $orders_services_model->save($orders_items);
                            }
                            $subscription_model = new Subscription_model();
                            if (isset($item->is_recursive) && $item->is_recursive == "1") {

                                $data = $subscription_model->if_exist($item->service_id, $_POST['customer_id']);
                                $sub_id = isset($data[0]['id']) && !empty($data[0]['id']) ? $data[0]['id'] : "";
                                $subscription = array(
                                    'id' => $sub_id,
                                    'service_id' => $item->service_id,
                                    'vendor_id' => $vendor_id,
                                    'business_id' => $business_id,
                                    'customer_id' => $customer_id,
                                    'created_by' => $vendor_id,
                                );
                                $subscription_model->save($subscription);
                            }
                        }

                        $response = [
                            'error' => false,
                            'message' => 'order placed successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [
                            'order_id' => $order_id
                        ];
                        return $this->response->setJSON($response);
                    }
                } else {
                    return redirect()->to('vendor/orders');
                }
            }
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                    'data' => []
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
                    'data' => []
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
    public function create_status()
    {

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {

                    $this->validation->setRules([
                        'status' => 'required|trim',
                        'operation' => 'required',
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
                        $vendor_id = $this->ionAuth->getUserId();
                        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                        $status = array(
                            'vendor_id' => $vendor_id,
                            'business_id' => $business_id,
                            'status' => $this->request->getVar('status'),
                            'operation' => $this->request->getVar('operation')
                        );
                        $status_model = new Status_model();
                        $status_model->save($status);
                        $response = [
                            'error' => false,
                            'message' => 'Status Created successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                } else {
                    return redirect()->back()->withInput();
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
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }

    public function set_delivery_boy()
    {
        $deliveryboy = $this->request->getGet('deliveryboy');
        $order_id = $this->request->getGet('order_id');
        $type = $this->request->getGet('type');
        if ($this->request->getGet('delivery_boy')) {
            $rules['delivery_boy'] = 'trim';
        }
        if ($this->request->getGet('order_id')) {
            $rules['order_id'] = 'trim';
        }
        if ($this->request->getGet('type')) {
            $rules['type'] = 'trim';
        }

        $this->validation->setRules($rules);
        if (!$this->validation->run()) {
            $errors = $this->validation->getErrors();
            $response = [
                'error' => true,
                'message' => $errors,
                'data' => []
            ];
            return $this->response->setJSON($response);
        } else {
            if ($type == "product") {
                update_details(['delivery_boy' => $deliveryboy], ['id' => $order_id], 'orders_items');
                $response = [
                    'error' => false,
                    'message' => "DeliveryBoy Assigned successfully!",
                ];
                return $this->response->setJSON($response);
            }
            if ($type == "service") {
                update_details(['delivery_boy' => $deliveryboy], ['id' => $order_id], 'orders_services');
                $response = [
                    'error' => false,
                    'message' => "DeliveryBoy Assigned successfully!",
                ];
                return $this->response->setJSON($response);
            }
        }
    }

    public function update_order_status()
    {
        $status = subscription();
        if ($status == 'active') {
            $status = $this->request->getGet('status');
            $order_id = $this->request->getGet('order_id');
            $type = $this->request->getGet('type');
            if ($this->request->getGet('status')) {
                $rules['status'] = 'required';
            }
            if ($this->request->getGet('order_id')) {
                $rules['order_id'] = 'numeric';
            }
            if ($this->request->getGet('type')) {
                $rules['type'] = 'required';
            }

            $this->validation->setRules($rules);
            if (!$this->validation->run($_GET)) {
                $errors = $this->validation->getErrors();
                $response = [
                    'error' => true,
                    'message' => $errors,
                    'data' => []
                ];
                return $this->response->setJSON($response);
            } else {
                if ($type == "product") {
                    update_details(['status' => $status], ['id' => $order_id], 'orders_items');
                    $response = [
                        'error' => false,
                        'message' => "Order status updated successfully!",
                    ];
                    return $this->response->setJSON($response);
                }
                if ($type == "service") {
                    update_details(['status' => $status], ['id' => $order_id], 'orders_services');
                    $response = [
                        'error' => false,
                        'message' => "Order status updated successfully!",
                    ];
                    return $this->response->setJSON($response);
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

    public function customer_balance()
    {
        $user_id = $this->request->getGet('user_id');
        $customer = fetch_details("customers", ['user_id' => $user_id]);
        $balance = isset($customer) ? $customer[0]['balance'] : "0";
        $response = [
            'error' => false,
            'balance' => $balance
        ];
        return $this->response->setJSON($response);
    }
    public function get_users()
    {
        $customer_model = new Customers_model();
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $response = $customer_model->get_users($search, $business_id);
            echo $response;
        }
    }
    protected $validationListTemplate = 'list';
    protected $ionAuthModel;
    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            $id = $this->ionAuth->getUserId();

            if (isset($_POST) && !empty($_POST)) {
                $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {

                    if ($this->ionAuth->isTeamMember()) {
                        if (!userHasPermission('customers', 'can_create', session('user_id'))) {
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

                    if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                        // business id is not set 
                        $business_model = new Businesses_model();
                        $allbusiness = $business_model->findAll();
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

                    $customers_model = new Customers_model();
                    $user_id = $_POST['user_id'];
                    $user = $this->ionAuth->user($user_id)->row();


                    $this->validation->setRule('first_name', lang('Auth.edit_user_validation_fname_label'), 'required');
                    $this->validation->setRule('identity', "Mobile", 'required');
                    $this->validation->setRule('email', lang('Auth.edit_user_validation_email_label'), 'required');
                    $this->validation->setRule('business_id', 'business', 'required');

                    if ($this->request->getPost('password')) {
                        $this->validation->setRule('password', lang('Auth.edit_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');
                    }

                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

                        $data = [
                            'first_name' => $this->request->getPost('first_name'),
                            'email' => $this->request->getPost('email'),
                            'identity' => $this->request->getPost('identity'),
                            'address' => $this->request->getPost('address'),
                        ];
                        // update the password if it was posted
                        if ($this->request->getPost('password')) {
                            $data['password'] = $this->request->getPost('password');
                        }
                        if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == "on") {
                            $status = "1";
                        } else {
                            $status = "0";
                        }
                        if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {
                            $customers = [
                                'vendor_id' => $_SESSION['user_id'],
                                'business_id' => $business_id,
                                'user_id' => $user_id,
                                'status' => $status,
                                'created_by' => $_SESSION['user_id'],
                            ];

                            $customers_model->update($_POST['customer_id'], $customers);
                        }
                        $this->ionAuth->update($user->id, $data);
                        $response = [
                            'error' => false,
                            'message' => 'Customer updated successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                } else {

                    if ($this->ionAuth->isTeamMember()) {
                        if (!userHasPermission('customers', 'can_create', session('user_id'))) {
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

                    $tables = $this->configIonAuth->tables;
                    $identityColumn = $this->configIonAuth->identity;
                    $this->data['identity_column'] = $identityColumn;

                    $this->validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'trim|required');
                    $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');


                    $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'required|trim|valid_email|is_unique[' . $tables['users'] . '.email]');

                    // $this->validation->setRule('phone', lang('Auth.create_user_validation_phone_label'), 'trim');
                    $this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');
                    $this->validation->setRule('business_id', 'business', 'required');

                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

                        $email = strtolower($this->request->getPost('email'));
                        $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
                        $password = $this->request->getPost('password');
                        $group_id_arry = fetch_details("groups", ['name' => 'customers'], "id");
                        $group_id = [$group_id_arry[0]['id']];
                        $additionalData = [
                            'first_name' => $this->request->getPost('first_name'),
                            'address' => $this->request->getPost('address'),
                            // 'phone'      => $this->request->getPost('phone'),
                        ];
                    }
                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                        $customers_model = new Customers_model();
                        $insert_id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);
                        if (!$insert_id) {
                            $errors = $this->ionAuth->errors(); // This is now plain text
                            $response['error'] = true;

                            // Customize your message here
                            if (strpos($errors, 'Identity Already Used') !== false) {
                                $response['message'] = 'Mobile number or email that is already registered.';
                            } elseif (strpos($errors, 'Unable to Create Account') !== false) {
                                $response['message'] = 'There was a problem creating the account. Please try again.';
                            } else {
                                $response['message'] = 'Registration failed: ' . $errors;
                            }

                            return $this->response->setJSON($response);
                        }
                        if (isset($_POST['status']) && !empty($_POST['status'])) {
                            $status = "1";
                        } else {
                            $status = "0";
                        }

                        if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {

                            $customers = [
                                'vendor_id' => $_SESSION['user_id'],
                                'business_id' => $business_id,
                                'user_id' => $insert_id,
                                'status' => $status,
                                'created_by' => $_SESSION['user_id'],

                            ];
                            $customers_model->save($customers);
                        }

                        // check to see if we are creating the user
                        // redirect them back to the admin page  
                        $response = [
                            'error' => false,
                            'message' => 'Customer added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }
                }
                $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
                $response['error'] = true;
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['message'] = $this->validation->getErrors();
                return $this->response->setJSON($response);
            } else {
                return redirect()->back()->withInput();
            }
        }
    }

    public function register()
    {

        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
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
                $tables = $this->configIonAuth->tables;
                $identityColumn = $this->configIonAuth->identity;
                $this->data['identity_column'] = $identityColumn;

                $this->validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'required');
                $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');
                if (!empty($_POST['email'])) {

                    $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'valid_email|is_unique[' . $tables['users'] . '.email]');
                }
                $this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');


                if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                    $email = strtolower($this->request->getPost('email'));
                    $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
                    $password = $this->request->getPost('password');
                    $group_id_arry = fetch_details("groups", ['name' => 'customers'], "id");
                    $group_id = [$group_id_arry[0]['id']];
                    $additionalData = [
                        'first_name' => $this->request->getPost('first_name'),
                        'phone' => $this->request->getPost('phone'),
                        'address' => $this->request->getPost('address'),
                    ];
                }
                if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                    $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);
                    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                    $balance = isset($_POST['balance']) ? $_POST['balance'] : "";
                    $status = isset($_POST['status']) ? $_POST['status'] : "1";
                    $customer = [
                        'user_id' => $id,
                        'business_id' => $business_id,
                        'created_by' => $_SESSION['user_id'],
                        'vendor_id' => $_SESSION['user_id'],
                        'balance' => $balance,
                        'status' => $status
                    ];
                    $customer_model = new Customers_model();
                    $customer_model->save($customer);
                    // check to see if we are creating the user
                    // redirect them back to the admin page  
                    $response = [
                        'error' => false,
                        'message' => 'Customer added successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();

                    return $this->response->setJSON($response);
                } else {
                    // display the create user form
                    // set the flash data error message if there is one

                    $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
                    $response['error'] = true;
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['message'] = $this->validation->getErrors();
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->back()->withInput();
            }
        }
    }
    public function sales_orders()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }
        $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id'])) ? trim($_SESSION['business_id']) : "";

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
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $settings = get_settings('general', true);
        $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['page'] = FORMS . "sales_orders";
        $data['title'] = "Create Order - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $this->ionAuth->getUserId();
        $data['user_id'] = $id;
        $category_model = new Categories_model();
        $categories_data = $category_model->get_categories($id, $business_id);
        $data['categories'] = $categories_data['data'];
        $status_model = new Status_model();
        $status = $status_model->get_status($business_id);
        $data['status'] = isset($status) ? $status : "";
        $customers = fetch_details("customers", ['business_id' => $business_id]);
        $data['customers'] = isset($customers) ? $customers : "";
        $data['business_id'] = $business_id;
        $data['user'] = $this->ionAuth->user($id)->row();
        $warehouse_model = new WarehouseModel();
        $data['warehouses'] = $warehouse_model->where('business_id', $business_id)->get()->getResultArray();

        $this->data['users'] = $this->ionAuth->users()->result();
        return view("vendor/template", $data);
    }

    public function save_sales_orders()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'total' => [
                        'rules' => 'required',
                        'label' => 'Total'
                    ],
                    'payment_status' => [
                        'rules' => 'required|trim',
                        'label' => 'Payment Status'
                    ],
                    'amount_paid' => 'trim',
                    'status' => [
                        'rules' => 'trim',
                        'label' => 'Status'
                    ],
                    'warehouse_id' => [
                        'rules' => 'required',
                        'label' => 'Warehouse'
                    ]
                ]);
                if ($this->request->getVar('payment_status') == "partially_paid" || $this->request->getVar('payment_status') == "fully_paid") {
                    $this->validation->setRules([
                        'sales_date' => [
                            'rules' => 'required',
                            'label' => 'Sales Date'
                        ],
                        'customer_id' => [
                            'rules' => 'required',
                            'label' => 'Customer'
                        ],
                        'products' => [
                            'rules' => 'required',
                            'label' => 'Products'
                        ],
                        'status' => [
                            'rules' => 'required',
                            'label' => 'Status'
                        ],
                        'payment_method' => [
                            'rules' => 'required',
                            'label' => 'Payment Method'
                        ],
                        'warehouse_id' => [
                            'rules' => 'required',
                            'label' => 'Warehouse'
                        ]
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
                    $customer_id = $this->request->getVar('customer_id');
                    $customer = fetch_details('customers', ['user_id' => $customer_id]);
                    $customer_id = $customer[0]['id'];

                    $quantity = $_POST['qty'];
                    $price = $_POST['price'];
                    for ($i = 0; $i < count($price); $i++) {
                        $sub_total[$i] = (int) $quantity[$i] * (int) $price[$i];
                    }
                    $total = (array_sum($sub_total));
                    $payment_status = $this->request->getVar('payment_status');
                    $delivery_charges = $this->request->getVar('delivery_charge');
                    $discouunt = (!empty($this->request->getVar('order_discount')) ? $this->request->getVar('order_discount') : 0);
                    $customer_wallet_balance = !empty($customer) ? floatval($customer[0]['balance']) : "0";
                    // check if the selected products_variants are in selected warehouse or not.
                    $warehouse_id = $this->request->getVar('warehouse_id');
                    $products = json_decode($_POST['sale_product_id']);

                    $warehouse_product_stock = new WarehouseProductStockModel();

                    foreach ($products as $item) {

                        $warehouse_product_list = $warehouse_product_stock->where([
                            'warehouse_id' => $warehouse_id,
                            'product_variant_id' => $item->variant_id
                        ])->get()->getResultArray();

                        if (empty($warehouse_product_list)) {
                            $response = [
                                'error' => true,
                                'message' => ["" . ucfirst($item->name) . " - $item->variant_name is not available in selected warehouse !"],
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }
                    }

                    // $total = ;
                    $final_total = $this->request->getVar('total');
                    $payment_type = $this->request->getVar('payment_method[0]');
                    $amount_paid = '';

                    if ($payment_status == "fully_paid") {
                        $amount_paid = $final_total;
                    }
                    if ($payment_status == "partially_paid") {
                        $amount_paid = $this->request->getVar('amount_paid');
                        if ($final_total > $amount_paid) {
                            $amount_paid = $this->request->getVar('amount_paid');
                        } else {
                            $response = [
                                'error' => true,
                                'message' => 'Amount is more than order total please check!',
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }
                    }
                    if ($payment_type == "wallet") {
                        $amount_paid = $final_total;
                    }
                    $orders_model = new Orders_model();
                    $vendor_id = $_SESSION['user_id'];
                    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                    $order = array(
                        'vendor_id' => $vendor_id,
                        'created_by' => $vendor_id,
                        'business_id' => $business_id,
                        'customer_id' => $customer_id,
                        'warehouse_id' => $warehouse_id,
                        'order_no' => $this->request->getVar('order_no'),
                        'order_type' => $this->request->getVar('order_type'),
                        'sales_date' => $this->request->getVar('sales_date'),
                        'payment_method' => $payment_type,
                        'tax_id' => $this->request->getVar('tax_id'),
                        'discount' => $discouunt,
                        'delivery_charges' => $delivery_charges,
                        'payment_status' => $payment_status,
                        'amount_paid' => $amount_paid,
                        'final_total' => $final_total,
                        'total' => $total,
                        'message' => $this->request->getVar('message'),


                    );

                    $orders_model->save($order);
                    $orders_id = $orders_model->getInsertID();
                    if ($payment_type == "wallet") {
                        $customers_transactions_model = new Customers_transactions_model();
                        $transaction = array(
                            'order_id' => $orders_id,
                            'customer_id' => $customer_id,
                            'vendor_id' => $vendor_id,
                            'created_by' => $vendor_id,
                            'payment_type' => $payment_type,
                            'amount' => $amount_paid,
                            'transaction_id' => $this->request->getVar('transaction_id'),

                        );
                        $customers_transactions_model->save($transaction);
                        $balance = $customer_wallet_balance - $final_total;
                        update_details(['balance' => $balance], ['user_id' => $customer_id], "customers");
                    }
                    if ($payment_type == "cash" && $payment_status == "partially_paid") {
                        $customers_transactions_model = new Customers_transactions_model();
                        $transaction = array(
                            'order_id' => $orders_id,
                            'customer_id' => $customer_id,
                            'created_by' => $vendor_id,
                            'payment_type' => $payment_type,
                            'amount' => $amount_paid,
                            'transaction_id' => $this->request->getVar('transaction_id'),

                        );
                        $customers_transactions_model->save($transaction);
                    }
                    if ($payment_type != "cash" && $payment_type != "wallet") {
                        $customers_transactions_model = new Customers_transactions_model();
                        $transaction = array(
                            'order_id' => $orders_id,
                            'customer_id' => $customer_id,
                            'created_by' => $vendor_id,
                            'payment_type' => $payment_type,
                            'amount' => $amount_paid,
                            'transaction_id' => $this->request->getVar('transaction_id'),

                        );
                        $customers_transactions_model->save($transaction);
                    }

                    $products = json_decode($_POST['sale_product_id']);
                    $sales_data = json_decode($_POST['sale_product_id'], true);
                    $tax_model = new Tax_model();
                    $count = count($products);
                    for ($i = 0; $i < $count; $i++) {
                        $tax = fetch_details('products', ['id' => $sales_data[$i]['product_id']], ['tax_ids', 'is_tax_included']);

                        $tax_details = fetch_details('tax', ['id' => $tax[0]['tax_ids']]);
                        $products[($count - 1) - $i]->qty = $_POST['qty'][$i];
                        $products[($count - 1) - $i]->product_id = $sales_data[$i]['product_id'];
                        $products[($count - 1) - $i]->product_name = $sales_data[$i]['name'];
                        $products[($count - 1) - $i]->tax_id = $tax[0]['tax_ids'];
                        $products[($count - 1) - $i]->is_tax_included = $tax[0]['is_tax_included'];
                        $products[($count - 1) - $i]->tax_name = (!empty($tax_details) ? $tax_details[0]['name'] : '');
                        $products[($count - 1) - $i]->tax_percentage = (!empty($tax_details) ? $tax_details[0]['percentage'] : '');
                        $products[($count - 1) - $i]->discount = isset($_POST['discount']) ? '0' : $_POST['discount'][$i];
                    }
                    foreach ($products as $item) {
                        $tax_ids = (isset($item->tax_id) && $item->tax_id != 0) ? json_decode($item->tax_id) : [];
                        $tax_details = [];

                        foreach ($tax_ids as $tax_id) {
                            $tax = $tax_model->find($tax_id);
                            $tax_details[] = [
                                'tax_id' => $tax_id,
                                'name' => $tax['name'],
                                'percentage' => $tax['percentage']
                            ];
                        }
                        $tax_details = empty($tax_details) ? "[]" : json_encode($tax_details);


                        $orders_items = array(
                            'product_id' => $item->product_id,
                            'product_name' => $item->product_name,
                            'tax_name' => $item->tax_name,
                            'tax_percentage' => '',
                            'tax_id' => '',
                            'is_tax_included' => $item->is_tax_included,
                            'tax_details' => $tax_details,
                            'order_id' => $orders_id,
                            'product_variant_id' => $item->variant_id,
                            'quantity' => $item->qty,
                            'price' => $item->price,
                            'sub_total' => $item->qty * $item->price,
                            'discount' => $item->discount == null ? 0 : $item->discount,
                            'status' => $this->request->getVar('status'),
                            // 'total' => ($item->qty * $item->price) + ()
                        );

                        $orders_items_model = new Orders_items_model();
                        $orders_items_model->save($orders_items);
                        update_stock(product_variant_ids: $item->variant_id, qtns: $item->qty);
                        updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $item->variant_id, warehouse_stock: $item->qty, type: 0);
                    }
                    $response = [
                        'error' => false,
                        'message' => ' Order saved successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->back();
            }
        }
    }
    public function payment_reminder()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }
        $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id'])) ? trim($_SESSION['business_id']) : "";

        if (empty($business_id) || check_data_in_table('businesses', $_SESSION['business_id'])) {
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "payment_reminder";
            $data['title'] = "Payment Reminder - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data['user_id'] = $id;
            $category_model = new Categories_model();
            $categories_data = $category_model->get_categories($id, $business_id);
            $data['categories'] = $categories_data['data'];
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['status'] = isset($status) ? $status : "";
            $customers = fetch_details("customers", ['business_id' => $business_id]);
            $data['customers'] = isset($customers) ? $customers : "";
            $data['business_id'] = $business_id;

            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();
            return view("vendor/template", $data);
        }
    }
    public function payment_reminder_table()
    {
        $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id'])) ? trim($_SESSION['business_id']) : "";
        $orders_model = new Orders_model();
        $orders = $orders_model->payment_reminder($business_id);

        $total = count($orders);

        $i = 0;
        $rows = [];
        foreach ($orders as $order) {

            $order_id = $order['id'];
            $delivery_boy_name = "";
            if ($order['order_type'] == "service") {
                $order_items = fetch_details('orders_services', ['order_id' => $order_id]);
                foreach ($order_items as $item) {
                    $delivery_boy_id = $item['delivery_boy'];
                    if ($delivery_boy_id != null && !empty($delivery_boy_id)) {
                        $delivery_boy_details = $this->ionAuth->user($delivery_boy_id)->row();
                        $delivery_boy_name = $delivery_boy_details->first_name;
                    } else {
                        $delivery_boy_name = "";
                    }
                }
            }
            if ($order['order_type'] == "product") {
                $order_items = fetch_details('orders_items', ['order_id' => $order_id]);
                foreach ($order_items as $item) {
                    $delivery_boy_id = $item['delivery_boy'];
                    if ($delivery_boy_id != null && !empty($delivery_boy_id)) {
                        $delivery_boy_details = $this->ionAuth->user($delivery_boy_id)->row();
                        $delivery_boy_name = $delivery_boy_details->first_name;
                    } else {
                        $delivery_boy_name = "";
                    }
                }
            }

            $currency = get_settings('general', true);
            $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
            $customer_id = $order['customer_id'];
            $customer_model = new Customers_model();
            $customer_wallet = $customer_model->get_customer($customer_id);
            $balance = 0.00;
            foreach ($customer_wallet as $customer) {
                $balance = $customer['balance'];
            }
            $customers = $this->ionAuth->user($customer_id)->row();
            $customer_name = isset($customers->first_name) ? $customers->first_name : " ";
            if ($order['payment_status'] == "fully_paid") {
                $status = "<span class='badge badge-success'>Fully Paid</span>";
            }
            if ($order['payment_status'] == "partially_paid") {
                $status = "<span class='badge badge-primary'>Partially Paid</span>";
            }
            if ($order['payment_status'] == "unpaid") {
                $status = "<span class='badge badge-warning'>Unpaid</span>";
            }
            if ($order['payment_status'] == "cancelled") {
                $status = "<span class='badge badge-danger'>Cancelled</span>";
            }

            $view_order = '<button type = "button" class="btn btn-info btn-sm payment_reminder_button" data-id = "' . $order['id'] . '"  title="Send Remainder" onclick = "payment_reminder(' . $order['id'] . ')"   ><i class="bi bi-bell"></i></button>';
            // $view_order .= " <a href='" . base_url("vendor/invoices/invoice") . "/" . $order_id . "' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>";
            // $view_order .= " <a href='" . base_url('vendor/invoices/view_invoice/' . $order_id) . "' class='btn btn-danger btn-sm' target='_blank' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice PDF'><i class='bi bi-file-earmark-pdf'></i></a>";
            // $view_order .= " <button type='button' value='Print this page' onclick='{window.print()};' class='btn btn-sm btn-default'><i class='fa fa-print'></i> Print</button>";

            if (!isset($order[0])) {
                $rows[$i] = [
                    'id' => $order['id'],
                    'order_type' => ucwords($order['order_type']),
                    'Order_date' => date_formats(strtotime($order['created_at'])),
                    'vendor_id' => $order['vendor_id'],
                    'customer_name' => $customer_name,
                    'business_id' => $order['business_id'],
                    'total' => currency_location(decimal_points($order['total'])),
                    'remaining_amount' => currency_location(decimal_points(($order['total'] - $order['amount_paid']))),
                    'balance' => currency_location(decimal_points($balance)),
                    'delivery_charges' => currency_location(decimal_points($order['delivery_charges'])),
                    'discount' => currency_location(decimal_points($order['discount'])),
                    'final_total' => currency_location(decimal_points($order['final_total'])),
                    'payment_status' => $status,
                    'message' => $order['message'],
                    'amount_paid' => currency_location(decimal_points($order['amount_paid'])),
                    'payment_method' => $order['payment_method'],
                    'delivery_boy' => $delivery_boy_name,
                    'action' => $view_order
                ];
                $i++;
            }
        }
        if (is_array($orders)) {
            $array['total'] = $total;
        }
        $array['rows'] = $rows;
        echo json_encode($array);
    }


    public function send_reminder()
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

            $order_id = $_GET['order_id'];
            $order_details = fetch_details('orders', ['id' => $order_id]);
            $user_id = fetch_details('customers', ['user_id' => $order_details[0]['customer_id']]);
            $user_id = $user_id[0]['user_id'];

            $customer_details = $this->ionAuth->user($user_id)->row();

            $email_config = array(
                'charset' => 'iso-8859-1',
                'mailType' => 'html'
            );
            $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id'])) ? trim($_SESSION['business_id']) : "";
            $setting = get_settings('email', true);
            $company_title = get_settings('general', true);
            $icon = get_business_icon($business_id);
            $primary_color = $company_title["primary_color"];

            $message = '<!DOCTYPE html>
            <html>
            <head>
                <meta charset="UTF-8">
                <title>Payment Reminder</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        font-size: 14px;
                        line-height: 1.5;
                        color: #333333;
                        background-color: #F7F7F7;
                        padding: 0;
                        margin: 0;
                    }
                    .container {
                        box-shadow: 0px -1px 6px 3px #0000000d;
                        max-width: 600px;
                        margin: 0 auto;
                        background-color: #FFFFFF;
                        padding: 30px;
                        border-radius: 5px;
                        border: 1px solid #efefef;
                        margin-top: 10px;
                        box-shadow: -1px 1px 4px 2px #40404021;
                    }
                    p {
                        font-size: large;
                        margin-bottom: 10px;
                    }
                    .invoice {
                        margin-top: 30px;
                        border-collapse: collapse;
                        width: 100%;
                    }
                    .invoice th, .invoice td {
                        padding: 10px;
                        border: 1px solid #CCCCCC;
                        text-align: left;
                        font-size: 14px;
                        line-height: 1.5;
                    }
                    .invoice th {
                        background-color: #F7F7F7;
                        font-weight: bold;
                        font-size: medium;
                    }
                    .invoice td {
                        background-color: #FFFFFF;
                    }
                    .button {
                        display: inline-block;
                        padding: 10px 20px;
                        background-color: #2E7D32;
                        color: #FFFFFF;
                        font-size: 14px;
                        font-weight: bold;
                        text-decoration: none;
                        border-radius: 5px;
                    }
                    .button:hover {
                        background-color: #4CAF50;
                    }
                    .h1 {
                        color: ' . $company_title['primary_color'] . ';
                        font-size: 24px;
                    }
                    .th {
                        background-color: #ffffff;
                        color: #f00;
                        text-align: left;
                        padding: 10px;
                    }
                    .image-box-100 {
                        display: flex;
                        flex-wrap: nowrap;
                        align-content: center;
                        justify-content: center;
                        align-items: center;
                        width: 250px;
                        height: 80px;
                    }
                    .image-box-100 img {
                        max-width: 100%;
                        max-height: 100%;
                    }
                    /* Responsive Styles */
                    @media screen and (max-width: 600px) {
                        .container {
                            padding: 20px;
                        }
                        h1 {
                            font-size: 20px;
                        }
                        .invoice th, .invoice td {
                            font-size: 12px;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="card">
                        <div class="d-flex justify-content-center">
                            <div class="image-box-100">
                                <img class="image-box-100" src="' . base_url($icon['icon']) . '">
                            </div>
                        </div>
                        <h1 class="h1">Payment Reminder for Order #[' . $order_details[0]['id'] . ']</h1>
                        <p>Dear ' . (isset($customer_details->first_name) ? $customer_details->first_name : '') . ',</p>
                        <p>I hope this email finds you well. I am writing to remind you that the payment for the above-mentioned invoice is now overdue. According to our records, the payment was due from ' . $order_details[0]['created_at'] . '.</p>
                        <p>Please note that late payment can cause significant problems for us as a company, and we would appreciate it if you could settle the outstanding balance as soon as possible.</p>
                        <table class="invoice">
                            <thead class="th">
                                <tr>
                                    <th>Order Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>' . $order_details[0]['created_at'] . '</td>
                                    <td><strong>' . currency_location(decimal_points(($order_details[0]['total'] - $order_details[0]['amount_paid']))) . '</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <p>If you have already made the payment, kindly ignore this email.</p>
                        <p>If you have any questions or concerns regarding this payment, please do not hesitate to contact us. <br><br>Best regards,<br> ' . $company_title['title'] . '</p>
                    </div>
                </div>
            </body>
            </html>';

            $email_con = [
                'protocol' => 'smtp',
                'SMTPHost' => $setting['smtp_host'],
                'SMTPPort' => (int) $setting['smtp_port'],
                'SMTPUser' => $setting['email'],
                'SMTPPass' => $setting['password'],
                'SMTPCrypto' => $setting['smtp_encryption'],
                'mailType' => $setting['mail_content_type'],
                'charset' => 'utf-8',
                'wordWrap' => true,
            ];

            $subject = "Payment Reminder";
            $email = \Config\Services::email();
            $email->initialize($email_con);
            $email->setFrom($setting['email'], $company_title['title']);
            $email->setTo(trim($customer_details->email));
            $email->setSubject($subject);
            $email->setMessage($message);

            if ($email->send()) {
                return $this->response->setJSON([
                    "error" => false,
                    "message" => "Email sent!",
                    "data" => [],
                    "csrf_token" => csrf_token(),
                    "csrf_hash" => csrf_hash()
                ]);
            } else {
                return $this->response->setJSON([
                    "error" => true,
                    "message" => "Something went wrong Please try again after some time.",
                    "data" => [
                        'console' => "console.log(" . $email->printDebugger() . ");"
                    ],
                    "csrf_token" => csrf_token(),
                    "csrf_hash" => csrf_hash()
                ]);
            }
        }
    }

    public function get_status()
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
            $data['page'] = VIEWS . 'status_table';
            $data['title'] = "Status - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['business_id'] = session('business_id');
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("vendor/template", $data);
        }
    }

    public function status_table()
    {
        $status_model = new Status_model();
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        // Fetch supplier data with total
        $status_data = $status_model->get_status_list($business_id);
        $status = $status_data['data'] ?? [];
        $total = $status_data['total'] ?? 0;

        $rows = [];
        foreach ($status as $val) {

            $operation = '';
            if ($val['operation'] == 0) {
                $operation = "Do Nothing";
            } elseif ($val['operation'] == 1) {
                $operation = "Credit";
            } elseif ($val['operation'] == 2) {
                $operation = "Debit	";
            }

            $rows[] = [
                'id' => $val['id'] ?? '',
                'name' => ucwords($val['status'] ?? ''),
                'operation' => $operation,
            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $rows
        ]);
    }
}
