<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers_model extends Model
{

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id', 'vendor_id', 'balance', 'created_by', 'status'];

    public function get_users($search_term = "", $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("users as u");
        $builder->select('u.*,c.user_id,c.balance');
        $builder->where('c.business_id ', $business_id);
        $builder->join('customers as c', 'u.id = c.user_id ', "left");
        $multipleWhere = [];
        if (!empty($search_term)) {
            $multipleWhere = [
                'u.`id`' => $search_term,
                'u.`first_name`' => $search_term,
                'u.`mobile`' => $search_term,
                'u.`email`' => $search_term,
                'c.`balance`' => $search_term,

            ];
        }
        $builder->groupStart();
        $builder->orLike($multipleWhere);
        $builder->groupEnd();
        $users = $builder->get()->getResultArray();
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['first_name'], "number" => $user['mobile'], "email" => $user['email'], "balance" => $user['balance']);
        }
        $response['data'] = $data;
        return json_encode($response);
    }
    public function get_customer($user_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("customers");
        $builder->where('user_id ', $user_id);

        return $builder->get()->getResultArray();
    }
    public function get_customers_details($business_id = "", $user_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("customers as c");
        $builder->select('c.*, u.first_name, u.email, u.mobile, u.last_name, u.address');
        $builder->join('users as u', 'c.user_id = u.id', 'left');

        // Apply filters
        if (!empty($business_id)) {
            $builder->where('c.business_id', $business_id);
        }

        if (!empty($user_id)) {
            $builder->where('c.user_id', $user_id);
        }

        // Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('c.balance', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.email', $search)
                ->groupEnd();
        }

        // Sort
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';

        // Clone builder for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // Do not reset builder state

        // Apply limit
        if (!empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        $customers = $builder->orderBy($sort, $order)->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $customers
        ];
    }

}
