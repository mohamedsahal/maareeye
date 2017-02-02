<?php

namespace App\Models;

use CodeIgniter\Model;

class Orders_model extends Model
{

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'vendor_id', 'customer_id', 'order_no', 'warehouse_id', 'business_id', 'created_by', 'total', 'delivery_charges', 'discount', 'final_total', 'payment_status', 'amount_paid', 'order_type', 'message', 'payment_method'];


    public function get_delivery_boy_orders_list($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders as o");

        $builder->select('o.*, o.created_at as Order_date, o.final_total as total, u.first_name as customer_name, c.balance');
        $builder->join('users as u', 'u.id = o.customer_id', "left");
        $builder->join('customers as c', 'c.user_id = o.customer_id', "left");
        $builder->where("o.business_id", $business_id);

        // Filters
        $search = $_GET['search'] ?? '';
        $start_date = $_GET['start_date'] ?? '';
        $end_date = $_GET['end_date'] ?? '';
        $payment_status_filter = $_GET['payment_status_filter'] ?? '';
        $order_type_filter = $_GET['order_type_filter'] ?? '';
        $sort = $_GET['sort'] ?? 'o.id';
        $order = $_GET['order'] ?? 'DESC';
        $offset = (int) ($_GET['offset'] ?? '');
        $limit = (int) ($_GET['limit'] ?? '');

        // Date range filter
        if (!empty($start_date) && !empty($end_date)) {
            $builder->where("o.created_at >=", "$start_date 00:00:00");
            $builder->where("o.created_at <=", "$end_date 23:59:59");
        }

        // Payment status filter
        if (in_array($payment_status_filter, ['fully_paid', 'partially_paid', 'unpaid'])) {
            $builder->where('o.payment_status', $payment_status_filter);
        }

        // Order type filter
        if (in_array($order_type_filter, ['product', 'service'])) {
            $builder->where('o.order_type', $order_type_filter);
        }

        // Search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('o.payment_method', $search)
                ->orLike('o.payment_status', $search)
                ->orLike('o.final_total', $search)
                ->orLike('o.created_at', $search)
                ->orLike('u.first_name', $search)
                ->orLike('c.balance', $search)
                ->groupEnd();
        }

        // Clone builder before applying limit for total count
        $count_builder = clone $builder;
        $total = $count_builder->select('COUNT(o.id) as total', false)->get()->getRowArray()['total'];

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Apply sorting and pagination
        $orders = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        return [
            'total' => (int) $total,
            'data' => $orders
        ];
    }

    public function get_order_invoice($order_id = "", $business_id = "")
    {
        $db = \Config\Database::connect();
        $customer_model = new Customers_model();
        $type = fetch_details('orders', ['id' => $order_id, 'business_id' => $business_id], ['order_type', 'customer_id']);

        $customer_id = $type[0]['customer_id'];

        $customer_array = $customer_model->where('user_id', $customer_id)->get()->getResultArray();
        if (empty($customer_array)) {
            $customer_array = $customer_model->where('id', $customer_id)->get()->getResultArray();
        }
        $user_id = $customer_array[0]['user_id'];

        if (isset($type) && !empty($type)) {
            if ($type[0]['order_type'] == "product") {
                $builder = $db->table("orders as o");
                $builder->select('o.created_at,b.contact,b.name as business_name,b.icon,b.address,b.tax_name as b_tax,b.tax_value,b.description,p.name as product_name,p.unit_id,u.first_name,u.last_name,u.mobile,u.email,o.id,o.order_type,o.customer_id,o.total,o.final_total,o.payment_status,o.amount_paid,o.payment_method,o.delivery_charges,o.discount,o.warehouse_id,oi.product_name as order_name,oi.product_id,oi.quantity,oi.price,oi.tax_name,oi.tax_percentage,oi.sub_total,oi.tax_details,warehouses.name as warehouse_name');
                $builder->where(['o.id' => $order_id, 'o.business_id' => $business_id]);
                $builder->join('orders_items as oi', 'oi.order_id=o.id', 'left');
                $builder->join('warehouses', 'warehouses.id=o.warehouse_id', 'left');
                $builder->join('users as u', "u.id=$user_id", 'left');
                $builder->join('products as p', 'p.id=oi.product_id', 'left');
                $builder->join('businesses as b', 'b.id=o.business_id', 'left');
                return $builder->get()->getResultArray();
            }
            if ($type[0]['order_type'] == "service") {
                $builder = $db->table("orders as o");
                $builder->select('o.created_at,b.contact,b.name as business_name,b.icon,b.address,b.tax_name as b_tax,b.description,b.tax_value,u.first_name,u.last_name,u.mobile,u.email,o.id,o.order_type,o.customer_id,o.total,o.final_total,o.payment_status,o.amount_paid,o.payment_method,o.delivery_charges,o.discount,os.service_name ,os.price,os.quantity,os.unit_name,os.sub_total,os.tax_name,os.tax_percentage,os.tax_details');
                $builder->where(['o.id' => $order_id, 'o.business_id' => $business_id]);
                $builder->join('orders_services as os', 'os.order_id=o.id', 'left');
                $builder->join('users as u', "u.id=$user_id", 'left');
                $builder->join('businesses as b', 'b.id=o.business_id', 'left');
                return $builder->get()->getResultArray();
            }
        } else {
            return false;
        }
    }

    public function best_customers_table()
    {
        $db = \Config\Database::connect();
        $business_id = $_SESSION['business_id'] ?? "";

        $builder = $db->table('orders o')
            ->join('customers c', 'c.id = o.customer_id')
            ->join('users u', 'u.id = c.user_id')
            ->where('o.business_id', $business_id)
            ->select('COUNT(o.id) as total_orders, SUM(o.final_total) as total_amount, u.mobile, u.email, u.first_name, u.id as customer_id, COUNT(o.customer_id) as total_sales')
            ->groupBy('u.id');

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';
        $sort = $_GET['sort'] ?? 'total_sales';
        $order = $_GET['order'] ?? 'DESC';

        $multipleWhere = [];

        // Search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'u.id' => $search,
                'u.first_name' => $search,
                'u.email' => $search,
                'u.mobile' => $search,
            ];
            $builder->groupStart()->orLike($multipleWhere)->groupEnd();
        }

        // Date filter
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_date = $_GET['start_date'] . ' 00:00:00';
            $end_date = $_GET['end_date'] . ' 23:59:59';
            $builder->where("o.created_at >=", $start_date);
            $builder->where("o.created_at <=", $end_date);
        }

        // Clone for counting
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // Use false to preserve the builder for further chaining

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Fetch paginated result
        $best_customers = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        // Format response
        $rows = [];
        foreach ($best_customers as $customer) {
            $rows[] = [
                'customer_id' => $customer['customer_id'],
                'first_name' => $customer['first_name'],
                'email' => $customer['email'],
                'mobile' => $customer['mobile'],
                'total_sales' => $customer['total_sales'],
                'total_amount' => currency_location(decimal_points($customer['total_amount'])),
            ];
        }

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }


    public function payment_reminder($business_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->where("business_id", $business_id);
        $builder->where('payment_status', 'partially_paid')->orWhere('payment_status', 'unpaid');

        if (isset($_GET['payment_status_filter'])) {
            $payment_status_filter = $_GET['payment_status_filter'];
            if ($payment_status_filter == 'fully_paid') {
                $builder->where('payment_status', $payment_status_filter);
            } elseif ($payment_status_filter == 'partially_paid') {

                $builder->where('payment_status', $payment_status_filter);
            } elseif ($payment_status_filter == 'unpaid') {
                $builder->where('payment_status', $payment_status_filter);
            }
        }

        if (isset($_GET['order_type_filter'])) {
            $order_type_filter = $_GET['order_type_filter'];
            if ($order_type_filter == 'product') {
                $builder->where('order_type', $order_type_filter);
            } elseif ($order_type_filter == 'service') {
                $builder->where('order_type', $order_type_filter);
            }
        }

        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {

            $builder->where('((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))');
        }

        $orders = $builder->getWhere()->getResultArray();

        return $orders;
    }
}
