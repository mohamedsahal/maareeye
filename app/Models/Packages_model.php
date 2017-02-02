<?php

namespace App\Models;

use CodeIgniter\Model;

class Packages_model extends Model
{

    protected $table = 'packages';
    protected $primaryKey = 'id';

    protected $allowedFields = ['title', 'no_of_businesses', 'no_of_delivery_boys', 'no_of_products', 'no_of_customers', 'no_of_warehouse', 'no_of_brands' ,'description', 'status','type', 'tenure', 'months', 'price', 'discounted_price'];

    public function add_package_tenures($tenure)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('packages_tenures');
        return $builder->insert($tenure);
    }
    public function get_packages($id)
    {
        $db = \Config\Database::connect();
        $builder = $this->db->table("packages as p");
        $builder->select('p.*,pt.id as tenure_id,pt.package_id,pt.tenure,pt.months,pt.price,pt.discounted_price');
        $builder->where('pt.package_id ',$id);
        $builder->join('packages_tenures as pt', 'p.id = pt.package_id ', "left"); // added left here
        return $builder->get()->getResult();
    }
    
}
