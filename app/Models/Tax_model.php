<?php

namespace App\Models;

use CodeIgniter\Model;

class Tax_model extends Model
{

    protected $table = 'tax';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'percentage', 'status'];

    public function count_of_tax()
    {
        $db = \Config\Database::connect();
        $builder = $db->table("tax");
        $builder->select('COUNT(tax.id) as `total`');
        $tax = $builder->get()->getResultArray();
        return $tax;
    }

    public function get_taxes()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tax');

        // Handle pagination
        $limit = (int) ($_GET['limit'] ?? '');
        $offset = (int) ($_GET['offset'] ?? '');

        // Handle sorting
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'DESC';

        // Handle search
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('name', $search)
                ->orLike('percentage', $search)
                ->groupEnd();
        }

        // Clone for total before adding limit/offset
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        // Get paginated records
        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        $taxes = $builder->orderBy($sort, $order)->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $taxes
        ];
    }
}