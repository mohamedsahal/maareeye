<?php

namespace App\Models;

use CodeIgniter\Model;

class Purchases_model extends Model
{

    protected $table = 'purchases';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'business_id', 'supplier_id', 'warehouse_id', 'order_no', 'order_type', 'purchase_date', 'tax_ids', 'delivery_charges', 'total', 'message', 'discount', 'payment_method', 'payment_status', 'amount_paid', 'status'];

    public function get_purchases($vendor_id, $business_id, $order_type)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("purchases as p");

        $builder->select('p.*, p.total as purchase_total, u.first_name as supplier_name, s.status as return_status');
        $builder->join('users as u', 'u.id = p.supplier_id', 'left');
        $builder->join('status as s', 's.id = p.status', 'left');

        // Base where conditions
        $builder->where('p.business_id', $business_id);
        $builder->where('p.order_type', $order_type);

        // Filters
        if (!empty($_GET['payment_status_filter'])) {
            $builder->where('p.payment_status', $_GET['payment_status_filter']);
        }

        if (!empty($_GET['supplier_id'])) {
            $builder->where('p.supplier_id', $_GET['supplier_id']);
        }

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $builder->where("p.created_at >=", $_GET['start_date'] . ' 00:00:00');
            $builder->where("p.created_at <=", $_GET['end_date'] . ' 23:59:59');
        }

        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('p.supplier_id', $search)
                ->orLike('u.first_name', $search)
                ->orLike('s.status', $search)
                ->orLike('p.total', $search)
                ->orLike('p.order_no', $search)
                ->orLike('p.purchase_date', $search)
                ->orLike('p.payment_status', $search)
                ->groupEnd();
        }

        // Clone builder before applying limit/offset to count total
        $total_builder = clone $builder;
        $total = $total_builder->countAllResults(false); // false = don't reset query

        // Apply limit & offset
        $limit = $_GET['limit'] ?? '';
        $offset = $_GET['offset'] ?? '';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Sorting
        $sort = $_GET['sort'] ?? 'p.id';
        $order = $_GET['order'] ?? 'DESC';
        $builder->orderBy($sort, $order);

        $purchases = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $purchases
        ];
    }

    public function get_purchase_invoice($purchase_id = '', $business_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("purchases as p");
        // Apply filters
        $builder->where(['p.id' => $purchase_id, 'p.business_id' => $business_id]);

        // Join tables
        $builder->join('purchases_items as pi', 'pi.purchase_id = p.id');
        $builder->join('warehouses', 'warehouses.id=p.warehouse_id');
        $builder->join('users as u', 'u.id = p.supplier_id');
        $builder->join('products_variants as pv', 'pv.id = pi.product_variant_id');
        $builder->join('products', 'pv.product_id = products.id');
        $builder->join('businesses as b', 'b.id = p.business_id');

        $builder->select('pv.variant_name, products.name as product_name, p.*,p.id as purchase_id,p.discount as purchase_discount,pi.price,pi.discount,pi.quantity,pi.id as purchase_items_id,b.contact,b.name,b.icon,b.address,b.tax_name as b_tax,b.description,b.tax_value,u.first_name,u.last_name,u.mobile,u.email, p.payment_status ,p.status , warehouses.name as warehouse_name');

        $res = $builder->get()->getResultArray();
        return $res;
    }

    public function purchases_report_table($business_id)
    {

        $db = \Config\Database::connect();
        $builder = $db->table("purchases p");
        $builder->join('users u', 'u.id = p.supplier_id');
        $builder->select('p.id as purchase_id, p.supplier_id, u.first_name, u.mobile, p.purchase_date, p.amount_paid, p.total, p.payment_status, (p.total - p.amount_paid) as remaining_amount');
        $builder->where('p.business_id', $business_id);

        $rows = [];
        $multipleWhere = [];

        // Filters
        if (!empty($_GET['payment_status_filter'])) {
            $builder->where('p.payment_status', $_GET['payment_status_filter']);
        }

        if (!empty($_GET['supplier_id'])) {
            $builder->where('p.supplier_id', $_GET['supplier_id']);
        }

        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $builder->where('p.created_at >=', $_GET['start_date'] . ' 00:00:00');
            $builder->where('p.created_at <=', $_GET['end_date'] . ' 23:59:59');
        }

        // Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'p.id' => $search,
                'p.supplier_id' => $search,
                'u.first_name' => $search,
                'u.mobile' => $search,
                'p.purchase_date' => $search,
                'p.amount_paid' => $search,
                'p.total' => $search,
                'p.payment_status' => $search,
            ];

            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';

        // Sorting
        $sort = $_GET['sort'] ?? 'p.id';
        $order = $_GET['order'] ?? 'DESC';

        // Total Count (clone builder to avoid affecting the main one)
        $countBuilder = clone $builder;
        $total = $countBuilder->select('COUNT(p.id) as total_count')->get()->getRow('total_count') ?? 0;

        // Apply limit
        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Fetch paginated data
        $purchases = $builder->orderBy($sort, $order)->get()->getResultArray();

        // Prepare response rows
        foreach ($purchases as $report) {
            $status_badge = '';
            switch ($report['payment_status']) {
                case 'fully_paid':
                    $status_badge = '<span class="badge badge-success text-dark">Fully Paid</span>';
                    break;
                case 'unpaid':
                    $status_badge = '<span class="badge badge-danger text-dark">Unpaid</span>';
                    break;
                case 'partially_paid':
                    $status_badge = '<span class="badge badge-warning text-dark">Partially Paid</span>';
                    break;
                default:
                    $status_badge = '<span class="badge badge-secondary text-dark">' . ucfirst($report['payment_status']) . '</span>';
            }

            $rows[] = [
                'purchase_id' => $report['purchase_id'],
                'supplier_id' => $report['supplier_id'],
                'first_name' => $report['first_name'],
                'mobile' => $report['mobile'],
                'purchase_date' => date_formats(strtotime($report['purchase_date'])),
                'amount_paid' => currency_location(decimal_points($report['amount_paid'])),
                'total' => currency_location(decimal_points($report['total'])),
                'payment_status' => $status_badge,
                'remaining_amount' => currency_location(decimal_points($report['remaining_amount'])),
            ];
        }

        return [
            'total' => $total,
            'rows' => $rows
        ];
    }

    public function supplier_details($business_id)
    {
        $db = \config\Database::connect();
        $builder = $db->table("purchases p");
        $builder->join('users u ', 'u.id = p.supplier_id');
        $builder->where('p.business_id', $business_id);
        $builder->select('supplier_id ,first_name');
        $builder->groupBy('supplier_id');
        return $builder->get()->getResultArray();
    }

}
