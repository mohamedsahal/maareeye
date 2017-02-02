<?php

namespace Config;

defined('BASEPATH');

class Maareeye extends \CodeIgniter\Config\BaseConfig
{
    // public $actions = ['can_create', 'can_read', 'can_update', 'can_delete'];
    public $permissions = [
        
        'business' => ['can_read'],

        'categories' => ['can_create', 'can_read', 'can_update'],

        'customers' => ['can_create', 'can_read', 'can_update'],

        'expenses' => ['can_create', 'can_read', 'can_update'],

        'expenses_type' => ['can_create', 'can_read', 'can_update'],

        'generate_barcode' => ['can_create'],

        'manage_stock' => ['can_create', 'can_read'],

        'orders' => ['can_create', 'can_read', 'can_update'],

        'orders_return' => ['can_create', 'can_read', 'can_update'],

        'pos' => ['can_create'],

        'products' => ['can_create', 'can_read', 'can_update'],

        'purchase_return' => ['can_create', 'can_read', 'can_update'],

        'purchases' => ['can_create', 'can_read', 'can_update'],

        'services' => ['can_create', 'can_read', 'can_update'],

        'subscription' => ['can_create', 'can_read', 'can_update'],

        'suppliers' => ['can_create', 'can_read', 'can_update'],

        'transactions' => ['can_create', 'can_read'],

        'units' => ['can_create', 'can_read', 'can_update'],
    ];
}
