<?php

namespace App\Controllers\vendor;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Customers_transactions_model;
use App\Models\Purchases_items_model;
use App\Models\Purchases_model;
use App\Models\Status_model;
use App\Models\Suppliers_model;
use App\Models\Tax_model;
use App\Models\Vendor_purchase_transactions_model;
use App\Models\WarehouseModel;
use App\Models\WarehouseProductStockModel;

class Purchases extends BaseController
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
            $data['page'] = VIEWS . 'purchases_table';
            $data['title'] = "Purchase List - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $vendor_id = $_SESSION['user_id'];
            if ($this->ionAuth->isTeamMember()) {
                $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
            } else {
                $vendor_id = $_SESSION['user_id'];
            }
            $id = $_SESSION['user_id'];
            $data['business_id'] = $business_id;
            $data['vendor_id'] = $vendor_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['status'] = isset($status) ? $status : "";
            $tax_model = new Tax_model();
            $data['taxes'] = $tax_model->findAll();
            $data['order_type'] = 'order';

            $orders = fetch_details('purchases', ['business_id' => $business_id]);

            if (isset($orders) && !empty($orders)) {
                foreach ($orders as $order) {
                    if (floatval($order['amount_paid']) == floatval($order['total'])) {
                        update_details(['payment_status' => 'fully_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) < floatval($order['total'])) {
                        update_details(['payment_status' => 'partially_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) == 0.00) {
                        update_details(['payment_status' => 'unpaid'], ['id' => $order['id']], "purchases");
                    }
                }
            }
            return view("vendor/template", $data);
        }
    }

    public function purchase_orders($type = '')
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $session = session();
            if ($this->ionAuth->isTeamMember()) {
                if ($type === "order") {
                    if (!userHasPermission('purchases', 'can_create', session('user_id'))) {
                        $session->setFlashdata("message", "You do not have permission for this action");
                        $session->setFlashdata("type", "error");
                        return redirect()->to(base_url('vendor/home'));
                    }
                } else if ($type === "return") {
                    if (!userHasPermission('purchase_return', 'can_create', session('user_id'))) {

                        $session->setFlashdata("message", "You do not have permission for this action");
                        $session->setFlashdata("type", "error");
                        return redirect()->to(base_url('vendor/home'));
                    }
                }
            }
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $uri = current_url(true);
            // $orderType = $uri->getSegment(6);
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'purchases';
            $data['title'] = "Purchase Order- " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['status'] = isset($status) ? $status : "";
            $data['order_type'] = $type;
            $tax_model = new Tax_model();
            $data['taxes'] = $tax_model->findAll();
            $warehouse_model = new WarehouseModel();
            $data['warehouses'] = $warehouse_model->where('business_id', $business_id)->get()->getResultArray();
            return view("vendor/template", $data);
        }
    }
    public function get_suppliers()
    {
        $user_id = $_SESSION['user_id'];
        $id = 0;
        if ($this->ionAuth->isTeamMember()) {
            $id = get_vendor_for_teamMember($user_id);
        } else {
            $id = $user_id;
        }
        $suppliers_model = new Suppliers_model();
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $response = $suppliers_model->search_suppliers($search, $id);
            echo $response;
        }
    }

    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $rules = [
                    'purchase_date' => [
                        'rules' => 'required',
                        'label' => 'Purchase Date',
                    ],
                    'supplier_id' => [
                        'rules' => 'required',
                        'label' => 'Supplier',
                    ],
                    'products' => [
                        'rules' => 'required',
                        'label' => 'Products',
                    ],
                    'status' => [
                        'rules' => 'required',
                        'label' => 'Status',
                    ],
                    'payment_status' => [
                        'rules' => 'required',
                        'label' => 'Payment Status',
                    ],
                    'warehouse_id' => [
                        'rules' => 'required',
                        'label' => 'Warehouse',
                    ],
                ];

                // Add conditional rules for "partially_paid"
                if ($this->request->getVar('payment_status') == "partially_paid") {
                    $rules['amount_paid'] = [
                        'rules' => 'required',
                        'label' => 'Amount Paid',
                    ];
                }

                // Add conditional rules for other statuses
                if ($this->request->getVar('payment_status') != "unpaid" && $this->request->getVar('payment_status') != "cancelled") {
                    $rules['payment_method'] = [
                        'rules' => 'required',
                        'label' => 'Payment Method',
                    ];
                }

                // Set the validation rules
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
                    if ($this->ionAuth->isTeamMember()) {
                        $session = session();
                        $order_type = $this->request->getVar('order_type');
                        if ($order_type === "order") {
                            if (!userHasPermission('purchases', 'can_create', session('user_id'))) {
                                $session->setFlashdata("message", "You do not have permission for this action");
                                $session->setFlashdata("type", "error");
                                return redirect()->to(site_url('admin/home'));
                            }
                        } else if ($order_type === "return") {
                            if (!userHasPermission('purchase_return', 'can_create', session('user_id'))) {

                                $session->setFlashdata("message", "You do not have permission for this action");
                                $session->setFlashdata("type", "error");
                                return redirect()->to(site_url('admin/home'));
                            }
                        }
                    }
                    $tax_ids = '[]';
                    $warehouse_id = $this->request->getVar('warehouse_id');
                    $tax_ids_input = $this->request->getVar('order_taxes');
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
                    $purchase_model = new Purchases_model();
                    $warehouse_product_stock_model = new WarehouseProductStockModel();
                    $user_id = $this->ionAuth->getUserId();
                    $vendor_id = 0;
                    if ($this->ionAuth->isTeamMember()) {
                        $vendor_id = get_vendor_for_teamMember($user_id);
                    } else {
                        $vendor_id = session('user_id');
                    }
                    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

                    $payment_status = $this->request->getVar('payment_status');

                    if ($payment_status == "unpaid" || $payment_status == "cancelled") {
                        $payment_method = null;
                    } else {
                        $payment_method = $this->request->getVar('payment_method');
                    }

                    $amount_paid = 0.00;

                    if ($payment_status == "fully_paid") {
                        $amount_paid = $this->request->getVar('total');
                    } else if ($payment_status == "partially_paid") {
                        $amount_paid = $this->request->getVar('amount_paid');
                    }

                    if ($amount_paid > $this->request->getVar('total')) {
                        $err = [
                            'error' => true,
                            'message' => ['Amount paid cannot be greater than total amount'],
                            'data' => []
                        ];
                        $err['csrf_token'] = csrf_token();
                        $err['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($err);

                    }

                    $purchase = array(
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'order_no' => $this->request->getVar('order_no'),
                        'order_type' => $this->request->getVar('order_type'),
                        'warehouse_id' => $warehouse_id,
                        'purchase_date' => $this->request->getVar('purchase_date'),
                        'supplier_id' => $this->request->getVar('supplier_id'),
                        'tax_ids' => $tax_ids,
                        'discount' => $this->request->getVar('order_discount'),
                        'delivery_charges' => $this->request->getVar('shipping'),
                        'payment_status' => $this->request->getVar('status'),
                        'amount_paid' => $amount_paid,
                        'total' => $this->request->getVar('total'),
                        'status' => $this->request->getVar('status'),
                        'message' => $this->request->getVar('message'),
                        'payment_method' => $payment_method,
                    );

                    $purchase_model->save($purchase);
                    $purchase_id = $purchase_model->getInsertID();

                    $products = json_decode($_POST['products']);

                    $count = count($products);
                    for ($i = 0; $i < $count; $i++) {
                        $products[($count - 1) - $i]->qty = $_POST['qty'][$i];
                        $products[($count - 1) - $i]->discount = $_POST['discount'][$i];
                    }
                    $currentDateTime = date('Y-m-d H:i:s');
                    foreach ($products as $item) {
                        $purchase_items = array(
                            'purchase_id' => $purchase_id,
                            'product_variant_id' => $item->id,
                            'quantity' => $item->qty,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'status' => $this->request->getVar('status')
                        );
                        $Purchases_items_model = new Purchases_items_model();
                        $Purchases_items_model->save($purchase_items);
                        $order_type = $this->request->getVar('order_type');
                        if ($order_type == "order") {
                            update_stock(product_variant_ids: $item->id, qtns: $item->qty, type: 'plus');
                            if (is_exist(['product_variant_id' => $item->id, 'warehouse_id' => $warehouse_id], ' warehouse_product_stock')) {
                                updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $item->id, warehouse_stock: $item->qty, type: 1);
                            } else {
                                $warehouse_data = [
                                    'warehouse_id' => $warehouse_id,
                                    'product_variant_id' => $item->id,  // Correct variant ID
                                    'stock' => $item->qty,
                                    'qty_alert' => 0,
                                    'vendor_id' => $vendor_id,
                                    'business_id' => $business_id,
                                    'updated_at' => $currentDateTime,
                                ];
                                $warehouse_product_stock_model->save($warehouse_data);
                            }
                        } elseif ($order_type == "return") {
                            update_stock(product_variant_ids: $item->id, qtns: $item->qty);
                            updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $item->id, warehouse_stock: $item->qty, type: 0);
                        }
                    }

                    $transaction = array(
                        'order_id' => $purchase_id,
                        'supplier_id' => $this->request->getVar('supplier_id'),
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'transaction_type' => "debit",
                        'order_type' => $this->request->getVar('order_type'),
                        'created_by' => $vendor_id,
                        'payment_type' => $payment_method,
                        'amount' => $amount_paid,
                        'message' => $this->request->getVar('message')
                    );
                    $vendor_transaction_model = new Vendor_purchase_transactions_model();
                    $vendor_transaction_model->save($transaction);

                    $response = [
                        'error' => false,
                        'message' => 'Purchase Order saved successfully',
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

    public function purchase_table()
    {
        $purchase_model = new Purchases_model();
        $user_id = $_SESSION['user_id'];

        // Get vendor ID based on user type
        $vendor_id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;
        $business_id = $_SESSION['business_id'];

        // Fetch purchases
        $purchases = $purchase_model->get_purchases($vendor_id, $business_id, 'order');

        $rows = [];
        foreach ($purchases['data'] as $purchase) {
            $status_labels = [
                'fully_paid' => 'success',
                'partially_paid' => 'primary',
                'unpaid' => 'warning',
                'cancelled' => 'danger'
            ];

            $status_class = $status_labels[$purchase['payment_status']] ?? 'secondary';
            $status = "<span class='badge badge-$status_class'>" . ucwords(str_replace('_', ' ', $purchase['payment_status'])) . "</span>";

            $supplier_name = ucwords(get_supplier($purchase['supplier_id']));
            $purchase_id = $purchase['id'];

            $edit = "<a href='" . site_url('vendor/purchases/view_purchase/' . $purchase_id) . "' class='btn btn-primary btn-sm' data-toggle='tooltip' title='View'><i class='bi bi-eye'></i></a> ";
            $edit .= "<a href='" . base_url('vendor/purchases/invoice/' . $purchase_id) . "' class='btn btn-warning btn-sm' data-toggle='tooltip' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>";

            $rows[] = [
                'id' => $purchase_id,
                'supplier_name' => $supplier_name,
                'purchase_date' => date_formats(strtotime($purchase['purchase_date'])),
                'status' => $status,
                'amount_paid' => currency_location(decimal_points($purchase['amount_paid'])),
                'purchase_total' => currency_location(decimal_points($purchase['total'])),
                'action' => $edit
            ];
        }

        // Return data
        if (!empty($rows)) {
            echo json_encode([
                'rows' => $rows,
                'total' => $purchases['total']
            ]);
        }

    }

    public function view_purchase($purchase_id)
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
            $data['page'] = VIEWS . 'view_purchase';
            $data['title'] = "Purchase Order Details - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['status'] = isset($status) ? $status : "";
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';

            $purchase = get_purchase_items($purchase_id)[0];
            $warehouse = fetch_details('warehouses', ['id' => $purchase['warehouse_id']], 'name');
            $data['order'] = $purchase;
            $data['order']['supplier_name'] = get_supplier($purchase['supplier_id']);
            $data['order']['warehouse_name'] = isset($warehouse) && !empty($warehouse) ? $warehouse[0]['name'] : '';
            $data['items'] = $purchase['items'];

            return view("vendor/template", $data);
        }
    }

    public function update_status_bulk()
    {
        $status = subscription();
        if ($status == 'active') {
            $status = $_POST['status'];
            $item_id = $_POST['item_ids'];
            $msg = "";
            for ($i = 0; $i < count($item_id); $i++) {
                update_details(['status' => $status], ['id' => $item_id[$i]], 'purchases_items');
            }
            if ($msg != "" && !empty($msg)) {
                $response = [
                    'error' => true,
                    'type' => 'error',
                    'message' => $msg,
                ];
                return $this->response->setJSON($response);
            }
            if ($msg == "") {
                $response = [
                    'error' => false,
                    'type' => 'success',
                    'message' => "Order status updated successfully!",
                ];
                return $this->response->setJSON($response);
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
            return $this->response->setJSON($response);
        }
    }

    public function update_order_status()
    {
        $status = subscription();
        if ($status == 'active') {
            $status = $this->request->getGet('status');
            $order_id = $this->request->getGet('order_id');
            if ($this->request->getGet('status')) {
                $rules['status'] = 'required';
            }
            if ($this->request->getGet('order_id')) {
                $rules['order_id'] = 'numeric';
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
                update_details(['status' => $status], ['id' => $order_id], 'purchases_items');
                $response = [
                    'error' => false,
                    'message' => "Order status updated successfully!",
                ];
                return $this->response->setJSON($response);
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

    public function invoice($purchase_id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isVendor() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
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
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "purchase_invoice";
            $data['title'] = "Invoice - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $purchase_model = new Purchases_model();
            $tax_model = new Tax_model();
            $order = $purchase_model->get_purchase_invoice($purchase_id, $business_id);
            $subTotal = 0;

            foreach ($order as $order_item) {

                $subTotal += ($order_item['price'] * $order_item['quantity']) - $order_item['discount'];
            }

            if (isset($order) && !empty($order)) {
                $data['order'] = $order[0];
                if (gettype(json_decode($order[0]['tax_ids'])) != 'array') {
                    $tax = $tax_model->find($order[0]['tax_ids']);
                    $taxes[] = [
                        'id' => $tax['id'],
                        'name' => $tax['name'],
                        'percentage' => $tax['percentage'],
                    ];
                    $data['tax'] = $taxes;
                } else {
                    $tax_amount = 0;
                    $taxes = [];
                    foreach (json_decode($order[0]['tax_ids']) as $tax_id) {
                        $tax = $tax_model->find($tax_id);
                        $taxes[] = [
                            'id' => $tax['id'],
                            'name' => $tax['name'],
                            'percentage' => $tax['percentage'],
                        ];
                    }

                    $data['tax'] = $taxes;
                }
                $data['sub_total'] = $subTotal;
            } else {
                $order = [];
            }
            return view("vendor/template", $data);
        }
    }

    public function invoice_table($purchase_id)
    {
        $orders_model = new Purchases_model();

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $orders = $orders_model->get_purchase_invoice($purchase_id, $business_id);
        $total = count($orders);
        $order_total = 0.00;
        if (!empty($orders)) {
            $i = 0;
            foreach ($orders as $order) {

                $sub_total = floatval($order['quantity'] * $order['price']) - floatval($order['discount']);
                $rows[$i] = [
                    'name' => ucwords($order['product_name'] . "/" . $order['variant_name']),
                    'price' => currency_location(decimal_points($order['price'])),
                    'quantity' => ucwords($order['quantity']),
                    'discount' => ucwords($order['discount']),
                    'subtotal' => currency_location(decimal_points($sub_total))
                ];

                $i++;
                $order_total += $sub_total;
            }
            $row = [
                'name' => "",
                'price' => "",
                'quantity' => "",
                'discount' => "<strong>Total</strong>",
                'subtotal' => "<strong>" . currency_location(decimal_points($order_total)) . "</strong>",
            ];

            array_push($rows, $row);
            $array['total'] = $total;
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }

    public function purchase_return()
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
            $data['page'] = VIEWS . 'purchases_return';
            $data['title'] = "Purchase Return List - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['status'] = isset($status) ? $status : "";
            $tax_model = new Tax_model();
            $data['taxes'] = $tax_model->findAll();
            $data['order_type'] = 'return';
            $orders = fetch_details('purchases', ['business_id' => $business_id]);

            if (isset($orders) && !empty($orders)) {
                foreach ($orders as $order) {
                    if (floatval($order['amount_paid']) == floatval($order['total'])) {
                        update_details(['payment_status' => 'fully_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) < floatval($order['total'])) {
                        update_details(['payment_status' => 'partially_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) == 0.00) {
                        update_details(['payment_status' => 'unpaid'], ['id' => $order['id']], "purchases");
                    }
                }
            }
            return view("vendor/template", $data);
        }
    }

    public function purchase_return_table()
    {
        $purchase_model = new Purchases_model();
        $vendor_id = $_SESSION['user_id'];
        $business_id = $_SESSION['business_id'];

        // Fetch return purchases
        $purchases = $purchase_model->get_purchases($vendor_id, $business_id, 'return');

        $rows = [];
        $status_labels = [
            'fully_paid' => 'success',
            'partially_paid' => 'primary',
            'unpaid' => 'info',
            'cancelled' => 'danger'
        ];

        foreach ($purchases['data'] as $purchase) {
            // Payment Status Badge
            $badge_class = $status_labels[$purchase['payment_status']] ?? 'secondary';
            $status = "<span class='badge badge-$badge_class'>" . ucwords(str_replace('_', ' ', $purchase['payment_status'])) . "</span>";

            // Order Status Badge
            $purchase_status = status_name($purchase['status']);
            $order_status = "<span class='badge badge-custom'>" . $purchase_status . "</span>";

            // Supplier Name
            $supplier_name = ucwords(get_supplier($purchase['supplier_id']));
            $purchase_id = $purchase['id'];

            // Action Buttons
            $edit = "<a href='" . site_url('vendor/purchases/view_purchase/' . $purchase_id) . "' class='btn btn-primary btn-sm' data-toggle='tooltip' title='View'><i class='bi bi-eye'></i></a> ";
            $edit .= "<a href='" . base_url("vendor/purchases/invoice/$purchase_id") . "' class='btn btn-warning btn-sm' data-toggle='tooltip' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>";

            // Final Row Data
            $rows[] = [
                'id' => $purchase_id,
                'supplier_name' => $supplier_name,
                'purchase_date' => date_formats(strtotime($purchase['purchase_date'])),
                'return_status' => ucwords($purchase_status),
                'status' => $status,
                'amount_paid' => currency_location(decimal_points($purchase['amount_paid'])),
                'purchase_total' => currency_location(decimal_points($purchase['total'])),
                'order_status' => $order_status,
                'action' => $edit
            ];
        }

        // Final Output
        if (!empty($rows)) {
            echo json_encode([
                'rows' => $rows,
                'total' => $purchases['total']
            ]);
        }

    }
}
