<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ReplaceHindiWithSomali extends Migration
{
    public function up()
    {
        // Replace Hindi with Somali in the languages table
        $this->db->table('languages')
            ->where('code', 'hi')
            ->update([
                'language' => 'somali',
                'code'     => 'so',
            ]);
    }

    public function down()
    {
        // Revert: change Somali back to Hindi
        $this->db->table('languages')
            ->where('code', 'so')
            ->update([
                'language' => 'hindi',
                'code'     => 'hi',
            ]);
    }
}
