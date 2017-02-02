<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseModel extends Model
{
    protected $table            = 'warehouses';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['id', 'vendor_id' ,'business_id' ,'name' , 'country' , 'city' , 'zip_code' , 'address'];

}
