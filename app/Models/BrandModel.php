<?php

namespace App\Models;

use CodeIgniter\Model;

class BrandModel extends Model
{
    protected $table = 'brands';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'business_id', 'vendor_id', 'name', 'description', 'created_at', 'updated_at', 'deleted_at'];

    public function get_brands($business_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table('brands');

        // Get query parameters
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $sort = isset($_GET['sort']) && !empty(trim($_GET['sort'])) ? trim($_GET['sort']) : "id";
        $order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? $_GET['order'] : "ASC";
        $search = $_GET['search'];
        // Base filter
        $builder->where('business_id', $business_id);

        // Search filter
        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('description', $search)
                ->groupEnd();
        }

        // Clone builder to get total count
        $countBuilder = clone $builder;
        $total = $countBuilder->select('COUNT(id) as total')->get()->getRow()->total;

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Fetch data with limit/offset/sorting
        $data = $builder
            ->select('*')
            ->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        return [
            'total' => (int) $total,
            'data' => $data
        ];
    }
}
