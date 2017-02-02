<?php

namespace App\Models;

use CodeIgniter\Model;

class Businesses_model extends Model
{

    protected $table = 'businesses';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'name', 'icon', 'description', 'address', 'contact', 'tax_name', 'tax_value', 'bank_details', 'status', 'website', 'email'];

    public function get_businesses($user_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("businesses");

        $builder->where('user_id', $user_id);

        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $sort = isset($_GET['sort']) && !empty(trim($_GET['sort'])) ? trim($_GET['sort']) : "id";
        $order = isset($_GET['order']) && in_array(strtoupper($_GET['order']), ['ASC', 'DESC']) ? $_GET['order'] : "ASC";

        $multipleWhere = [];
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'id' => $search,
                'user_id' => $search,
                'name' => $search,
                'icon' => $search,
                'description' => $search,
                'address' => $search,
                'contact' => $search,
                'tax_name' => $search,
                'tax_value' => $search,
                'bank_details' => $search,
                'email' => $search,
                'website' => $search,
            ];
        }

        if (!empty($multipleWhere)) {
            $builder->groupStart();
            foreach ($multipleWhere as $key => $value) {
                $builder->orLike($key, $value);
            }
            $builder->groupEnd();
        }

        // Clone the builder for counting
        $countBuilder = clone $builder;
        $total = $countBuilder->select('COUNT(id) as total')->get()->getRow()->total;

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Fetch actual data
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
