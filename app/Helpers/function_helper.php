<?php

use App\Libraries\Flutterwave;

/**
 * Returns the app display name, normalizing legacy "Maareeye" to "Maareeye".
 */
function get_app_display_name($title = '')
{
    $title = trim($title ?? '');
    if (empty($title) || in_array(strtolower($title), ['Maareeye', 'up biz'])) {
        return 'Maareeye';
    }
    return $title;
}
use App\Models\Units_model;
use App\Libraries\Razorpay;



/**
 * Updates the stock of a product variant in a warehouse.
 * 
 * If type = 0, it performs a subtraction ('-') from the stock.
 * If type = 1, it performs an addition ('+') to the stock.
 * 
 * @param {int} $warehouse_id         The ID of the warehouse.
 * @param {int} $product_variant_id   The ID of the product variant.
 * @param {int} $warehouse_stock      The amount to adjust the stock by.
 * @param {int|null} $type            The type of operation: 0 for subtraction, 1 for addition. Default is null.
 */

function updateWarehouseStocks($warehouse_id, $product_variant_id, $warehouse_stock, $type)
{

    $db = \Config\Database::connect();
    $result = $db->table('warehouse_product_stock')
        ->where([
            'warehouse_id' => $warehouse_id,
            'product_variant_id' => $product_variant_id
        ])
        ->get()->getResultArray();


    for ($i = 0; $i < count($result); $i++) {
        $stock = intval($result[$i]['stock']);

        if ($type == 1) {
            $stock += intVal($warehouse_stock);
        } else {
            $stock -= intVal($warehouse_stock);
        }


        $db->table('warehouse_product_stock')->where([
            'warehouse_id' => $warehouse_id,
            'product_variant_id' => $product_variant_id,

        ])->update(['stock' => $stock, 'updated_at' => date('Y-m-d H:i:s')]);
    }


}
function userHasPermission($moduleName, $action, $user_id)
{

    $maareeyeConfig = new \Config\Maareeye();
    $all_permissions = $maareeyeConfig->permissions;

    $userPermissions = get_user_permissions($user_id);

    if (!empty($userPermissions)) {
        $userPermissions = json_decode($userPermissions[0]['permissions'], true);
        if (
            isset($userPermissions["'$moduleName'"]) &&
            in_array($action, $all_permissions[$moduleName]) &&
            in_array("'$action'", $userPermissions["'$moduleName'"])
        ) {
            return true;
        }
    }

    return false;
}

function get_vendor_for_teamMember($user_id)
{
    $team_member = fetch_details('team_members', ['user_id' => $user_id]);
    $vendor_id = $team_member[0]['vendor_id'];
    return $vendor_id;
}
function get_user_permissions($user_id)
{
    return fetch_details('team_members', ['user_id' => $user_id]);
}
function is_team_member($user_id)
{
    $team_member = fetch_details('team_members', ['user_id' => $user_id]);
    return !empty($team_member);
}
function get_packages($package_id = "")
{
    $where = [];
    if (!empty($package_id) && is_numeric($package_id)) {
        $where['id'] = $package_id;
    }
    return fetch_details('packages', $where, [], '', '', '', 'DESC');
}
function get_tenures($package_id = "", $tenure_id = "")
{
    $where = [];
    if (!empty($package_id) && is_numeric($package_id)) {
        $where['package_id'] = $package_id;
    }
    if (!empty($tenure_id) && is_numeric($tenure_id)) {
        $where['id'] = $tenure_id;
    }
    return fetch_details('packages_tenures', $where, [], '', '', '', 'DESC');
}

function fetch_details($table, $where = [], $fields = [], $limit = '', $offset = '0', $sort = 'id', $order = 'ASC', $where_in_key = '', $where_in_value = '', $or_like = [])
{
    $db = \Config\Database::connect();
    $builder = $db->table($table);
    if (!empty($fields)) {
        $builder = $builder->select($fields);
    }
    if (!empty($where)) {
        $builder = $builder->where($where)->select($fields);
    }
    if (!empty($where_in_key) && !empty($where_in_value)) {
        $builder = $builder->whereIn($where_in_key, $where_in_value);
    }

    if (isset($or_like) && !empty($or_like)) {
        $builder->groupStart();
        $builder->orLike($or_like);
        $builder->groupEnd();
    }
    if ($limit != null && $limit != "") {
        $builder = $builder->limit($limit, $offset);
    }

    $builder = $builder->orderBy($sort, $order);
    $res = $builder->get()->getResultArray();
    return $res;
}
function get_plans_tenures($package_id = "")
{

    $packages = get_packages($package_id);
    $tenures = get_tenures($package_id);

    for ($i = 0; $i < count($packages); $i++) {
        $packages[$i]['tenures'] = array();
        for ($j = 0; $j < count($tenures); $j++) {
            if ($packages[$i]['id'] == $tenures[$j]['package_id']) {
                array_push($packages[$i]['tenures'], $tenures[$j]);
            }
        }
    }
    return $packages;
}
function get_plans_tenures_edit($id)
{
    $packages = fetch_details('packages', ['id' => $id]);
    $tenures = fetch_details('packages_tenures', ['package_id' => $id]);
    for ($i = 0; $i < count($packages); $i++) {
        $packages[$i]['tenures'] = array();
        for ($j = 0; $j < count($tenures); $j++) {
            if ($packages[$i]['id'] == $tenures[$j]['package_id']) {
                array_push($packages[$i]['tenures'], $tenures[$j]);
            }
        }
    }
    return $packages;
}

function get_purchase_items($purchase_id)
{
    $purchase = fetch_details("purchases", ["id" => $purchase_id]);
    $purchase_items = fetch_details("purchases_items", ['purchase_id' => $purchase_id]);
    for ($i = 0; $i < count($purchase); $i++) {
        $purchase[$i]['items'] = array();
        for ($j = 0; $j < count($purchase_items); $j++) {
            if ($purchase[$i]['id'] == $purchase_items[$j]['purchase_id']) {
                array_push($purchase[$i]['items'], $purchase_items[$j]);
            }
        }
    }
    return $purchase;
}
function get_products_with_variants($product_id)
{

    $products = fetch_details('products', ['id' => $product_id]);
    $products_variants = fetch_details('products_variants', ['product_id' => $product_id]);
    for ($i = 0; $i < count($products); $i++) {
        $products[$i]['variants'] = array();
        for ($j = 0; $j < count($products_variants); $j++) {
            if ($products[$i]['id'] == $products_variants[$j]['product_id']) {
                array_push($products[$i]['variants'], $products_variants[$j]);
            }
        }
    }
    return $products;
}
function get_supplier($supplier_id)
{
    return fetch_details('users', ['id' => $supplier_id], ['first_name'])[0]['first_name'];
}

function get_variant_name($product_variant_id)
{
    return fetch_details('products_variants', ['id' => $product_variant_id], 'variant_name')[0]['variant_name'];
}
function status_name($id)
{
    return fetch_details('status', ['id' => $id], 'status')[0]['status'];
}

function get_product_image($variant_id)
{
    $product_id = fetch_details('products_variants', ['id' => $variant_id], 'product_id')[0]['product_id'];
    $image = fetch_details('products', ['id' => $product_id], 'image')[0]['image'];


    if (file_exists($image)) {
        $image_path = base_url($image);
    } else {
        $image_path = base_url(relativePath: 'public/backend/assets/img/no-image.jpg');
    }
    return $image_path;
}

function category_name($category_id)
{
    $category = fetch_details('categories', ['id' => $category_id], ['name'])[0]['name'];
    return $category;
}


function get_products_of_business($business_id = "", $limit = "20", $offset = '0', $search = "")
{
    $multipleWhere = [];
    if (!empty($search)) {
        $multipleWhere = [
            '`id`' => $search,
            '`category_id`' => $search,
            '`business_id`' => $search,
            '`tax_id`' => $search,
            '`name`' => $search,
            '`description`' => $search,
            '`image`' => $search,
            '`type`' => $search,
            '`stock_management`' => $search,
            '`stock`' => $search,
            '`unit_id`' => $search,
            '`is_tax_included`' => $search,
            '`status`' => $search,
        ];
    }
    $products = fetch_details('products', ['business_id' => $business_id], [], $limit, $offset, '', 'DECS', '', '', $multipleWhere);
    for ($i = 0; $i < count($products); $i++) {
        $product_id = isset($products[$i]['id']) ? $products[$i]['id'] : "";
        $products_variants = fetch_details('products_variants', ['product_id' => $product_id]);
        $products[$i]['variants'] = array();
        for ($j = 0; $j < count($products_variants); $j++) {
            if ($products[$i]['id'] == $products_variants[$j]['product_id']) {
                array_push($products[$i]['variants'], $products_variants[$j]);
            }
        }
    }

    return $products;
}
function escape_array($array)
{
    $db = \Config\Database::connect();
    $posts = array();
    if (!empty($array)) {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                $posts[$key] = $db->escapeString((string) $value);
            }
        } else {
            return $db->escapeString($array);
        }
    }
    return $posts;
}



// CI3→CI4 upgrade: Replaced get_instance(). DB access via \Config\Database::connect()
$CI_INSTANCE = [];  # Legacy ref - kept for optional controller access

function register_ci_instance(\App\Controllers\BaseController &$_ci)
{
    global $CI_INSTANCE;
    $CI_INSTANCE[0] = &$_ci;
}

function update_details($set, $where, $table, $escape = true)
{
    $db = \Config\Database::connect();
    $db->transStart();
    if ($escape) {
        $set = escape_array($set);
    }
    $db->table($table)->update($set, $where);
    $db->transComplete();
    $response = FALSE;
    if ($db->transStatus() === TRUE) {
        $response = TRUE;
    }
    return $response;
}
function get_group($name = "")
{
    $db = \Config\Database::connect();
    $builder = $db->table("groups as g");
    $builder->select('ug.*,g.name');
    $builder->where('g.name', $name);
    $builder->join('users_groups as ug', 'g.id = ug.group_id ', "left");
    $group = $builder->get()->getResultArray();
    return $group;
}

function check_package_status($start_date, $end_date, $date = "")
{
    $status = "";
    $date = ($date != "") ? $date : date("Y-m-d");
    if ($date >= $start_date && $date <= $end_date) {
        return $status = "1";
    }
    if ($date < $start_date && $start_date < $end_date) {
        return $status = "1";
    }
    if ($end_date < $date) {

        return $status = "0";
    }

    return $status;
}

function exists($where, $table)
{
    $db = \Config\Database::connect();
    $builder = $db->table($table);
    $builder = $builder->where($where);
    $res = count($builder->get()->getResultArray());
    if ($res > 0) {
        return true;
    } else {
        return false;
    }
}

function find_days($start_date = "", $end_date = "")
{
    $start_date = strtotime($start_date); // or your date as well
    $end_date = strtotime($end_date); // or your date as well
    $datediff = $start_date - $end_date;
    return round($datediff / (60 * 60 * 24));
}

function get_settings($type = "", $is_json = false)
{
    $settings = fetch_details("settings", ['variable' => $type]);
    if (isset($settings[0]['value']) && !empty($settings[0]['value'])) {
        return ($is_json) ? json_decode($settings[0]['value'], true) : $settings[0]['value'];
    } else {
        return false;
    }
}
function check_data_in_table($table, $id)
{
    $db = \Config\Database::connect();
    $session = \Config\Services::session();
    $builder = $db->table($table)
        ->where('id', $id);
    $row = $builder->get()->getRow();
    if (!isset($row->id)) {
        $array_items = ['business_id', 'business_name'];
        $session->remove($array_items);
    } else {
        return false;
    }
}

function fetch_stock($business_id = '', $where_in_key = '', $where_in_value = [], $where = [], $or_like = [], $limit = '', $offset = '')
{
    $db = \Config\Database::connect();
    $builder = $db->table("products as p");

    $builder->select('p.image, p.id, p.stock_management, p.name, p.stock as product_stock, pv.id as variant_id, pv.variant_name, pv.stock as variant_stock');
    $builder->join('products_variants as pv', 'p.id = pv.product_id', 'left');
    $builder->where('p.business_id', $business_id);

    if (!empty($where)) {
        $builder->where($where);
    }

    if (!empty($where_in_key) && !empty($where_in_value)) {
        $builder->whereIn($where_in_key, $where_in_value);
    }

    // Handle search via GET or via parameter
    if (isset($_GET['search']) && $_GET['search'] !== '') {
        $search = $_GET['search'];
        $or_like['p.name'] = $search;
    }

    if (!empty($or_like)) {
        $builder->groupStart();
        foreach ($or_like as $key => $val) {
            $builder->orLike($key, $val);
        }
        $builder->groupEnd();
    }

    // Clone builder before applying limit
    $totalBuilder = clone $builder;
    $total = $totalBuilder->countAllResults(false); // false = don’t reset builder

    // Apply limit & offset
    if (!empty($limit) && $limit > 0) {
        $builder->limit($limit, $offset);
    }

    $res['data'] = $builder->get()->getResultArray();
    $res['total'] = $total;

    return $res;
}

function fetch_products($business_id = "", $category_id = "", $brand_id = "", $search = "", $limit = NULL, $offset = NULL, $sort = "p.id", $order = "DESC", $where_in_key = '', $where_in_value = [], $extra_data = [], $warehouse_id = '')
{

    $db = \Config\Database::connect();

    // Main product query
    $builder = $db->table('products p');

    // Joins
    $builder->select('p.*, SUM(wps.stock) as stock,wps.stock as warehouse_stock,wps.qty_alert as warehouse_qty_alert');
    $builder->join('products_variants pv', 'pv.product_id = p.id', 'left');
    $builder->join('warehouse_product_stock wps', 'wps.product_variant_id = pv.id', 'left');

    // Conditions
    $builder->where('p.business_id', $business_id);
    $builder->where('p.status', '1');

    if (!empty($category_id)) {
        $builder->where('p.category_id', $category_id);
    }

    if (!empty($brand_id)) {
        $builder->where('p.brand_id', $brand_id);
    }
    if (!empty($warehouse_id)) {
        $builder->where('wps.warehouse_id', $warehouse_id);
    }

    if (!empty($extra_data) && isset($extra_data['product_id']) && $extra_data['product_id'] != '') {
        $builder->where('p.id', $extra_data['product_id']);
    }

    if (!empty($search)) {
        $builder->groupStart()
            ->like('p.id', $search)
            ->orLike('p.category_id', $search)
            ->orLike('p.business_id', $search)
            ->orLike('p.tax_ids', $search)
            ->orLike('p.name', $search)
            ->orLike('p.description', $search)
            ->orLike('p.image', $search)
            ->orLike('p.type', $search)
            ->orLike('p.stock_management', $search)
            ->orLike('p.stock', $search)
            ->orLike('p.unit_id', $search)
            ->orLike('p.is_tax_included', $search)
            ->orLike('p.status', $search)
            ->groupEnd();
    }

    if (!empty($where_in_key) && !empty($where_in_value)) {
        $builder->whereIn("p.$where_in_key", $where_in_value);
    }

    // Group by to aggregate stock correctly per product
    $builder->groupBy('p.id');

    // Get total count separately

    $countSubQuery = $db->table('products p')
        ->select('COUNT(DISTINCT p.id) as total')
        ->join('products_variants pv', 'pv.product_id = p.id', 'left')
        ->join('warehouse_product_stock wps', 'wps.product_variant_id = pv.id', 'left')
        ->where('p.business_id', $business_id)
        ->where('p.status', '1');

    if (!empty($category_id))
        $countSubQuery->where('p.category_id', $category_id);
    if (!empty($brand_id))
        $countSubQuery->where('p.brand_id', $brand_id);
    if (!empty($warehouse_id))
        $countSubQuery->where('wps.warehouse_id', $warehouse_id);
    if (!empty($extra_data['product_id']))
        $countSubQuery->where('p.id', $extra_data['product_id']);

    if (!empty($search)) {
        $countSubQuery->groupStart()
            ->like('p.id', $search)
            ->orLike('p.category_id', $search)
            ->orLike('p.business_id', $search)
            ->orLike('p.tax_ids', $search)
            ->orLike('p.name', $search)
            ->orLike('p.description', $search)
            ->orLike('p.image', $search)
            ->orLike('p.type', $search)
            ->orLike('p.stock_management', $search)
            ->orLike('p.stock', $search)
            ->orLike('p.unit_id', $search)
            ->orLike('p.is_tax_included', $search)
            ->orLike('p.status', $search)
            ->groupEnd();
    }

    $totalResult = $countSubQuery->get()->getRowArray();
    $totalCount = $totalResult['total'] ?? 0;

    // Apply sort and limit
    $builder->orderBy($sort, $order);


    if (isset($limit) && !empty($limit) && $limit > 0) {
        $builder->limit($limit, $offset);
    }

    $query = $builder->get();
    $products = $query->getResultArray();



    // Add product variants to each product
    foreach ($products as &$product) {
        $variants = $db->table('products_variants')
            ->where('product_id', $product['id'])
            ->get()
            ->getResultArray();
        $product['variants'] = $variants;

        // Fallback stock if stock_management is off
        if ($product['stock_management'] == '0') {
            $product['stock'] = (int) $product['stock'];
            $product['qty_alert'] = (int) $product['warehouse_qty_alert'];
        } else {
            $product['stock'] = (int) $product['stock']; // already aggregated from join
        }
    }

    return [
        'total' => $totalCount,
        'products' => $products
    ];
}


function product_stock($business_id = "")
{
    $products = fetch_products($business_id, limit: '', offset: '')['products'];

    if (!empty($products)) {
        $response = [];
        $low = 0;
        $out = 0;
        foreach ($products as $product) {
            if ($product['stock_management'] == "1") {
                $stock = $product['stock'];
                if ($stock == "0" || $stock == "") {
                    $out++;
                    $response['out'] = $out;
                    $response['out_stock_product_name'] = $product['name'];
                    $response['message'][] = $product['name'] . " is Out of Stock";
                } elseif ($stock <= $product['qty_alert']) {
                    $low++;
                    $response['low_stock_product_name'] = $product['name'];
                    $response['message'][] = $product['name'] . " is Low in Stock";
                    $response['low'] = $low;
                }
            }
            if ($product['stock_management'] == "2") {
                $variants = $product['variants'];
                if (!empty($variants)) {
                    for ($i = 0; $i < count($variants); $i++) {
                        $stock = $variants[$i]['stock'];
                        if ($stock == "0" || $stock == "") {
                            $response['out_stock_product_name'] = $variants[$i]['variant_name'];
                            $response['message'][] = $variants[$i]['variant_name'] . " is Out of Stock";
                            $out++;
                            $response['out'] = $out;
                        } elseif ($stock <= $variants[$i]['qty_alert']) {
                            $low++;
                            $response['low_stock_product_name'] = $variants[$i]['variant_name'];
                            $response['message'][] = $variants[$i]['variant_name'] . " is Low in Stock";
                            $response['low'] = $low;
                        }
                    }
                }
            }
        }
        return $response;
    }
}
function fetch_services($business_id = "", $search = "", $limit = "10", $offset = "0", $sort = "id", $order = "DESC")
{

    $where['business_id'] = $business_id;
    $multipleWhere = [];
    if (!empty($search)) {
        $multipleWhere = [
            '`id`' => $search,
            '`business_id`' => $search,
            '`vendor_id`' => $search,
            '`tax_ids`' => $search,
            '`unit_id`' => $search,
            '`name`' => $search,
            '`description`' => $search,
            '`image`' => $search,
            '`is_tax_included`' => $search,
            '`price`' => $search,
            '`cost_price`' => $search,
            '`is_recursive`' => $search,
            '`recurring_days`' => $search,
            '`recurring_price`' => $search,
            '`status`' => $search,
        ];
    }

    $services = fetch_details('services', $where, [], $limit, $offset, $sort, $order, '', '', $multipleWhere);
    if (isset($services) && !empty($services)) {
        $units_model = new Units_model();

        for ($i = 0; $i < count($services); $i++) {
            $unit_id = $services[$i]['unit_id'];
            $units = $units_model->unit_name($unit_id);
            if (isset($units[0]['name'])) {

                $unit_name = $units[0]['name'];
                $services[$i]['unit_name'] = $unit_name;
            }
        }
    }
    $total = fetch_details('services', $where, ['COUNT(`id`) as total'], $limit, 0, $sort, $order, '', '', $multipleWhere);
    $response['total'] = (!empty($total[0]['total'])) ? $total[0]['total'] : 0;
    $response['services'] = $services;
    return $response;
}

function check_delivery_boy($mobile)
{
    $delivery_boy = fetch_details('users', ['mobile' => $mobile]);
    $delivery_boy_user_id = $delivery_boy[0]['id'];
    $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $delivery_boy_user_id]);
    foreach ($delivery_boy_businesses as $business) {
        $delivery_boy_business_id[] = $business['business_id'];
        $response['business_id'] = $delivery_boy_business_id;
    }
    $delivery_boy_business_status = $delivery_boy_businesses[0]['status'];
    $response['status'] = $delivery_boy_business_status;
    return $response;
}

function is_delivery_boy_assigned($type = "", $id = "")
{
    if ($type == "product") {
        $order = fetch_details("orders_items", ['id' => $id]);
    }

    if ($type == "service") {
        $order = fetch_details("orders_services", ['id' => $id]);
    }
    return $order;
}

function get_delivery_boy_permission($user_id, $business_id, $permit = NULL)
{
    $permits = fetch_details('delivery_boys', ['user_id' => $user_id, 'business_id' => $business_id], 'permissions');
    if (isset($permits) && !empty($permits)) {
        $s_permits = json_decode($permits[0]['permissions'], true);
        return $s_permits;
    } else {
        return false;
    }
}

function has_upcoming($user_id)
{
    $db = \Config\Database::connect();
    $builder = $db->table('users_packages');
    $builder = $builder->where(['user_id' => $user_id]);
    $result = $builder->get()->getResultArray();


    if (count($result) == 0) {
        return false;
    }
    $type = $db->table('users_packages');
    $arr = $type->select(['start_date', 'end_date', 'tenure', 'id'])->where(['user_id' => $user_id, 'status' => 1])->get()->getResultArray();

    $id = [];
    foreach ($arr as $row) {
        $status = subscription_status($row['id']);

        if ($status == 'upcoming') {
            $id[] = $row['id'];
            return $id;
        }
    }
    return false;
}

function subscription_status($subscription_id)
{
    $db = \Config\Database::connect();
    $row = $db->table('users_packages')->where(['id' => $subscription_id])->get()->getResultArray()[0];

    $starts_from = strtotime($row['start_date']);
    $expiry_date = strtotime($row['end_date']);
    $seconds = $expiry_date - time();

    $status = 'expired';

    if ($seconds > 0) {
        $status = 'active';
    }
    if ($starts_from > time()) {
        $status = 'upcoming';
    }
    if ($row['status'] == 0) {
        $status = 'expired';
    }
    return $status;
}

function labels($label, $alt = '')
{
    $session = session();
    $lang = $session->get('lang');
    $label = trim($label);

    if (empty($lang)) {
        $lang = 'en';
    }

    $translation = lang('Text.' . $label, [], $lang);

    if (!empty($translation) && $translation !== 'Text.' . $label) {
        return trim($translation);
    }

    // Fallback to English when current language has empty translation
    if ($lang !== 'en') {
        $translation = lang('Text.' . $label, [], 'en');
        if (!empty($translation) && $translation !== 'Text.' . $label) {
            return trim($translation);
        }
    }

    return trim($alt);
}

function add_transaction($transaction_id, $amount, $payment_method, $user_id, $status = 'pending', $subscription_id = '', $message = '')
{

    $db = \Config\Database::connect();
    $arr = [
        'user_id' => $user_id,
        'payment_method' => $payment_method,
        'txn_id' => $transaction_id,
        'amount' => $amount,
        'message' => $message,
        'status' => $status
    ];

    $insert = $db->table('transactions')->insert($arr);
    $db->lastQuery;
    if ($insert) {
        return $db->insertID();
    } else {
        return false;
    }
}

function verify_payment_transaction($txn_id, $payment_method, $additional_data = [])
{
    $db = \Config\Database::connect();

    if (empty(trim($txn_id))) {
        $response['error'] = true;
        $response['message'] = "Transaction ID is required";
        return $response;
    }
    $razorpay = new Razorpay;
    switch ($payment_method) {
        case 'razorpay':
            $payment = $razorpay->fetch_payments($txn_id);
            if (!empty($payment) && isset($payment['status'])) {
                if ($payment['status'] == 'authorized') {
                    $capture_response = $razorpay->capture_payment($payment['amount'], $txn_id, $payment['currency']);
                    if ($capture_response['status'] == 'captured') {
                        $response['error'] = false;
                        $response['message'] = "Payment captured successfully";
                        $response['amount'] = $capture_response['amount'] / 100;
                        $response['data'] = $capture_response;
                        $response['status'] = $payment['status'];
                        return $response;
                    } else if ($capture_response['status'] == 'refunded') {
                        $response['error'] = true;
                        $response['message'] = "Payment is refunded.";
                        $response['amount'] = $capture_response['amount'] / 100;
                        $response['data'] = $capture_response;
                        $response['status'] = $payment['status'];
                        return $response;
                    } else {
                        $response['error'] = true;
                        $response['message'] = "Payment could not be captured.";
                        $response['amount'] = (isset($capture_response['amount'])) ? $capture_response['amount'] / 100 : 0;
                        $response['data'] = $capture_response;
                        $response['status'] = $payment['status'];
                        return $response;
                    }
                } else if ($payment['status'] == 'captured') {
                    $response['error'] = false;
                    $response['message'] = "Payment captured successfully";
                    $response['amount'] = $payment['amount'] / 100;
                    $response['status'] = $payment['status'];
                    $response['data'] = $payment;
                    return $response;
                } else if ($payment['status'] == 'created') {
                    $response['error'] = true;
                    $response['message'] = "Payment is just created and yet not authorized / captured!";
                    $response['amount'] = $payment['amount'] / 100;
                    $response['data'] = $payment;
                    $response['status'] = $payment['status'];
                    return $response;
                } else {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . ucwords($payment['status']) . "! ";
                    $response['amount'] = (isset($payment['amount'])) ? $payment['amount'] / 100 : 0;
                    $response['status'] = $payment['status'];
                    $response['data'] = $payment;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the transaction ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                $response['status'] = 'failed';
                return $response;
            }
            break;

        case "flutterwave":
            $flutterwave = new Flutterwave();
            $transaction = $flutterwave->verify_transaction($txn_id);
            if (!empty($transaction)) {
                $transaction = json_decode($transaction, true);
                if ($transaction['status'] == 'error') {
                    $response['error'] = true;
                    $response['message'] = $transaction['message'];
                    $response['amount'] = (isset($transaction['data']['amount'])) ? $transaction['data']['amount'] : 0;
                    $response['data'] = $transaction;
                    $response['status'] = $transaction['data']['status'];
                    return $response;
                }

                if ($transaction['status'] == 'success' && $transaction['data']['status'] == 'successful') {
                    $response['error'] = false;
                    $response['message'] = "Payment has been completed successfully";
                    $response['amount'] = $transaction['data']['amount'];
                    $response['status'] = $transaction['data']['status'];
                    $response['data'] = $transaction;
                    return $response;
                } else if ($transaction['status'] == 'success' && $transaction['data']['status'] != 'successful') {
                    $response['error'] = true;
                    $response['message'] = "Payment is " . $transaction['data']['status'];
                    $response['amount'] = $transaction['data']['amount'];
                    $response['status'] = $transaction['data']['status'];
                    $response['data'] = $transaction;
                    return $response;
                }
            } else {
                $response['error'] = true;
                $response['message'] = "Payment not found by the transaction ID!";
                $response['amount'] = 0;
                $response['data'] = [];
                $response['status'] = $transaction['status'];
                return $response;
            }
            break;
    }
}

function add_subscription($user_id, $plan_id, $tenure, $transaction_id, $price, $tenure_name = "", $starts_from = '', $start_now = false)
{
    $id = active_plan($user_id);
    $upcoming_plan_id = has_upcoming($user_id);


    $db = \Config\Database::connect();

    if ($starts_from == '') {
        $starts_from = date('Y-m-d');
    }

    if ($id) {
        $previous = fetch_details('users_packages', ['id' => $id])[0];
        if ($start_now) {
            $expiry_date = strtotime($previous['start_date']);
            $expiry_date = strtotime($previous['end_date']);
            $seconds = $expiry_date - time();
            update_details(['status' => 0], ['id' => $id], 'users_packages');
        } else {
            if (!($starts_from == '')) {
                $starts_from = $previous['end_date'];
            }
        }
    }

    if ($upcoming_plan_id) {
        foreach ($upcoming_plan_id as $upcoming_id) {
            $previous = fetch_details('users_packages', ['id' => $upcoming_id])[0];
            if (!($starts_from == '')) {
                $starts_from = $previous['end_date'];
            }
        }
    }
    $expiry_date = new \DateTime($starts_from);
    $expiry_date = $expiry_date->modify('+' . $tenure . ' months')->format('Y-m-d');
    $plan = $db->table('packages')->where(['id' => $plan_id]);
    $plan = $plan->get()->getResultArray()[0];

    $data = [
        'user_id' => $user_id,
        'package_id' => $plan_id,
        'package_name' => $plan['title'],
        'no_of_businesses' => $plan['no_of_businesses'],
        'no_of_delivery_boys' => $plan['no_of_delivery_boys'],
        'no_of_products' => $plan['no_of_products'],
        'no_of_customers' => $plan['no_of_customers'],
        'no_of_warehouse' => $plan['no_of_warehouse'],
        'no_of_brands' => $plan['no_of_brands'],
        'tenure' => $tenure_name,
        'price' => $price,
        'months' => $tenure,
        'status' => '1',
        'start_date' => $starts_from,
        'end_date' => $expiry_date
    ];

    $builder = $db->table('users_packages')->insert($data);
    if ($builder) {
        return $db->insertID();
    }
    return false;
}
function active_plan($user_id)
{
    $db = \Config\Database::connect();
    $builder = $db->table('users_packages');
    $builder = $builder->where(['user_id' => $user_id]);
    $result = $builder->get()->getResultArray();
    if (count($result) == 0) {
        return false;
    }
    $type = $db->table('users_packages');
    $arr = $type->select(['start_date', 'end_date', 'tenure', 'id'])->where(['user_id' => $user_id, 'status' => 1])->get()->getResultArray();
    foreach ($arr as $row) {
        $status = subscription_status($row['id']);
        if ($status == 'active') {
            return $row['id'];
        }
    }
    return false;
}

function check_subscription($user_id)
{

    $id = active_plan($user_id);
    if (!$id) {
        return false;
    }

    $subscription = fetch_details("users_packages", ['id' => $id]);
    if ($subscription) {
        $status = subscription_status($id);
        $business = [];
        $product = [];
        $delivery_boys = [];
        $customer = [];

        if ($status == "active") {
            $no_of_businesses = $subscription[0]['no_of_businesses'];
            $arr = [];
            $businesses = fetch_details('businesses', ['user_id' => $user_id]);
            if ($no_of_businesses) {
                $count = count($businesses);
                if ($no_of_businesses == $count || $no_of_businesses < $count) {
                    $arr = "0";
                }
                if ($no_of_businesses > $count) {
                    $arr = "1";
                }
                $business = $arr;
            }
            $no_of_products = $subscription[0]['no_of_products'];

            if ($no_of_products) {
                $products = fetch_details('products', ['vendor_id' => $user_id]);
                $count = count($products);
                if (($no_of_products == $count || $no_of_products < $count) && $no_of_products != '-1') {
                    $arr = "0";
                }
                if ($no_of_products > $count || $no_of_products == '-1') {
                    $arr = "1";
                }
                $product = $arr;
            }
            $no_of_delivery_boys = $subscription[0]['no_of_delivery_boys'];
            if ($no_of_delivery_boys) {
                $delivery_boy = fetch_details("delivery_boys", ['vendor_id' => $user_id]);
                $count = count($delivery_boy);
                if ($no_of_delivery_boys == $count || $no_of_delivery_boys < $count) {
                    $arr = "0";
                }
                if ($no_of_delivery_boys > $count) {
                    $arr = "1";
                }
                $delivery_boys = $arr;
            }
            $no_of_customers = $subscription[0]['no_of_customers'];
            if ($no_of_customers) {
                $customers = fetch_details("customers", ['vendor_id' => $user_id]);
                $count = count($customers);
                if ($no_of_customers == $count || $no_of_customers < $count) {
                    $arr = "0";
                }
                if ($no_of_customers > $count) {
                    $arr = "1";
                }
                $customer = $arr;
            }
            $array = [
                'no_of_businesses' => $business,
                'no_of_products' => $product,
                'no_of_delivery_boys' => $delivery_boys,
                'no_of_customers' => $customer,

            ];
            return $array;
        } else {
            $id = has_upcoming($user_id);
            if (isset($id) && !empty($id[0])) {
                echo "has upcoming plan";
            }
        }
    } else {
        return false;
    }
}


function get_vendor_of_delivery_boy($user_id)
{
    $db = \Config\Database::connect();
    $builder = $db->table("delivery_boys");
    $builder->select('vendor_id')->where(['user_id' => $user_id, 'status' => "1"]);
    $vendor_id = $builder->get()->getRow();
    return $vendor_id->vendor_id ?? '';
}
// check user package 
function subscription()
{
    $ionAuth = new \App\Libraries\IonAuth();
    $id = $ionAuth->getUserId();
    if ($ionAuth->isDeliveryBoy()) {
        $vendor_id = get_vendor_of_delivery_boy($id);
        $user = fetch_details('users', ['id' => $vendor_id, 'active' => 1]);
        $users_package_id = active_plan($vendor_id);
    } else if ($ionAuth->isTeamMember()) {
        $vendor_id = get_vendor_for_teamMember($id);
        $user = fetch_details('users', ['id' => $vendor_id, 'active' => 1]);
        $users_package_id = active_plan($vendor_id);
    } else {
        $user = fetch_details('users', ['id' => $id, 'active' => 1]);
        $users_package_id = active_plan($id);
    }
    if (!empty($user)) {
        $user_package = fetch_details('users_packages', ['id' => $users_package_id, 'status' => 1]);
        if (!empty($user_package)) {
            foreach ($user_package as $package) {
                $status = subscription_status($package['id']);
                if ($status == 'active') {
                    return $status;
                }
                if ($status == 'upcoming') {
                    return 'upcoming';
                }
                if ($status == 'expired') {
                    return 'expired';
                }
            }
        } else {
            return 'expired';
        }
    }
}

function create_label($variable, $title = '')
{

    if ($title == '') {
        $title = $variable;
    }
    return '<div class="form-group col-md-6">
        <label>' . $title . '</label>
        <input type="text" name="' . $variable . '" value="' . labels($variable, $title) . '" class="form-control">
    </div>';
}

function update_stock($product_variant_ids, $qtns, $type = '')
{

    /*
        --First Check => Is stock management active (Stock type != NULL) 
        Case 1 : Simple Product 		
        Case 2 : Variable Product (Product Level,Variant Level) 			

        Stock Type :
            0 => Simple Product(simple product)
                  -Stock will be stored in (product)master table	
            1 => Product level(variable product)
                -Stock will be stored in product_variant table	
            2 => Variant level(variable product)		
                -Stock will be stored in product_variant table	
        */
    $db = \Config\Database::connect();
    $res = $db->table('products as p')
        ->select('p.*,pv.*,p.id as p_id,pv.id as pv_id,p.stock as p_stock,pv.stock as pv_stock')
        ->where('pv.id = ', $product_variant_ids)
        ->join('products_variants as pv', 'p.id = pv.product_id ', "left")
        ->get()->getResultArray();


    for ($i = 0; $i < count($res); $i++) {
        if (($res[$i]['stock_management'] != null || $res[$i]['stock_management'] != "")) {

            /* Case 1 : Simple Product(simple product) */
            if ($res[$i]['stock_management'] == 0) {
                if ($type == 'plus') {
                    if ($res[$i]['p_stock'] != null) {
                        $stock = intval($res[$i]['p_stock']) + intval($qtns);
                        $db->table('products')->where('id', $res[$i]['p_id'])->update(['stock' => $stock]);
                        if ($stock > 0) {
                            return true;
                        }
                    }
                } else {
                    if ($res[$i]['p_stock'] != null && $res[$i]['p_stock'] > 0) {
                        $stock = intval($res[$i]['p_stock']) - intval($qtns);
                        $db->table('products')->where('id', $res[$i]['p_id'])->update(['stock' => $stock]);
                        if ($stock == 0) {
                            return false;
                        }
                    }
                }
            }

            /* Case 2 : Product level(variable product) */
            if ($res[$i]['stock_management'] == 1) {
                if ($type == 'plus') {

                    if ($res[$i]['p_stock'] != null) {
                        $stock = intval($res[$i]['p_stock']) + intval($qtns);
                        $db->table('products')->where('id', $res[$i]['p_id'])->update(['stock' => $stock]);
                        if ($stock > 0) {
                            return true;
                        }
                    }
                } else {
                    if ($res[$i]['p_stock'] != null && $res[$i]['p_stock'] > 0) {
                        $stock = intval($res[$i]['p_stock']) - intval($qtns);
                        $db->table('products')->where('id', $res[$i]['p_id'])->update(['stock' => $stock]);
                        if ($stock == 0) {
                            return false;
                        }
                    }
                }
            }

            /* Case 3 : Variant level(variable product) */
            if ($res[$i]['stock_management'] == 2) {
                if ($type == 'plus') {
                    if ($res[$i]['pv_stock'] != null) {

                        $stock = intval($res[$i]['pv_stock']) + intval($qtns);
                        $db->table('products_variants')->where('id', $res[$i]['pv_id'])->update(['stock' => $stock]);
                        if ($stock > 0) {
                            return true;
                        }
                    }
                } else {
                    if ($res[$i]['pv_stock'] != null && $res[$i]['pv_stock'] > 0) {

                        $stock = intval($res[$i]['pv_stock']) - intval($qtns);
                        $db->table('products_variants')->where('id', $res[$i]['pv_id'])->update(['stock' => $stock]);
                        if ($stock == 0) {
                            return false;
                        }
                    }
                }
            }
        }
    }
}

function validate_stock($product_variant_ids, $qtns)
{
    /*
        --First Check => Is stock management active (Stock type != NULL) 
        Case 1 : Simple Product 		
        Case 2 : Variable Product (Product Level,Variant Level) 			

        Stock Type :
            0 => Simple Product(simple product)
                  -Stock will be stored in (product)master table	
            1 => Product level(variable product)
                -Stock will be stored in product_variant table	
            2 => Variant level(variable product)		
                -Stock will be stored in product_variant table	
        */
    $db = \Config\Database::connect();
    $response = array();
    $is_exceed_allowed_quantity_limit = false;
    $error = false;
    for ($i = 0; $i < count($product_variant_ids); $i++) {

        $res = $db->table('products as p')
            ->select('pv.id as pv_id,p.stock_management,SUM(wps.stock) as p_stock,pv.stock as pv_stock,p.name,pv.variant_name,wps.stock as warehouse_stock,wps.qty_alert as warehouse_qty_alert')
            ->where('pv.id = ', $product_variant_ids[$i])
            ->join('products_variants as pv', 'p.id = pv.product_id ', "left")
            ->join('warehouse_product_stock wps', 'wps.product_variant_id = pv.id', 'left')
            ->get()->getResultArray();

        if (($res[0]['stock_management'] != null && $res[0]['stock_management'] != '')) {
            //Case 1 : Simple Product(simple product)
            if ($res[0]['stock_management'] == 0) {
                if ($res[0]['p_stock'] != null && $res[0]['p_stock'] != '') {
                    $stock = intval($res[0]['p_stock']) - intval($qtns[$i]);
                    if ($stock < 0) {
                        $error = true;
                        break;
                    }
                }
            }
            //Case 2 & 3 : Product level(variable product) ||  Variant level(variable product)
            if ($res[0]['stock_management'] == 1) {
                if ($res[0]['p_stock'] != null && $res[0]['p_stock'] != '') {
                    $stock = intval($res[0]['p_stock']) - intval($qtns[$i]);
                    if ($stock < 0) {
                        $error = true;
                        break;
                    }
                }
            }
            if ($res[0]['stock_management'] == 2) {
                if ($res[0]['pv_stock'] != null && $res[0]['pv_stock'] != '') {
                    $stock = intval($res[0]['pv_stock']) - intval($qtns[$i]);
                    if ($stock < 0) {
                        $error = true;
                        break;
                    }
                }
            }
        }
    }


    if ($error) {
        $response['error'] = true;
        if ($is_exceed_allowed_quantity_limit) {
            $response['message'] = "One of the products quantity exceeds the allowed limit.Please deduct some quanity in order to purchase the item";
        } else {
            // $response['message'] = "One of the product is out of stock.";
            $response['message'] = $res[0]['name'] . ' (' . $res[0]['variant_name'] . ") product is out of stock.";
        }
    } else {
        $response['error'] = false;
        $response['message'] = "Stock available for purchasing.";
    }
    return $response;
}

function get_business_icon($business_id = "")
{
    $icon = fetch_details('businesses', ['id' => $business_id], ['icon'])[0];
    if (isset($icon) && !empty($icon)) {
        return $icon;
    }
}
function is_exist($where, $table, $update_id = null)
{


    $db = \Config\Database::connect();
    $builder = $db->table($table);

    $where_tmp = [];
    foreach ($where as $key => $val) {
        $where_tmp[$key] = $val;
    }


    if (($update_id == null) ? $builder->where($where_tmp)->countAllResults() > 0 : $builder->where($where_tmp)->whereNotIn('id', $update_id)->countAllResults() > 0) {
        return true;
    } else {
        return false;
    }
}

function fetch_purchases()
{
    $db = \Config\Database::connect();
    $purchase[] = array();
    $sales[] = array();
    $month_res = $db->table('orders')
        ->select('SUM(final_total) AS total_sale')
        ->where('business_id', $_SESSION['business_id'])
        ->get()->getResultArray();
    $month_wise_sales['total_sale'] = array_map('intval', array_column($month_res, 'total_sale'));


    $month_res_purchase = $db->table('purchases')
        ->select('SUM(total) AS total_purchases ')
        ->where('business_id', $_SESSION['business_id'])
        ->get()->getResultArray();

    $month_wise_sales['total_purchases'] = array_map('intval', array_column($month_res_purchase, 'total_purchases'));

    $purchase = $month_wise_sales;
    return $purchase;
}

//DATABASE BACKUP

function backup_tables($host, $user, $pass, $dbname, $tables = '*')
{
    $link = mysqli_connect($host, $user, $pass, $dbname);

    // Check connection
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
        exit;
    }

    mysqli_query($link, "SET NAMES 'utf8'");

    //get all of the tables
    if ($tables == '*') {
        $tables = array();
        $result = mysqli_query($link, 'SHOW TABLES');

        while ($row = mysqli_fetch_row($result)) {
            $tables[] = $row[0];
        }
    } else {
        $tables = is_array($tables) ? $tables : explode(',', $tables);
    }



    $return = '';
    //cycle through
    foreach ($tables as $table) {
        $result = mysqli_query($link, 'SELECT * FROM ' . $table);
        $num_fields = mysqli_num_fields($result);
        $num_rows = mysqli_num_rows($result);

        $return .= 'DROP TABLE IF EXISTS ' . $table . ';';
        $row2 = mysqli_fetch_row(mysqli_query($link, 'SHOW CREATE TABLE ' . $table));
        $return .= "\n\n" . $row2[1] . ";\n\n";
        $counter = 1;

        //Over tables
        for ($i = 0; $i < $num_fields; $i++) {   //Over rows
            while ($row = mysqli_fetch_row($result)) {
                if ($counter == 1) {
                    $return .= 'INSERT INTO ' . $table . ' VALUES(';
                } else {
                    $return .= '(';
                }

                //Over fields
                for ($j = 0; $j < $num_fields; $j++) {
                    $row[$j] = addslashes($row[$j]);
                    $row[$j] = str_replace("\n", "\\n", $row[$j]);
                    if (isset($row[$j])) {
                        $return .= '"' . $row[$j] . '"';
                    } else {
                        $return .= '""';
                    }
                    if ($j < ($num_fields - 1)) {
                        $return .= ',';
                    }
                }

                if ($num_rows == $counter) {
                    $return .= ");\n";
                } else {
                    $return .= "),\n";
                }
                ++$counter;
            }
        }
        $return .= "\n\n\n";
    }
    // Directory path
    $backupDir = 'public/database_backup';

    // Check if directory exists, if not, create it
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true); // recursive = true
    }

    // Set file name
    $fileName = $backupDir . '/db-backup-' . date('d-m-Y_g-i-s') . '.sql';

    if (write_file($fileName, $return)) {
        return true;
    } else {
        return false;
    }
}

function currency_location($data)
{
    $settings = get_settings('general', true);
    $currency_location = isset($settings['currency_locate']) ? $settings['currency_locate'] : 'left';
    $currency = $settings['currency_symbol'];


    if ($currency_location === 'left') {
        $data = $currency . $data;
        return $data;
    }
    if ($currency_location === 'right') {
        $data = $data . $currency;
        return $data;
    }
}
function date_formats($data)
{
    $settings = get_settings('general', true);

    $date_formats = isset($settings['date_format']) ? $settings['date_format'] : 'm/d/y H:i A';

    if ($date_formats === 'm/d/y H:i A') {
        $data = date($date_formats, $data);
        return $data;
    }
    if ($date_formats === 'd/m/Y H:i A') {
        $data = date($date_formats, $data);
        return $data;
    }
    if ($date_formats === 'Y/m/d H:i A') {
        $data = date($date_formats, $data);
        return $data;
    }
    if ($date_formats === 'd-M-Y H:i A') {
        $data = date($date_formats, $data);
        return $data;
    }
}
function decimal_points($data)
{
    $settings = get_settings('general', true);
    $decimal_points = isset($settings['decimal_points']) ? (int) $settings['decimal_points'] : 0;
    $data = number_format($data, $decimal_points);
    return $data;
}
function formatOffset($offset)
{
    $hours = $offset / 3600;
    $remainder = $offset % 3600;
    $sign = $hours > 0 ? '+' : '-';
    $hour = (int) abs($hours);
    $minutes = (int) abs($remainder / 60);
    if ($hour == 0 and $minutes == 0) {
        $sign = ' ';
    }
    return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minutes, 2, '0');
}

function getTimezoneOptions()
{
    $list = DateTimeZone::listAbbreviations();
    $idents = DateTimeZone::listIdentifiers();

    $data = $offset = $added = array();
    foreach ($list as $abbr => $info) {
        foreach ($info as $zone) {
            if (
                !empty($zone['timezone_id'])
                and
                !in_array($zone['timezone_id'], $added)
                and
                in_array($zone['timezone_id'], $idents)
            ) {
                $z = new DateTimeZone($zone['timezone_id']);
                $c = new DateTime(null, $z);
                $zone['time'] = $c->format('H:i a');
                $offset[] = $zone['offset'] = $z->getOffset($c);
                $data[] = $zone;
                $added[] = $zone['timezone_id'];
            }
        }
    }

    array_multisort($offset, SORT_ASC, $data);

    $i = 0;
    $temp = array();
    foreach ($data as $key => $row) {
        $temp[0] = $row['time'];
        $temp[1] = formatOffset($row['offset']);
        $temp[2] = $row['timezone_id'];
        $options[$i++] = $temp;
    }

    if (!empty($options)) {
        return $options;
    }
}

function csvToJson($file_path, $type = 'upload')
{
    $data = [];
    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $headers = fgetcsv($handle);

        // Create a mapping of header positions
        $headerPositions = [];
        foreach ($headers as $index => $header) {
            $header = str_replace(' ', '_', $header);
            if (!isset($headerPositions[$header])) {
                $headerPositions[$header] = [];
            }
            $headerPositions[$header][] = $index;
        }

        while (($row = fgetcsv($handle)) !== FALSE) {
            // Skip rows that are empty or contain only null/empty strings
            $filteredRow = array_filter($row, function ($value) {
                return $value !== null && trim($value) !== '';
            });
            if (empty($filteredRow)) {
                continue;
            }

            $rowData = [];
            foreach ($headerPositions as $header => $positions) {
                if (count($positions) > 1) {
                    $rowData[$header] = [];
                    foreach ($positions as $pos) {
                        $rowData[$header][] = $row[$pos] ?? '';
                    }
                } else {
                    $rowData[$header] = $row[$positions[0]] ?? '';
                }
            }
            $data[] = $rowData;
        }

        fclose($handle);
    }


    return $data;
}


// function csvToJsonProduct($file_path, $type = 'upload')
// {
//     $data = [];

//     if (($handle = fopen($file_path, "r")) !== FALSE) {

//         $headers = fgetcsv($handle);

//         // Create a mapping of header positions
//         $headerPositions = [];
//         foreach ($headers as $index => $header) {
//             $header = str_replace(' ', '_', $header);
//             if (!isset($headerPositions[$header])) {
//                 $headerPositions[$header] = [];
//             }
//             $headerPositions[$header][] = $index;
//         }

//         // Define field mappings to JSON
//         $field_mappings = [
//             'image' => 'pro_input_image',
//             'type' => 'product_type',
//             'is_tax_included' => 'is_tax_inlcuded',
//             'barcode' => 'variant_barcode'
//         ];


//         // Read CSV rows
//         $rows_by_product = [];
//         while (($row = fgetcsv($handle)) !== FALSE) {

//             // Skip empty rows
//             $filteredRow = array_filter($row, function ($value) {
//                 return $value !== null && trim($value) !== '';
//             });
//             if (empty($filteredRow)) {
//                 continue;
//             }

//             $rowData = [];
//             foreach ($headerPositions as $header => $positions) {
//                 $key = $field_mappings[$header] ?? $header;
//                 if (count($positions) > 1 && !in_array($header, ['warehouse_ids', 'warehouse_stock', 'warehouse_qty_alert'])) {
//                     $rowData[$key] = [];
//                     foreach ($positions as $pos) {
//                         $rowData[$key][] = $row[$pos] ?? '';
//                     }
//                 } else {
//                     $rowData[$key] = !empty($positions) ? ($row[$positions[0]] ?? '') : '';
//                 }
//             }
//             $product_name = $rowData['name'] ?? 'unknown';
//             $rows_by_product[$product_name][] = $rowData;
//         }

//         fclose($handle);

//         // Process each product
//         foreach ($rows_by_product as $product_name => $rows) {
//             $product = [
//                 'name' => $product_name,
//                 'product_id' => '',
//                 'category_id' => '',
//                 'brand_id' => '',
//                 'is_tax_inlcuded' => '',
//                 'tax_ids' => '',
//                 'description' => '',
//                 'pro_input_image' => '',
//                 'product_type' => '',
//                 'stock_management' => '',
//                 'stock_management_type' => '',
//                 'simple_product_stock' => '',
//                 'simple_product_unit_id' => '',
//                 'simple_product_qty_alert' => '',
//                 'variant_name' => [],
//                 'variant_barcode' => [],
//                 'sale_price' => [],
//                 'purchase_price' => [],
//                 'unit_id' => [],
//                 'stock' => [],
//                 'qty_alert' => [],
//                 'warehouses' => [],
//                 'status' => '',
//                 'business_id' => '',
//                 'vendor_id' => ''
//             ];

//             foreach ($rows as $index => $rowData) {
//                 // Set product-level fields from the first row
//                 if ($index === 0) {
//                     $product['category_id'] = $rowData['category_id'] ?? '';
//                     $product['business_id'] = $rowData['business_id'] ?? '';
//                     $product['vendor_id'] = $rowData['vendor_id'] ?? '';
//                     $product['tax_ids'] = $rowData['tax_ids'] ?? '';
//                     $product['description'] = $rowData['description'] ?? '';
//                     $product['pro_input_image'] = $rowData['pro_input_image'] ?? '';
//                     $product['product_type'] = $rowData['product_type'] ?? '';
//                     $product['stock_management_type'] = $rowData['stock_management'] ?? '';
//                     $product['stock_management'] = ($rowData['stock_management'] === '1') ? 'on' : 'off';
//                     $product['simple_product_stock'] = $rowData['stock'] ?? '';
//                     $product['simple_product_unit_id'] = $rowData['unit_id'] ?? '';
//                     $product['simple_product_qty_alert'] = $rowData['qty_alert'] ?? '';
//                     $product['status'] = $rowData['status'] ?? '';
//                     $product['product_id'] = $rowData['product_id'] ?? '';
//                 }

//                 // Collect variant data
//                 $product['variant_name'][] = $rowData['variant_name'] ?? '';
//                 $product['sale_price'][] = $rowData['sale_price'] ?? '';
//                 $product['purchase_price'][] = $rowData['purchase_price'] ?? '';
//                 $product['variant_barcode'][] = $rowData['variant_barcode'] ?? '';
//                 $product['unit_id'][] = $rowData['variant_unit_id'] ?? $rowData['unit_id'] ?? '';
//                 $product['stock'][] = $rowData['variant_stock'] ?? '';
//                 $product['qty_alert'][] = $rowData['variant_qty_alert'] ?? '';

//                 // Collect warehouse data
//                 $warehouse_data = [
//                     'warehouse_ids' => [],
//                     'warehouse_stock' => [],
//                     'warehouse_qty_alert' => []
//                 ];
//                 if (isset($headerPositions['warehouse_ids'])) {
//                     foreach ($headerPositions['warehouse_ids'] as $index => $pos) {
//                         if (!empty($rowData['warehouse_ids'][$index])) {
//                             $warehouse_data['warehouse_ids'][] = $rowData['warehouse_ids'][$index];
//                             $warehouse_data['warehouse_stock'][] = $rowData['warehouse_stock'][$index] ?? '';
//                             $warehouse_data['warehouse_qty_alert'][] = $rowData['warehouse_qty_alert'][$index] ?? '';
//                         }
//                     }
//                 }
//                 $product['warehouses'][] = $warehouse_data;

//             }

//             $data[] = $product;
//         }
//     }

//     return $data;
// }

function csvToJsonProduct($file_path, $type = 'upload')
{
    $data = [];

    if (($handle = fopen($file_path, "r")) !== FALSE) {
        $headers = fgetcsv($handle);

        // Create a mapping of header positions
        $headerPositions = [];
        foreach ($headers as $index => $header) {
            $header = str_replace(' ', '_', trim($header));
            if (!isset($headerPositions[$header])) {
                $headerPositions[$header] = [];
            }
            $headerPositions[$header][] = $index;
        }

        // Define field mappings to JSON
        $field_mappings = [
            'image_path' => 'pro_input_image',
            'product_type' => 'product_type',
            'product_name' => 'name'
        ];

        // Identify variant indices dynamically
        $variant_indices = [];
        foreach ($headerPositions as $header => $positions) {
            if (preg_match('/^variant_(\d+)_name$/', $header, $matches)) {
                $variant_indices[] = (int) $matches[1];
            }
        }
        sort($variant_indices); // Ensure variants are processed in order (1, 2, 3, ...)

        // Read CSV rows
        while (($row = fgetcsv($handle)) !== FALSE) {
            // Skip empty rows
            $filteredRow = array_filter($row, function ($value) {
                return $value !== null && trim($value) !== '';
            });
            if (empty($filteredRow)) {
                continue;
            }

            $rowData = [];
            foreach ($headerPositions as $header => $positions) {
                $key = $field_mappings[$header] ?? $header;
                if (count($positions) > 1) {
                    $rowData[$key] = [];
                    foreach ($positions as $pos) {
                        $rowData[$key][] = $row[$pos] ?? '';
                    }
                } else {
                    $rowData[$key] = !empty($positions) ? ($row[$positions[0]] ?? '') : '';
                }
            }

            // Initialize product structure
            $product = [
                'product_id' => $rowData['product_id'] ?? '',
                'name' => $rowData['name'] ?? 'unknown',
                'category_id' => $rowData['category_id'] ?? '',
                'business_id' => $rowData['business_id'] ?? '',
                'vendor_id' => $rowData['vendor_id'] ?? '',
                'description' => $rowData['description'] ?? '',
                'image' => $rowData['image'] ?? '',
                'tax_ids' => $rowData['tax_ids'] ?? '',
                'brand_id' => $rowData['brand_id'] ?? '',
                'product_type' => $rowData['product_type'] ?? '',
                'is_tax_included' => $rowData['is_tax_included'] ?? '',
                'status' => $rowData['status'] ?? '',
                'stock_management' => $rowData['stock_management'] ?? '',
                'product_stock' => $rowData['product_stock'] ?? '',
                'product_qty_alert' => $rowData['product_qty_alert'] ?? '',
                'product_unit_id' => $rowData['product_unit_id'] ?? '',
                'variants' => [],
            ];

            // Handle variants dynamically
            $variants = [];
            foreach ($variant_indices as $index) {
                $variant_name = $rowData["variant_{$index}_name"] ?? '';
                if (!empty($variant_name)) {
                    $variant = [
                        'variant_name' => $variant_name,
                        'variant_id' => $rowData["variant_{$index}_id"] ?? '',
                        'barcode' => $rowData["variant_{$index}_barcode"] ?? '',
                        'sale_price' => $rowData["variant_{$index}_sale_price"] ?? '',
                        'purchase_price' => $rowData["variant_{$index}_purchase_price"] ?? '',
                        'unit_id' => $rowData["variant_{$index}_unit_id"] ?? '',
                        'stock' => $rowData["variant_{$index}_stock"] ?? '',
                        'qty_alert' => $rowData["variant_{$index}_qty_alert"] ?? '',
                        'warehouses' => []
                    ];

                    // Handle warehouses for this variant dynamically
                    $warehouse_indices = [];
                    foreach ($headerPositions as $header => $positions) {
                        if (preg_match("/^variant_{$index}_warehouse_(\d+)_id$/", $header, $matches)) {
                            $warehouse_indices[] = (int) $matches[1];
                        }
                    }
                    sort($warehouse_indices); // Ensure warehouses are processed in order (1, 2, 3, ...)

                    foreach ($warehouse_indices as $w_index) {
                        $warehouse_id = $rowData["variant_{$index}_warehouse_{$w_index}_id"] ?? '';
                        if (!empty($warehouse_id)) {
                            $variant['warehouses'][] = [
                                'warehouse_id' => $warehouse_id,
                                'stock' => $rowData["variant_{$index}_warehouse_{$w_index}_stock"] ?? '',
                                'qty_alert' => $rowData["variant_{$index}_warehouse_{$w_index}_qty_alert"] ?? ''
                            ];
                        }
                    }

                    $variants[] = $variant;
                }
            }

            $product['variants'] = $variants;
            $data[] = $product;
        }

        fclose($handle);
    }

    return $data;
}