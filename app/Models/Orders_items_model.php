<?php

namespace App\Models;

use CodeIgniter\Model;
use Countable;

class Orders_items_model extends Model
{

    protected $table = 'orders_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['order_id', 'product_id', 'product_variant_id', 'product_name', 'quantity', 'price', 'tax_name', 'tax_percentage', 'is_tax_included', 'tax_details', 'sub_total', 'status', 'delivery_boy'];


    public function top_selling_products()
    {
        $db = \Config\Database::connect();
        $business_id = $_SESSION['business_id'] ?? '';

        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';
        $sort = $_GET['sort'] ?? 'total_sales';
        $order = $_GET['order'] ?? 'DESC';

        $builder = $db->table("orders_items as ot")
            ->join('orders o', 'o.id = ot.order_id')
            ->join('products p', 'p.id = ot.product_id')
            ->where('p.business_id', $business_id)
            ->groupBy('ot.product_id')
            ->select('COUNT(ot.order_id) as total_sales, SUM(ot.price) as total_amount, ot.product_id, ot.price, p.name, p.stock');

        // Apply date range filter
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $builder->where('o.created_at >=', $_GET['start_date'] . ' 00:00:00');
            $builder->where('o.created_at <=', $_GET['end_date'] . ' 23:59:59');
        }

        // Apply search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('p.id', $search)
                ->orLike('p.name', $search)
                ->orLike('ot.price', $search)
                ->groupEnd();
        }

        // Clone builder for total count before pagination
        $count_builder = clone $builder;
        $total = $count_builder->countAllResults(false); // keeps current builder state

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Fetch data with pagination
        $results = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        // Format result
        $rows = [];
        foreach ($results as $i => $report) {
            $rows[] = [
                'product_id' => $report['product_id'],
                'product_name' => $report['name'],
                'price' => currency_location(decimal_points($report['price'])),
                'total_sales' => $report['total_sales'],
                'total_amount' => currency_location(decimal_points($report['total_amount'])),
            ];
        }

        return [
            'total' => $total,
            'rows' => $rows,
        ];
    }
}
