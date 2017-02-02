<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FreePackage extends Migration
{
    public function up()
    {
        $fields = [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => '16',
                'DEFAULT'    => null,
                'after' => 'status',
            ],
        ];
        $this->forge->addColumn('packages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', 'type');
    }
}
