<?php

namespace App\Models;

use CodeIgniter\Model;

class Status_model extends Model
{

    protected $table = 'status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'business_id', 'status', 'operation'];

    public function get_status($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("status");
        $builder->whereIn('business_id', [$business_id, '0']);
        return $builder->get()->getResultArray();
    }

    public function get_status_list($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("status as s");
        $builder->select('s.*')

            ->where('s.business_id', $business_id);

        // Pagination and sorting
        $offset = (int) ($_GET['offset'] ?? '');
        $limit = (int) ($_GET['limit'] ?? '');
        $sort = $_GET['sort'] ?? 'u.id';
        $order = $_GET['order'] ?? 'ASC';

        // Search filters
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('s.status', $search)
                ->groupEnd();
        }

        // Clone builder before limit to get total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // false keeps existing query

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Apply sorting and pagination
        $suppliers = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        return [
            'total' => $total,
            'data' => $suppliers
        ];
    }
}
