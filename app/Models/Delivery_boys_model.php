<?php

namespace App\Models;

use CodeIgniter\Model;

class Delivery_boys_model extends Model
{

    protected $table = 'delivery_boys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id', 'vendor_id', 'status', 'permissions'];

    public function assigned_businesses($user_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("delivery_boys");
        $builder->select('business_id');
        $builder->where('user_id', $user_id);
        $business_ids = $builder->get()->getResultArray();
        return $business_ids;
    }
    public function delivery_boys($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("delivery_boys as db");
        $builder->select('db.*,u.first_name,u.email,u.mobile,u.last_name,u.username');
        $builder->where('business_id ', $business_id);
        $builder->join('users as u', 'db.user_id = u.id ', "left");
        return $builder->get()->getResultArray();
    }
    public function get_delivery_boys($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("delivery_boys as db");
        $builder->select('db.*, u.first_name, u.email, u.mobile, u.last_name, u.username');
        $builder->join('users as u', 'db.user_id = u.id', 'left');
        $builder->where('db.business_id', $business_id);

        // Search filter
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart();
            $builder->orLike('u.id', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->orLike('u.email', $search)
                ->orLike('u.mobile', $search)
                ->orLike('u.active', $search);
            $builder->groupEnd();
        }

        // Get total before applying limit
        $total = $builder->countAllResults(false); // Don't reset builder

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'u.id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $builder->orderBy($sort, $order);

        // Fetch data
        $data = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $data,
        ];
    }
}
