<?php

namespace App\Models;

use CodeIgniter\Model;

class Packages_tenures_model extends Model
{

    protected $table = 'packages_tenures';
    protected $primaryKey = 'id';
    protected $allowedFields = ['package_id', 'tenure', 'months', 'price', 'discounted_price'];
    
}
