<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateVendorPurchaseTransactionsAndMediaTables extends Migration
{
    public function up()
    {
        // vendor_purchase_transactions table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'vendor_id' => ['type' => 'INT', 'constraint' => 11],
            'order_id' => ['type' => 'INT', 'constraint' => 11],
            'supplier_id' => ['type' => 'INT', 'constraint' => 11],
            'order_type' => ['type' => 'VARCHAR', 'constraint' => 64],
            'created_by' => ['type' => 'INT', 'constraint' => 11],
            'payment_type' => ['type' => 'VARCHAR', 'constraint' => 128],
            'transaction_type' => ['type' => 'VARCHAR', 'constraint' => 128],
            'amount' => ['type' => 'DOUBLE'],
            'created_at datetime default current_timestamp',
            'updated_at TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('vendor_purchase_transactions');

        // media table
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
            'vendor_id' => ['type' => 'INT', 'constraint' => 11],
            'title' => ['type' => 'MEDIUMTEXT'],
            'name' => ['type' => 'MEDIUMTEXT'],
            'extension' => ['type' => 'VARCHAR', 'constraint' => 64],
            'type' => ['type' => 'VARCHAR', 'constraint' => 64],
            'sub_directory' => ['type' => 'MEDIUMTEXT'],
            'size' => ['type' => 'MEDIUMTEXT'],
            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('media');

        // Modify warehouses table
        $this->forge->addColumn('warehouses', [
            'default_warehouse' => [
                'type' => 'INT',
                'after' => 'address',
                'default' => 0,
            ]
        ]);

        // Modify warehouse_product_stock table
        $this->db->query("ALTER TABLE `warehouse_product_stock` CHANGE `created_at` `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;");
        $this->db->query("ALTER TABLE `warehouse_product_stock` CHANGE `updated_at` `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;");

        // Drop column from expenses_type
        $this->forge->dropColumn('expenses_type', 'expenses_type_date');
    }

    public function down()
    {
        $this->forge->dropTable('vendor_purchase_transactions', true);
        $this->forge->dropTable('media', true);
        $this->forge->dropColumn('warehouses', 'default_warehouse');

        // Revert warehouse_product_stock column changes
        $this->db->query("ALTER TABLE `warehouse_product_stock` CHANGE `created_at` `created_at` DATETIME;");
        $this->db->query("ALTER TABLE `warehouse_product_stock` CHANGE `updated_at` `updated_at` DATETIME;");

        // Re-add the dropped column in expenses_type (assuming DATETIME type)
        $this->forge->addColumn('expenses_type', [
            'expenses_type_date' => ['type' => 'DATETIME', 'null' => true],
        ]);
    }
}
