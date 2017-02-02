<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTeamMembersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'business_ids' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'permissions' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'status' => [
               'type'           => 'INT',
                'constraint'     => 100,  
                'unsigned'       => false, 
                'auto_increment' => false,
                'default'        => 1,
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('team_members');
    }

    public function down()
    {
        $this->forge->dropTable('team_members');
    }
}
