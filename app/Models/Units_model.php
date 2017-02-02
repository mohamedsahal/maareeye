<?php

namespace App\Models;

use CodeIgniter\Model;

class Units_model extends Model
{

    protected $table = 'units';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'parent_id', 'name', 'symbol', 'conversion'];

    // for all vendor+admin units
    public function get_units_for_forms($vendor_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("units")
            ->select('*')
            ->whereIn('vendor_id', [0, $vendor_id]);
        return $builder->get()->getResultArray();
    }
    public function unit_name($unit_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("units")
            ->select('name')
            ->whereIn('id', [$unit_id]);
        return $builder->get()->getResultArray();
    }
    // for pagination 
    public function get_units($vendor_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("units");

        // Handle vendor_id condition
        if (!empty($vendor_id)) {
            $builder->whereIn('vendor_id', [0, $vendor_id]);
        }

        // Search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('id', $search)
                ->orLike('vendor_id', $search)
                ->orLike('parent_id', $search)
                ->orLike('name', $search)
                ->orLike('symbol', $search)
                ->orLike('conversion', $search)
                ->groupEnd();
        }

        // Clone builder before applying limit/order for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // false = don't reset query

        // Pagination
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';
        $builder->orderBy($sort, $order);

        // Fetch data
        $units = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $units
        ];
    }
}
