<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        // inserting "-1" (infinite) to packages and users_packages table;
        
        $this->db->table('packages')->update(['no_of_warehouse' => -1]);

        $this->db->table('users_packages')->update(['no_of_warehouse' => -1]);


         // Fetch all businesses
        $businesses = $db->table('businesses')->get()->getResultArray();

        foreach ($businesses as $business) {
            $business_id = $business['id'];
            $vendor_id = $business['user_id'];

            $data = [
                'vendor_id' =>  $vendor_id,
                'business_id'    => $business_id,
                'name' => 'Default Warehouse',
                'country' => 'Default Country',
                'city' => 'Default City',
                'zip_code' => '0000000',
                'address' => 'Default Warehouse Address'
            ];
    
            // Using Query Builder
            $this->db->table('warehouses')->insert($data);
        }
    }
}
