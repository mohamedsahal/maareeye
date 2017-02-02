<?php

namespace App\Models;

use CodeIgniter\Model;

class Products_model extends Model
{

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'category_id', 'brand_id', 'business_id', 'vendor_id', 'warehouse_id', 'tax_ids', 'name', 'description', 'image', 'type', 'stock_management', 'stock', 'unit_id', 'qty_alert', 'is_tax_included', 'status'];

    public function get_product_details($business_id = '', $flag = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("products as p");
        $builder->select('p.id,pv.id,pv.stock ,pv.qty_alert,pv.product_id,pv.variant_name,p.name as product,p.stock_management,p.stock,p.qty_alert,p.business_id , p.image');
        $builder->where('p.business_id', $business_id);
        $builder->whereIn('p.stock_management', [1, 2]);
        $builder->join('products_variants as pv', 'p.id = pv.product_id ', "left"); // added left here
        $offset = 0;
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }

        if (isset($flag) && $flag == "out") {
            $builder->where('((p.stock = 0) AND (pv.stock = 0 ))');
        }
        if (isset($flag) && $flag == "low") {
            $builder->where('((p.stock < p.qty_alert AND p.stock > 0 AND p.stock_management = "1" ) OR (pv.stock < pv.qty_alert AND pv.stock > 0 AND p.stock_management = "2")) ');
            $builder->groupBy('p.id');
        }

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 20;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`p.name`' => $search,
                '`pv.variant_name`' => $search,
                '`pv.stock`' => $search,
                '`p.stock`' => $search,
            ];
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        $sort = "p.id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'p.id') {
                $sort = "p.id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        $products = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $products;
    }
    public function get_products($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products');

        // Get input parameters
        $category_id = $_GET['category_id'] ?? '';
        $brand_id = $_GET['brand_id'] ?? '';
        $limit = (int) ($_GET['limit'] ?? '');
        $offset = (int) ($_GET['offset'] ?? '');
        $sort = $_GET['sort'] ?? 'id';
        $order = strtoupper($_GET['order'] ?? 'DESC');
        $search = $_GET['search'] ?? '';

        // Base query
        $builder->select('id, name, description, image, type, stock_management, stock, qty_alert, unit_id, is_tax_included, status, category_id, tax_ids, vendor_id')
            ->where('business_id', $business_id)
            ->where('status', 1);

        // Filters
        if (!empty($category_id)) {
            $builder->where('category_id', $category_id);
        }

        if (!empty($brand_id)) {
            $builder->where('brand_id', $brand_id);
        }

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('description', $search)
                ->orLike('id', $search)
                ->groupEnd();
        }

        // Clone builder for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->select('COUNT(id) as total')->get()->getRow()->total;

        // Paginated data
        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }
        $data = $builder->orderBy($sort, $order)
            ->get()
            ->getResultArray();

        return [
            'total' => (int) $total,
            'data' => $data
        ];
    }

    public function edit_product($product_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $this->db->table("products as p");
        $builder->select('p.*,pv.id as product_variant_id,pv.product_id,pv.variant_name,pv.sale_price,pv.purchase_price,pv.stock,pv.unit_id');
        $builder->where('p.id', $product_id);
        $builder->join('products_variants as pv', 'p.id = pv.product_id ', "left"); // added left here
        return $builder->get()->getResultArray();
    }
}
