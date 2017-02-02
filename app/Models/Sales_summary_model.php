<?php

namespace App\Models;

use CodeIgniter\Model;

class Sales_summary_model extends Model
{

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id', 'vendor_id', 'payment_method', 'txn_id', 'amount', 'created_at'];

    public function get_sales_summary()
    {
        $db = \Config\Database::connect();
        $business_id = $_SESSION['business_id'] ?? '';
        $vendor_id = !empty($vendor_id) ? $vendor_id : ($_SESSION['user_id'] ?? '');

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';
        $sort = $_GET['sort'] ?? 'o.id';
        $order = $_GET['order'] ?? 'DESC';

        $multipleWhere = [];

        $builder = $db->table('orders o')
            ->select('u.id as users_id, o.id as order_id, o.total, u.first_name, o.amount_paid, o.payment_method, o.payment_status, o.discount, o.delivery_charges')
            ->join('customers c', 'c.id = o.customer_id')
            ->join('users u', 'u.id = c.user_id')
            ->where('o.business_id', $business_id)
            ->where('o.vendor_id', $vendor_id);

        // Filter: Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'u.first_name' => $search,
                'u.email' => $search,
                'o.amount_paid' => $search,
                'o.payment_method' => $search,
                'o.payment_status' => $search,
                'o.total' => $search,
                'o.id' => $search,
                'u.id' => $search,
            ];
            $builder->groupStart()->orLike($multipleWhere)->groupEnd();
        }

        // Filter: Payment Type
        if (!empty($_GET['payment_type_filter'])) {
            $builder->where('o.payment_method', $_GET['payment_type_filter']);
        }

        // Filter: Date Range
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_date = $_GET['start_date'] . ' 00:00:00';
            $end_date = $_GET['end_date'] . ' 23:59:59';
            $builder->where("o.created_at >=", $start_date);
            $builder->where("o.created_at <=", $end_date);
        }

        // Clone builder for count
        $count_builder = clone $builder;
        $count_builder->select('COUNT(o.id) as total');
        $total = $count_builder->get()->getRowArray()['total'];

        // Apply limit
        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Get data
        $sales_data = $builder->orderBy($sort, $order)->get()->getResultArray();

        // Calculate total amounts
        $final_total_amount = array_sum(array_column($sales_data, 'total'));
        $final_amount_paid = array_sum(array_column($sales_data, 'amount_paid'));

        // Build response rows
        $rows = [];
        foreach ($sales_data as $report) {
            $rows[] = [
                'order_id' => $report['order_id'],
                'users_id' => $report['users_id'],
                'username' => $report['first_name'],
                'business_id' => $business_id,
                'payment_method' => str_replace('_', ' ', ucfirst($report['payment_method'])),
                'payment_status' => str_replace('_', ' ', ucfirst($report['payment_status'])),
                'total' => currency_location(decimal_points($report['total'])),
                'discount' => currency_location(decimal_points($report['discount'])),
                'delivery_charges' => currency_location(decimal_points($report['delivery_charges'])),
                'amount_paid' => currency_location(decimal_points($report['amount_paid'])),
            ];
        }

        // Append total row
        $rows[] = [
            'order_id' => '<div class="fw-bold">Total</div>',
            'users_id' => '-',
            'username' => '-',
            'business_id' => '-',
            'payment_method' => '-',
            'payment_status' => '-',
            'total' => '<span class="badge bg-primary">' . currency_location(decimal_points($final_total_amount)) . '</span>',
            'discount' => '-',
            'delivery_charges' => '-',
            'amount_paid' => '<span class="badge bg-primary">' . currency_location(decimal_points($final_amount_paid)) . '</span>',
        ];

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }
}
