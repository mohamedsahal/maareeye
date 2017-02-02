<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNoOfWarehouseInPackagesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('packages', [
            'no_of_warehouse' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,  // Set to false if you want it to be NOT NULL
                'after'      => 'no_of_customers',
                'comment'    => '-1 is for unlimited',
            ],
        ]);

        $this->forge->addColumn('users_packages', [
            'no_of_warehouse' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,  // Set to false if you want it to be NOT NULL
                'after'      => 'no_of_customers',
                'comment'    => '-1 is for unlimited',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('packages', 'no_of_warehouse');
        $this->forge->dropColumn('users_packages', 'no_of_warehouse');
    }
}
