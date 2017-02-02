<?php

namespace App\Models;

use CodeIgniter\Model;

class Vendor_purchase_transactions_model extends Model
{

    protected $table = 'vendor_purchase_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'supplier_id', 'order_id', 'transaction_type', 'order_type', 'created_by', 'payment_type', 'amount'];

    public function get_transactions($created_by)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("vendor_purchase_transactions as vt");
        $builder->select('vt.*, vt.transaction_type as type, u.first_name');
        $builder->join('users as u', 'vt.created_by = u.id', 'left');
        $builder->where('vt.created_by', $created_by);

        // Optional filters
        if (!empty($_GET['type'])) {
            $type = $_GET['type'];
            if (in_array($type, ['0', 'credit', 'debit'])) {
                $builder->where('vt.transaction_type', $type);
            }
        }

        // Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart();
            $builder->orLike('vt.id', $search)
                ->orLike('vt.transaction_type', $search)
                ->orLike('vt.payment_type', $search)
                ->orLike('vt.amount', $search)
                ->orLike('u.first_name', $search)
                ->orLike('vt.created_by', $search);
            $builder->groupEnd();
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Pagination
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : '';
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : '';

        // Clone for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // do not reset query

        // Apply limit
        if (!empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Execute final query
        $transactions = $builder->orderBy($sort, $order)->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $transactions
        ];
    }
}
