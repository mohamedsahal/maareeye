<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Categories_Table extends Migration
{
    // business_id in categories table
    public function up()
    {
        $fields = [
            'business_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'after' => 'vendor_id',
            ],
        ];
        $this->forge->addColumn('categories', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('categories', ['business_id']);
    }
}
