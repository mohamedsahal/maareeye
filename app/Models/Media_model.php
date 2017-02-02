<?php

namespace App\Models;

use CodeIgniter\Model;

class Media_model extends Model
{

    protected $table = 'media';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'title', 'name', 'extension', 'type', 'sub_directory', 'size'];
    public function get_media($vendor_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("media as m");
        $builder->select('m.*')
            ->where('m.vendor_id', $vendor_id);

        // Pagination and sorting
        $offset = (int) ($_GET['offset'] ?? '');
        $limit = (int) ($_GET['limit'] ?? '');
        $sort = $_GET['sort'] ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Search filters
        $search = $_GET['search'] ?? '';
        if (!empty($search)) {
            $builder->groupStart()
                ->orLike('m.title', $search)
                ->orLike('m.name', $search)
                ->orLike('m.extension', $search)
                ->orLike('m.extension', $search)
                ->orLike('m.type', $search)
                ->orLike('m.sub_directory', $search)
                ->orLike('m.size', $search)
                ->groupEnd();
        }

        // Clone builder before limit to get total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // false keeps existing query

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        // Apply sorting and pagination
        $media = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        return [
            'total' => $total,
            'data' => $media
        ];
    }

}
