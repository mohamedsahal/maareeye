<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WarehouseProductStockSeeder extends Seeder
{
    public function run()
    {

        // Connect to the database
        $db = \Config\Database::connect();

        // Fetch all products from the 'products' table
        $products = $db->table('products')->get()->getResult();

        foreach ($products as $product) {

            // Fetch all variants related to the current product
            $variants = $db->table('products_variants')
                ->where('product_id', $product->id)
                ->get()->getResult();

            // Get business and vendor IDs for the current product
            $business_id = $product->business_id;
            $vendor_id = $product->vendor_id;

            // Find the appropriate warehouse based on the vendor and business IDs
            $warehouse = $db->table('warehouses')
                ->where([
                    'vendor_id' => $vendor_id,
                    'business_id' => $business_id
                ])
                ->get()
                ->getResult();

            // Only proceed if a warehouse is found
            if (!empty($warehouse)) {

                // Get the warehouse ID from the first warehouse found
                $warehouse_id = $warehouse[0]->id;

                // Loop through each variant of the product
                foreach ($variants as $variant) {

                    // Prepare data for insertion into the 'warehouse_product_stock' table
                    $data = [
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'warehouse_id' => $warehouse_id,
                        'product_variant_id' => $variant->id, // Use the variant ID
                        'created_at' => date('Y-m-d H:i:s'), // Current timestamp for created_at
                        'updated_at' => date('Y-m-d H:i:s'), // Current timestamp for updated_at
                    ];

                    // Determine stock values based on the stock management type
                    if ($product->stock_management == 1) {
                        // Stock managed at the product level (single variant)
                        $data['stock'] = $product->stock;
                        $data['qty_alert'] = $product->qty_alert;
                    } elseif ($product->stock_management == 2) {
                        // Stock managed per variant level
                        $data['stock'] = $variant->stock;
                        $data['qty_alert'] = $variant->qty_alert;
                    }

                    // Insert the prepared data into the 'warehouse_product_stock' table
                    $db->table('warehouse_product_stock')->insert($data);
                }
            }
        }
    }
}
