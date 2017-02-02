<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Customers_Transactions extends Migration
{
    public function up()
    {
        $fields = [
            'transaction_type' => [
                'type' => 'VARCHAR',
                'constraint' => '64',
                'after' => 'payment_type', 
                'null' => true, 
            ],
        ];
        $this->forge->addColumn('customers_transactions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('customers_transactions', ['transaction_type']);
    }
}
