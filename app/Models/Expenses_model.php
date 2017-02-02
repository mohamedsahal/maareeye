<?php

namespace App\Models;

use CodeIgniter\Model;

class expenses_model extends Model
{

    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['expenses_id', 'business_id', 'vendor_id', 'expenses_type', 'title', 'note', 'amount', 'expenses_date'];


    public function get_expenses($vendor_id = "")
    {
        $business_id = $_SESSION['business_id'] ?? "";
        $db = \Config\Database::connect();
        $builder = $db->table("expenses as e");

        // SELECT fields
        $builder->select('e.*, et.id as expenses_type_id, et.title');
        $builder->join('expenses_type as et', 'et.id = e.expenses_id', 'left');

        // Base WHERE clause
        $builder->where('e.business_id', $business_id);

        if (!empty($vendor_id)) {
            $builder->whereIn('e.vendor_id', [0, $vendor_id]);
        }

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) && intval($_GET['limit']) > 0 ? intval($_GET['limit']) : '';

        // Sorting
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart();
            $builder->orLike('e.id', $search)
                ->orLike('e.vendor_id', $search)
                ->orLike('e.expenses_id', $search)
                ->orLike('e.note', $search)
                ->orLike('e.expenses_date', $search)
                ->orLike('e.amount', $search)
                ->orLike('e.business_id', $search);
            $builder->groupEnd();
        }

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Execute main query
        $expenses = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        // Count total records
        $count_builder = $db->table("expenses as e");
        $count_builder->select('COUNT(e.id) as total');
        $count_builder->where('e.business_id', $business_id);
        if (!empty($vendor_id)) {
            $count_builder->whereIn('e.vendor_id', [0, $vendor_id]);
        }
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $count_builder->groupStart();
            $count_builder->orLike('e.id', $search)
                ->orLike('e.vendor_id', $search)
                ->orLike('e.expenses_id', $search)
                ->orLike('e.note', $search)
                ->orLike('e.expenses_date', $search)
                ->orLike('e.amount', $search)
                ->orLike('e.business_id', $search);
            $count_builder->groupEnd();
        }
        $total = $count_builder->get()->getRowArray();

        // Return structured data
        return [
            'total' => $total['total'] ?? 0,
            'data' => $expenses
        ];
    }
}
