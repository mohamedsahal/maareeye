<?php

namespace App\Models;

use CodeIgniter\Model;

class Vendors_model extends Model
{

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'mobile', 'email', 'first_name', 'last_name', 'active', 'created_on'];



    // Inside your Vendors_model class
    public function getVendorByUserId($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("users");
        $builder->select('*');
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }

    public function get_vendors()
    {
        $group = get_group('vendors');
        $group_id = $group[0]['group_id'] ?? null;

        if (!$group_id) {
            return ['total' => 0, 'data' => []];
        }

        $db = \Config\Database::connect();
        $builder = $db->table("users as u");
        $builder->select('u.*, ug.group_id');
        $builder->join('users_groups as ug', 'u.id = ug.user_id', 'left');
        $builder->where('ug.group_id', $group_id);

        // Handle search
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('u.id', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->orLike('u.email', $search)
                ->orLike('u.mobile', $search)
                ->orLike('u.active', $search)
                ->groupEnd();
        }

        // Clone for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->select('COUNT(DISTINCT u.id) as total')->get()->getRow()->total ?? 0;

        // Pagination and ordering
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';
        $sort = $_GET['sort'] ?? 'u.id';
        $order = strtoupper($_GET['order'] ?? 'ASC');

        // Apply pagination and ordering
        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        $data = $builder->orderBy($sort, $order)->get()->getResultArray();

        return [
            'total' => (int) $total,
            'data' => $data
        ];
    }
    public function edit_profile($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("users");
        $builder->select('*');
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }
}
