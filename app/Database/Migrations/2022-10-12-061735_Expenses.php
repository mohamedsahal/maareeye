<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Expenses extends Migration
{
    // adding expenses and expenses_type table
    public function up()
    {
        //  adding expenses  table
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
            'expenses_id' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],
            'note' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],
            'amount' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],
            'expenses_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'created_at' =>
            [
                'type' => 'TIMESTAMP'
            ],
            'updated_at' =>
            [
                'type' => 'DATETIME',
                'null' => 'YES'
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('expenses');

        // adding expenses type table

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],

            'vendor_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],

            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],

            'description' => [
                'type' => 'VARCHAR',
                'constraint' => '512',
                'null' => false
            ],

            'expenses_type_date' => [
                'type' => 'DATE',
                'null' => false
            ],
            'created_at' =>
            [
                'type' => 'TIMESTAMP'
            ],
            'updated_at' =>
            [
                'type' => 'DATETIME',
                'null' => 'YES'
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('expenses_type');
    }

    public function down()
    {
        $this->forge->dropTable('expenses');
        $this->forge->dropTable('expenses_type');
    }
}
