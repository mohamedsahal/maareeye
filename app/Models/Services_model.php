<?php

namespace App\Models;

use CodeIgniter\Model;

class Services_model extends Model
{

    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'business_id', 'tax_ids', 'name', 'unit_id', 'description', 'image', 'price', 'cost_price', 'is_tax_included', 'is_recursive', 'recurring_days', 'recurring_price', 'status'];

    public function fetch_services($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("services as s");
        $builder->select('s.*, b.name as business_name');
        $builder->join('businesses as b', 'b.id = s.business_id', "left");
        $builder->where('s.business_id', $business_id);

        // Search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart();
            $builder->orLike('s.id', $search)
                ->orLike('s.name', $search)
                ->orLike('s.price', $search)
                ->orLike('s.cost_price', $search)
                ->orLike('s.unit_', $search)
                ->orLike('b.name', $search);
            $builder->groupEnd();
        }

        // Optional single ID filter
        if (!empty($_GET['id'])) {
            $builder->where('s.id', $_GET['id']);
        }

        // Clone builder for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // don’t reset builder

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';

        // Sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Final query execution
        $builder->orderBy($sort, $order);
        $services = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $services
        ];
    }

}