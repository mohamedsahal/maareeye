<?php

namespace App\Models;

use CodeIgniter\Model;

class Subscription_model extends Model
{

    protected $table = 'subscription';
    protected $primaryKey = 'id';
    protected $allowedFields = ['service_id', 'customer_id', 'vendor_id', 'delivery_boy_id', 'business_id'];

    public function count_subscription($customer_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("subscription");
        $builder->select('COUNT(service_id) as `total`');
        $builder->where('customer_id', $customer_id);
        $subscription = $builder->get()->getResultArray();
        return $subscription;
    }

    public function get_customers($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("subscription as sub");

        $builder->select('sub.service_id, sub.customer_id, sub.business_id, u.first_name as name');
        $builder->join('customers as c', 'sub.customer_id = c.id', 'left');
        $builder->join('users as u', 'c.user_id = u.id', 'left');
        $builder->where('sub.business_id', $business_id);
        $builder->groupBy('sub.customer_id');

        // Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('u.first_name', $search)
                ->groupEnd();
        }

        // Clone builder before pagination for count
        $count_builder = clone $builder;
        $total = $count_builder->countAllResults(false); // false to avoid resetting

        // Pagination
        $offset = $_GET['offset'] ?? '';
        $limit = $_GET['limit'] ?? '';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'u.id';
        $order = $_GET['order'] ?? 'ASC';
        $builder->orderBy($sort, $order);

        $customers = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $customers
        ];
    }
    public function get_subscription($customer_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("subscription as sub");
        $builder->select('sub.id as sub_id,sub.customer_id,sub.service_id ,o.id,os.service_name,os.recurring_days ,os.price,os.is_recursive,os.starts_on,os.ends_on');
        $builder->where('sub.customer_id', $customer_id);
        $builder->join('orders_services as os', 'sub.service_id = os.service_id ', "left"); // added left here
        $builder->join('orders as o', 'o.customer_id = sub.customer_id ', "left"); // added left here
        $builder->groupBy("sub.service_id");

        $condition = [];
        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'os.service_name ' => $search,
                'os.price' => $search,
                'os.is_recursive' => $search,
                'os.starts_on' => $search,
                'os.ends_on' => $search,
            ];
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $builder->where($condition);
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        $subscription = $builder->orderBy($sort, $order)->limit($limit, $offset)->getWhere()->getResultArray();
        return $subscription;
    }


    public function get_services($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("subscription as sub");
        $builder->select('sub.*, sub.id as sub_id, s.name');
        $builder->join('services as s', 's.id = sub.service_id', 'left');
        $builder->where('sub.business_id', $business_id);
        $builder->groupBy('sub.service_id');

        // Search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $builder->groupStart()
                ->orLike('s.name', $search)
                ->groupEnd();
        }

        // Clone for total count
        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false); // false = don't reset builder

        // Pagination
        $limit = isset($_GET['limit']) ? (int) $_GET['limit'] : '';
        $offset = isset($_GET['offset']) ? (int) $_GET['offset'] : '';

        if (isset($limit) && !empty($limit) && $limit > 0) {
            $builder->limit($limit, $offset);
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'sub.id';
        $order = $_GET['order'] ?? 'ASC';
        $builder->orderBy($sort, $order);

        $services = $builder->get()->getResultArray();

        return [
            'total' => $total,
            'data' => $services
        ];
    }
    public function get_customers_of_services($service_id = "", $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("subscription as sub");
        $builder->select('sub.customer_id,u.first_name ,u.last_name');
        $builder->where(['sub.business_id' => $business_id, 'sub.service_id' => $service_id]);
        $builder->join('customers as c', 'sub.customer_id = c.id ', "left"); // added left here
        $builder->join('users as u', 'c.user_id = u.id ', "left"); // added left here

        $condition = [];
        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "u.id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'u.id') {
                $sort = "u.id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'u.first_name ' => $search,

            ];
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $builder->where($condition);
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        $customers = $builder->orderBy($sort, $order)->limit($limit, $offset)->getWhere()->getResultArray();
        return $customers;
    }

    public function if_exist($service_id, $customer_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscription as sub');
        $builder->select('sub.id as id');
        $builder->where(['sub.service_id' => $service_id, 'sub.customer_id' => $customer_id]);
        $data = $builder->get()->getResultArray();
        return $data;
    }

    public function renew()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('subscription as sub');
        $builder->select('sub.service_id,sub.business_id,sub.vendor_id,sub.customer_id,s.status,os.status as os_status,os.id as os_id,os.*');
        $builder->join('services as s', 'sub.service_id = s.id', "left"); // added left here
        $builder->join('orders_services as os', 'sub.service_id = os.service_id', "left"); // added left here
        $builder->where(['s.status' => '1']);
        $subscriptions = $builder->get()->getResultArray();

        if (isset($subscriptions) && !empty($subscriptions)) {
            $orders_services_model = new Orders_services_model();
            $order_model = new Orders_model();

            $today = date('Y-m-d');
            $id = [];
            foreach ($subscriptions as $subscription) {

                $order = fetch_details('orders', ['id' => $subscription['order_id'], 'customer_id' => $subscription['customer_id']]);

                $expiry_date = $subscription['ends_on'];


                if (isset($order) && !empty($order)) {

                    if (date('Y-m-d', strtotime($expiry_date)) == $today) {
                        $days = $subscription['recurring_days'];

                        $end_date = date('Y-m-d', strtotime($today . ' + ' . $days . 'days'));

                        $order = array(
                            'vendor_id' => $subscription['vendor_id'],
                            'business_id' => $subscription['business_id'],
                            'customer_id' => $subscription['customer_id'],
                            'created_by' => $subscription['vendor_id'],
                            'final_total' => $subscription['vendor_id'],
                            'total' => $subscription['vendor_id'],
                            'delivery_charges' => $subscription['vendor_id'],
                            'discount' => $subscription['vendor_id'],
                            'payment_status' => 'unpaid',
                            'payment_method' => '',
                            'order_type' => 'service',
                            'amount_paid' => '',
                        );

                        $order_model->save($order);

                        $order_id = $order_model->getInsertID();

                        $order_services = array(
                            'order_id' => $order_id,
                            'service_id' => $subscription['service_id'],
                            'service_name' => $subscription['service_name'],
                            'price' => $subscription['price'],
                            'quantity' => $subscription['quantity'],
                            'unit_name' => $subscription['unit_name'],
                            'unit_id' => $subscription['unit_id'],
                            'tax_percentage' => $subscription['tax_percentage'],
                            'is_tax_included' => $subscription['is_tax_included'],
                            'tax_name' => $subscription['tax_name'],
                            'is_recursive' => $subscription['is_recursive'],
                            'recurring_days' => $subscription['recurring_days'],
                            'starts_on' => $today,
                            'ends_on' => $end_date,
                            'sub_total' => $subscription['sub_total'],
                            'status' => '1',
                        );
                        $id[] = $orders_services_model->insert($order_services);
                    }
                }
            }

            return $id;
        } else {
            return false;
        }
    }

    public function renew_message($id = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('orders_services as os');
        $builder->select('u.first_name,o.customer_id,u.email,os.service_name,o.final_total,os.starts_on,os.ends_on');
        $builder->join('orders as o', 'os.order_id = o.id', "left"); // added left here
        $builder->join('users as u', 'o.customer_id = u.id', "left"); // added left here
        $builder->whereIn('os.id', $id);
        $subscriptions = $builder->get()->getResultArray();
        return $subscriptions;
    }
}
