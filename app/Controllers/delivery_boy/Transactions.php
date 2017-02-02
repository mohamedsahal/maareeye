<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Customers_model;
use App\Models\Customers_transactions_model;

class Transactions extends BaseController
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
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
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
            $data['page'] = FORMS . "transaction";
            $data['title'] = "Transaction - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['delivery_boy_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $data['businesses'] = $businesses;
            return view("delivery-man/template", $data);
        }
    }
    public function transactions_table()
    {

        $vendor_id = get_vendor_of_delivery_boy($_SESSION['user_id']);
        $customers_transaction_model = new Customers_transactions_model();
        $transactions = $customers_transaction_model->get_transactions($vendor_id); // assumes merged version returns ['data' => [], 'total' => 0]

        $rows = [];

        if (!empty($transactions['data']) && is_array($transactions['data'])) {
            foreach ($transactions['data'] as $transaction) {
                // Get customer user_id
                if ($transaction['order_id'] == 0) {
                    $customer_user_id = $transaction['customer_id'];
                } else {
                    $customer_detail = fetch_details("customers", ['id' => $transaction['customer_id']], 'user_id');
                    $customer_user_id = $customer_detail[0]['user_id'] ?? null;
                }

                // Get customer name
                $customer = $customer_user_id ? $this->ionAuth->user($customer_user_id)->row() : null;
                $customer_name = $customer->first_name ?? 'N/A';

                // Get creator name
                $creator = $this->ionAuth->user($transaction['created_by'])->row();
                $created_by = $creator->first_name ?? 'Unknown';

                // Format row
                $rows[] = [
                    'id' => $transaction['id'],
                    'type' => $transaction['transaction_type'],
                    'customer_name' => $customer_name,
                    'order_id' => $transaction['order_id'],
                    'created_by' => $created_by,
                    'payment_type' => str_replace('_', ' ', ucfirst($transaction['payment_type'])),
                    'amount' => currency_location(decimal_points($transaction['amount'])),
                    'opening_balance' => currency_location(decimal_points($transaction['opening_balance'])),
                    'closing_balance' => currency_location(decimal_points($transaction['closing_balance'])),
                ];
            }
        }

        // Final output
        echo json_encode([
            'total' => $transactions['total'] ?? 0,
            'rows' => $rows
        ]);

    }

    public function customer_transaction_table($order_id = "", $customer_id = "")
    {

        $transactions = fetch_details("customers_transactions", ["order_id" => $order_id, "customer_id" => $customer_id]);
        $i = 0;
        $rows = [];

        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                if ($transaction['transaction_type'] == "2") {
                    $order = fetch_details("purchases", ['id' => $order_id]);
                    $final_total = $order[0]['total'];
                } else {
                    $order = fetch_details("orders", ['id' => $order_id]);
                    $final_total = $order[0]['final_total'];
                }
                $previous_paid_amount = isset($order) ? ($order[0]['amount_paid']) : floatval(0.00);
                $transaction_amount = $transaction['amount'];
                $payment_status = $order[0]['payment_status'];
                $amount_left = "0";

                if ($payment_status == "fully_paid") {
                    $status = "<span class='badge badge-success'>Fully Paid</span>";
                    $amount_left = "0.00";
                }
                if ($payment_status == "partially_paid") {
                    $status = "<span class='badge badge-primary'>Partially Paid</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                if ($payment_status == "unpaid") {
                    $status = "<span class='badge badge-warning'>Unpaid</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                if ($payment_status == "cancelled") {
                    $status = "<span class='badge badge-danger'>Cancelled</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                $rows[$i] = [
                    'id' => $transaction['id'],
                    'payment_type' => ucfirst(str_replace('_', ' ', $transaction['payment_type'])),
                    'created_at' => date("d-M-Y h:i A", strtotime($transaction['created_at'])),
                    'amount' => currency_location(decimal_points($transaction['amount'])),
                    'status' => $status,
                ];
                $i++;
            }
            $row = [
                'id' => "<strong>Paid</strong>",
                'amount' => "<span class='badge badge-success'>" . currency_location(decimal_points($previous_paid_amount)) . "</span>",
                'payment_type' => "<strong>Remaining Amount<strong>",
                'created_at' => "<span class='badge badge-danger'>" . currency_location(decimal_points($amount_left)) . "</span>",
                'status' => "",
            ];
            if (is_array($transactions)) {
                $array['total'] = count($transactions);
            }
            array_push($rows, $row);
        }
        $array['rows'] = $rows;
        echo json_encode($array);
    }

    public function save_payment()
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
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {
                    $customers_transaction_model = new Customers_transactions_model();

                    if (isset($_POST['order_id'])) {
                        $this->validation->setRules([
                            'payment_type' => 'required|trim',
                            'created_by' => 'required|trim',
                            'amount' => 'required|trim',
                        ]);
                    } else {
                        $this->validation->setRules([
                            'payment_type' => 'required|trim',
                            'created_by' => 'required|trim',
                            'amount' => 'required|trim',
                            'customer_id' => 'required|trim',
                            'type' => 'required|trim',

                        ]);
                    }
                    if (isset($_POST['payment_type']) && $_POST['payment_type'] == 'other') {
                        $rules['payment_method_name'] = 'trim|required';
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
                        $opening_balance = "";
                        $amount = $this->request->getVar('amount');
                        $transaction_type = 'credit';
                        if (isset($_POST['order_id'])) {
                            $customer_id = $_POST['customer_id'];
                            $order_id = $this->request->getVar('order_id');
                            $order = fetch_details("orders", ['id' => $order_id]);
                            $previous_paid_amount = isset($order[0]) ? $order[0]['amount_paid'] : "0";
                            $final_total = isset($order[0]) ? $order[0]['final_total'] : "0";
                            $amount_left = floatval($final_total) - (floatval($previous_paid_amount) + floatval($amount));

                            $type = '1';

                            if ($amount_left == 0) {
                                update_details(['payment_status' => 'fully_paid'], ['id' => $order_id], 'orders');
                                $response['error'] = true;
                                $response['csrfName'] = csrf_token();
                                $response['csrfHash'] = csrf_hash();
                                $response['message'] = ['Your order has been paid'];
                                return $this->response->setJSON($response);
                            }
                            if ($amount > $amount_left) {
                                $response['error'] = true;
                                $response['csrfName'] = csrf_token();
                                $response['csrfHash'] = csrf_hash();
                                $response['message'] = ['Amount should not be greater than remaining amount - ' . $amount_left];
                                return $this->response->setJSON($response);
                            }
                        } else {
                            $order_id = "0";
                            $customer_id = $_POST['customer_id'];
                            $customer = fetch_details("customers", ['user_id' => $customer_id]);
                            $customer_id = $customer[0]['id'];
                            $balance = isset($customer[0]['balance']) ? $customer[0]['balance'] : "";
                            $opening_balance = $balance;

                            if ($transaction_type == "debit") {
                                $amount = floatval($balance) - floatval($amount);
                            }
                            if ($transaction_type == "credit") {
                                $amount = floatval($balance) + floatval($amount);
                            }
                        }

                        $vendor = get_vendor_of_delivery_boy($_SESSION['user_id']);
                        $vendor_id = $vendor;
                        $transaction = array(
                            'customer_id' => $customer_id,
                            'order_id' => $order_id,
                            'vendor_id' => $vendor_id,
                            'created_by' => $this->request->getVar('created_by'),
                            'payment_type' => $this->request->getVar('payment_type'),
                            'transaction_type' => $transaction_type,
                            'amount' => $this->request->getVar('amount'),
                            'opening_balance' => $opening_balance,
                            'closing_balance' => $amount,
                            'message' => $this->request->getVar('message')
                        );

                        $customers_transaction_model->save($transaction);
                        if (isset($_POST['order_id'])) {
                            $amount = floatval($previous_paid_amount) + floatval($amount);
                            update_details(['amount_paid' => $amount], ['id' => $order_id], "orders");
                        }
                        update_details(['balance' => $amount], ['id' => $customer_id], "customers");
                        $response = [
                            'error' => false,
                            'message' => 'Payment added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Payment added successfully';
                        $_SESSION['toastMessageType'] = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                } else {

                    return redirect()->back();
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
    public function customers_table()
    {
        $business_id = $_SESSION['business_id'] ?? '';
        $customers_model = new Customers_model();
        $customers = $customers_model->get_customers_details($business_id);

        $rows = [];

        if (!empty($customers['data']) && is_array($customers['data'])) {
            foreach ($customers['data'] as $customer) {
                $rows[] = [
                    'id' => $customer['user_id'],
                    'customer_name' => $customer['first_name'],
                    'email' => $customer['email'],
                    'balance' => currency_location(decimal_points($customer['balance'])),
                ];
            }
        }

        $array = [
            'total' => $customers['total'] ?? 0,
            'rows' => $rows
        ];

        echo json_encode($array);

    }
}
