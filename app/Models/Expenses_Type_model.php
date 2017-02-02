<?php

namespace App\Models;

use CodeIgniter\Model;

class Expenses_Type_model extends Model
{

    protected $table = 'expenses_type';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'vendor_id', 'title', 'description'];

    public function get_expenses_type($vendor_id = "")
    {
        

        $db = \Config\Database::connect();
        $builder = $db->table('expenses_type et');

        // Fetch records where vendor_id is 0 or specific vendor

        
        if (!empty($vendor_id)) {
            $builder->whereIn('vendor_id', [0, $vendor_id]);
        }

        // Search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'et.id' => $search,
                'et.vendor_id' => $search,
                'et.title' => $search,
                'et.description' => $search,
                // 'et.expenses_type_date' => $search,
            ];
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Count total records
        $count_builder = clone $builder;
        $total = $count_builder->select('COUNT(et.id) as total')->get()->getRowArray()['total'] ?? 0;

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';

        if (!empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Final data fetch
        $expenses_type = $builder
            ->select('et.*')
            ->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        return [
            'total' => $total,
            'data' => $expenses_type
        ];

    }
}