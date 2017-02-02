<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderNoToOrdersTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders', [
            'order_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,  // You can set to false if you want it to be NOT NULL
                'after'      => 'customer_id',  // Position the column after 'id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'order_no');
    }
}
