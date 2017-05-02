<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Ensures general settings title is set to Maareeye.
 */
class MaareeyeSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $row = $db->table('settings')->where('variable', 'general')->get()->getRowArray();

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
}
