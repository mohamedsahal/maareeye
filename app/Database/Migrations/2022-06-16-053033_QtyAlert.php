<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class QtyAlert extends Migration
{
    public function up()
    {
        $fields = [
            'qty_alert' => [
                'type' => 'VARCHAR',
                'constraint' => '16',
                'DEFAULT'    => null,
                'after' => 'stock',
            ],
        ];
        $this->forge->addColumn('products', $fields);
        $this->forge->addColumn('products_variants', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'qty_alert');
        $this->forge->dropColumn('products_variants', 'qty_alert');
    }
}
