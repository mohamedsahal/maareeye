<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPackagesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('packages', [
            'no_of_brands' => [
                'type'       => 'VARCHAR',
                'constraint' => 255, // Specify length
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', 'no_of_brands');
    }
}
