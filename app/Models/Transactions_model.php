<?php

namespace App\Models;

use CodeIgniter\Model;

class Transactions_model extends Model
{

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'amount', 'txn_id', 'payment_method', 'status', 'message'];

    public function get_transactions($user_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("transactions as t");
        $builder->select('t.*, u.first_name, u.last_name, u.email, u.mobile, up.package_id, p.title');
        $builder->join('users as u', 't.user_id = u.id', 'left');
        $builder->join('users_packages as up', 't.id = up.transaction_id', 'left');
        $builder->join('packages as p', 'up.package_id = p.id', 'left');

        if (!empty($user_id)) {
            $builder->where('t.user_id', $user_id);
        }

        // Search filter
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('u.first_name', $search)
                ->orLike('u.last_name', $search)
                ->orLike('u.email', $search)
                ->orLike('u.phone', $search)
                ->orLike('t.id', $search)
                ->orLike('t.payment_method', $search)
                ->orLike('t.txn_id', $search)
                ->orLike('t.amount', $search)
                ->orLike('t.status', $search)
                ->orLike('t.created_at', $search)
                ->groupEnd();
        }

        // Date range filter
        if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
            $builder->where("t.created_at >=", $_GET['start_date'] . ' 00:00:00');
            $builder->where("t.created_at <=", $_GET['end_date'] . ' 23:59:59');
        }

        // Status filter
        if (!empty($_GET['transaction_status'])) {
            $status = $_GET['transaction_status'];
            if ($status === 'success') {
                $builder->groupStart()
                    ->where('t.status', 'success')
                    ->orWhere('t.status', 'successful')
                    ->orWhere('t.status', 'authorized')
                    ->orWhere('t.status', 'captured')
                    ->groupEnd();
            } elseif (in_array($status, ['failed', 'pending'])) {
                $builder->where('t.status', $status);
            }
        }

        // Payment method filter
        if (!empty($_GET['txn_provider'])) {
            $builder->where('t.payment_method', $_GET['txn_provider']);
        }

        // Clone for total count before adding limit/offset
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        // Pagination and Sorting
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';
        $sort = $_GET['sort'] ?? 't.id';
        $order = $_GET['order'] ?? 'DESC';

        $builder->orderBy($sort, $order);

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        $transactions = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $transactions
        ];
    }
}
