<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SeedWarehouse extends BaseController
{
    public function seedWarehouseData()
    {
        // Get the database connection
        $db = \Config\Database::connect();

        $error_message = "";
        // Check if the 'warehouses' table exists
        if (!$db->tableExists('warehouses')) {
            $error_message = "Error: 'warehouses' table does not exist.";
        }

        // Check if a warehouse with ID 1 exists
        // $warehouseExists = $db->table('warehouses')->where('id', 1)->countAllResults() > 0;

        $warehouseStockCount = $db->table('warehouse_product_stock')->countAllResults();
        $productVariantsCount = $db->table('products_variants')->countAllResults();

    
        if ($warehouseStockCount == $productVariantsCount) {
            $error_message = "Error: Default Warehouses already exists.";
        }

        if(strlen($error_message ) > 0){
            return $error_message;
        }

        // Load the seeder
        $seeder = \Config\Database::seeder();

        // Run the seeders
        try {
            $seeder->call('WarehouseSeeder');
            $seeder->call('WarehouseProductStockSeeder');
            return "Seeders ran successfully."; // Proper return
        } catch (\Exception $e) {
            return "Error running seeder: " . $e->getMessage(); // Proper return
        }
    }
}
