<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Supllier extends Migration
{
    // adding supplier table
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'vendor_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'balance' => [
                'type' => 'FLOAT',
                'null' => false

            ],
            'billing_address' => [
                'type' => 'VARCHAR',
                'constraint' => '264',
                'null' => false
            ],
            'shipping_address' => [
                'type' => 'VARCHAR',
                'constraint' => '264',
                'null' => false
            ],
            'credit_period' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'credit_limit' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'tax_name' => [
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => false
            ],
            'tax_num' => [
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => false
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => '2',
                'null' => false
            ],

            'created_at datetime default current_timestamp',
            'updated_at TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('suppliers');

        //  adding purchase table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'business_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'vendor_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'order_no' => [
                'type' => 'VARCHAR',
                'constraint' => '64',
                'null' => false
            ],
            'purchase_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'tax_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => '12',
                'null' => false
            ],
            'delivery_charges' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'total' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'payment_method' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
                'null' => false
            ],
            'payment_status' => [
                'type' => 'VARCHAR',
                'constraint' => '128',
                'null' => false
            ],
            'amount_paid' => [
                'type' => 'DOUBLE',
                'null' => false
            ],
            'message' => [
                'type' => 'VARCHAR',
                'constraint' => '1024',
                'null' => false
            ],
            'discount' => [
                'type' => 'FLOAT',
                'null' => false
            ],

            'created_at datetime default current_timestamp',
            'updated_at TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('purchases');

        //  adding purchase items table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'purchase_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'product_variant_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'quantity' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'price' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'discount' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'status' => [
                'type' => 'TINYINT',
                'constraint' => '2',
                'null' => false
            ],
            'created_at datetime default current_timestamp',
            'updated_at TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('purchases_items');

        // add columns in customers_transactions table
        $fields = [
            'supplier_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'DEFAULT'    => null,
                'after' => 'customer_id',
            ],
            'order_type' => [
                'type' => 'TINYINT',
                'constraint' => '2',
                'DEFAULT'    => null,
                'after' => 'order_id',
            ],
        ];
        $this->forge->addColumn('customers_transactions', $fields);


        $this->db->query('INSERT INTO `groups` ( `name`, `description`) VALUES ("suppliers", "Suppliers")');
    }

    public function down()
    {
        $this->forge->dropTable('suppliers');
        $this->forge->dropTable('purchases');
        $this->forge->dropTable('purchases_items');
        $this->forge->dropColumn('customers_transactions', ['supplier_id', 'transaction_type']);
    }
}
