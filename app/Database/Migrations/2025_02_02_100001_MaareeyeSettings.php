<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Ensures general settings title is set to Maareeye.
 */
class MaareeyeSettings extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('settings')->where('variable', 'general');
        $row = $builder->get()->getRowArray();

        if ($row && !empty($row['value'])) {
            $value = json_decode($row['value'], true);
            if (is_array($value) && isset($value['title'])) {
                $title = trim((string) $value['title']);
                $lower = strtolower($title);
                $legacyNames = ['up biz', 'upbiz'];
                if (in_array($lower, $legacyNames)) {
                    $value['title'] = 'Maareeye';
                    $db->table('settings')
                        ->where('variable', 'general')
                        ->update(['value' => json_encode($value)]);
                }
            }
        }
    }

    public function down()
    {
        // No rollback
    }
}
