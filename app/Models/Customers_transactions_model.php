<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers_transactions_model extends Model
{

    protected $table = 'customers_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'customer_id', 'supplier_id', 'order_id', 'transaction_type', 'order_type', 'created_by', 'payment_type', 'amount', 'opening_balance', 'closing_balance', 'message', 'transaction_id'];

    public function get_transactions($created_by)
    {

        $db = \Config\Database::connect();
        $builder = $db->table("customers_transactions as ct");
        $builder->select('ct.*, ct.transaction_type as type, u.first_name');
        $builder->join('users as u', 'ct.created_by = u.id', 'left');
        $builder->where('ct.created_by', $created_by);

        // Filter by type (wallet/sale)
        if (!empty($_GET['type'])) {
            $type = $_GET['type'];
            if ($type == 'wallet') {
                $builder->where('ct.order_id', 0);
            } elseif ($type == 'sale') {
                $builder->where('ct.order_id !=', 0);
            }
        }

        // Search logic
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart();
            $builder->orLike('ct.id', $search)
                ->orLike('ct.transaction_type', $search)
                ->orLike('ct.payment_type', $search)
                ->orLike('ct.amount', $search)
                ->orLike('ct.opening_balance', $search)
                ->orLike('ct.closing_balance', $search)
                ->orLike('ct.message', $search)
                ->orLike('ct.created_by', $search)
                ->orLike('u.first_name', $search);
            $builder->groupEnd();
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Pagination
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';

        // Clone builder to get total count
        $count_builder = clone $builder;
        $total = $count_builder->countAllResults(false); // Do not reset the builder

        // Limit
        if ($limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Execute
        $transactions = $builder->orderBy($sort, $order)->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $transactions
        ];
    }
}
