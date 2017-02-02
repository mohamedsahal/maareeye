<?php

namespace App\Models;

use CodeIgniter\Model;

class Profit_loss_model extends Model
{
    protected $table = 'customers_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id', 'vendor_id', 'payment_method', 'txn_id', 'amount', 'created_at'];


    public function get_profit_loss($vendor_id)
    {
        $db = \Config\Database::connect();

        $business_id = $_SESSION['business_id'] ?? '';

        $dateRange = [];

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $start_date = $_GET['start_date'] . ' 00:00:00';
            $end_date = $_GET['end_date'] . ' 23:59:59';
            $dateRange = ['created_at >=' => $start_date, 'created_at <=' => $end_date];
        }


        // Merge business/vendor/date range filters
        $baseWhere = array_merge(['vendor_id' => $vendor_id, 'business_id' => $business_id], $dateRange);

        // Fetch summarized data
        $purchase_total = $db->table("purchases p")->selectSum('p.total', 'purchase_total')->where($baseWhere)->get()->getRow('purchase_total') ?? 0;
        $amount_collected = $db->table("orders o")->selectSum('o.amount_paid', 'amount_collected')->where($baseWhere)->get()->getRow('amount_collected') ?? 0;
        $expenses_total = $db->table("expenses e")->selectSum('e.amount', 'expenses_total')->where($baseWhere)->get()->getRow('expenses_total') ?? 0;
        $sales_total = $db->table("orders o")->selectSum('o.total', 'sales_total')->where($baseWhere)->get()->getRow('sales_total') ?? 0;

        // Calculations
        $outstanding_total = $sales_total - $amount_collected;
        $final_total = $sales_total - ($purchase_total + $expenses_total + $outstanding_total);

        // Format final total with color
        $formatted_final_total = $final_total < 0
            ? '<span class="fw-bolder text-danger">' . currency_location(decimal_points($final_total)) . '</span>'
            : '<span class="fw-bolder text-success">' . currency_location(decimal_points($final_total)) . '</span>';

        // Prepare response row
        $rows[] = [
            'purchases' => currency_location(decimal_points($purchase_total)),
            'sales' => currency_location(decimal_points($sales_total)),
            'expenses' => currency_location(decimal_points($expenses_total)),
            'amount_collected' => currency_location(decimal_points($amount_collected)),
            'outstanding_total' => currency_location(decimal_points($outstanding_total)),
            'total' => $formatted_final_total,
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
        ];

        return ['rows' => $rows];
    }
}
