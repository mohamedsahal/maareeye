<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBrandToUserUsersPackagesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users_packages', [
            'no_of_brands' => [
                'type'       => 'VARCHAR',
                'constraint' => 255, // Specify length
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users_packages', 'no_of_brands');
    }
}
