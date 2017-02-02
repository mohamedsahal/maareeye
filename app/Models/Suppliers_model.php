<?php

namespace App\Models;

use CodeIgniter\Model;

class Suppliers_model extends Model
{

    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'vendor_id', 'balance', 'billing_address', 'shipping_address', 'credit_period', 'credit_limit', 'tax_name', 'tax_num', 'status'];


    public function edit_supplier($supplier_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('s.*,s.id as sup_id,u.id,u.first_name,u.email,u.mobile');
        $builder->where('s.user_id ', $supplier_id);
        $builder->join('users as u', 'u.id = s.user_id ', "left"); // added left here
        return $builder->get()->getResultArray();
    }
    public function get_suppliers($vendor_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('s.balance, s.status, u.id, u.first_name as name, u.email, u.mobile, s.user_id')
            ->join('users as u', 'u.id = s.user_id', 'left')
            ->where('s.vendor_id', $vendor_id);

        // Pagination and sorting
        $offset = (int) ($_GET['offset'] ?? '');
        $limit = (int) ($_GET['limit'] ?? '');
        $sort = $_GET['sort'] ?? 'u.id';
        $order = $_GET['order'] ?? 'ASC';

        // Search filters
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('s.user_id', $search)
                ->orLike('s.balance', $search)
                ->orLike('u.email', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->orLike('u.mobile', $search)
                ->orLike('s.billing_address', $search)
                ->orLike('s.shipping_address', $search)
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

    public function search_suppliers($search_term = "", $vendor_id = "")
    {
        // connect to the database
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('u.id,u.first_name,s.vendor_id,s.balance');

        $builder->join('users as u', 'u.id = s.user_id ', "left");
        if (!empty($search_term)) {
            $multipleWhere = [
                'u.`id`' => $search_term,
                'u.`first_name`' => $search_term,
                's.`balance`' => $search_term,
            ];
        }
        $builder->groupStart();
        $builder->orLike($multipleWhere);
        $builder->groupEnd();
        $users = $builder->get()->getResultArray();

        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['first_name'], "balance" => $user['balance']);
        }
        $response['data'] = $data;
        return json_encode($response);
    }
}
