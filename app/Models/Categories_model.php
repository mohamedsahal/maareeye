<?php

namespace App\Models;

use CodeIgniter\Model;

class Categories_model extends Model
{

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['parent_id', 'vendor_id', 'name', 'status', 'business_id'];


    public function get_categories($vendor_id = "", $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("categories");

        // Filter by general name and business_id logic
        if (!empty($vendor_id)) {
            $builder->groupStart()
                ->where('name', 'general')
                ->orWhereIn('business_id', [0, $business_id])
                ->groupEnd();
        } else {
            $builder->where('business_id', 0);
        }

        // Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('id', $search)
                ->orLike('vendor_id', $search)
                ->orLike('parent_id', $search)
                ->orLike('name', $search)
                ->orLike('status', $search)
                ->groupEnd();
        }

        // Clone builder before limit & sort for count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // do not reset

        // Pagination
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Sort
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';
        $builder->orderBy($sort, $order);

        $categories = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $categories
        ];
    }
}
