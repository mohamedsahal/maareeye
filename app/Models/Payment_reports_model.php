<?php

namespace App\Models;

use CodeIgniter\Model;

class Payment_reports_model extends Model
{

    protected $table = 'customers_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'customer_id', 'business_id', 'created_by', 'total', 'delivery_charges', 'discount', 'final_total', 'payment_status', 'amount_paid', 'order_type', 'message', 'payment_method'];

    public function get_payment_reports_list()
    {
        $vendor_id = !empty($vendor_id) ? $vendor_id : $_SESSION['user_id'];
        $business_id = $_SESSION['business_id'] ?? '';

        $db = \Config\Database::connect();
        $builder = $db->table("customers_transactions as ct")
            ->select('ct.*, u.id as user_id, u.username, u.email, u.first_name, u.last_name')
            ->join('users u', 'u.id = ct.customer_id')
            ->where('ct.vendor_id', $vendor_id);

        $multipleWhere = [];

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'DESC';

        // Filter: Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'ct.customer_id' => $search,
                'ct.id' => $search,
                'u.username' => $search,
                'u.email' => $search,
                'u.first_name' => $search,
                'u.last_name' => $search,
                'ct.amount' => $search,
                'ct.payment_type' => $search,
                'ct.created_at' => $search
            ];
        }

        // Filter: Payment Type
        if (!empty($_GET['payment_type_filter'])) {
            $builder->where('ct.payment_type', $_GET['payment_type_filter']);
        }

        // Filter: Date Range
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_date = $_GET['start_date'] . ' 00:00:00';
            $end_date = $_GET['end_date'] . ' 23:59:59';
            $builder->where("ct.created_at >=", $start_date);
            $builder->where("ct.created_at <=", $end_date);
        }

        // Apply search filters
        if (!empty($multipleWhere)) {
            $builder->groupStart()->orLike($multipleWhere)->groupEnd();
        }

        // Count query for total
        $count_builder = clone $builder;
        $count_builder->select('COUNT(ct.id) as total');
        $total = $count_builder->get()->getRowArray()['total'];

        // Data query
        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        $payment_reports = $builder->orderBy($sort, $order)->get()->getResultArray();
        $rows = [];

        foreach ($payment_reports as $report) {
            $rows[] = [
                'id' => $report['id'],
                'customer_id' => $report['customer_id'],
                'vendor_id' => $report['vendor_id'],
                'username' => $report['username'],
                'business_id' => $business_id,
                'payment_type' => $report['payment_type'],
                'amount' => currency_location(decimal_points($report['amount'])),
                'email' => $report['email'],
                'created_at' => $report['created_at'],
                'name' => ucfirst($report['first_name'] . ' ' . $report['last_name']),
            ];
        }

        return [
            'total' => $total,
            'rows' => $rows
        ];

    }
}
