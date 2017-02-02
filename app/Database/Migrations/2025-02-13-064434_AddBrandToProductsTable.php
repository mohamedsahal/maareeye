<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBrandToProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'brand_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => false,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'brand_id');
    }
}
