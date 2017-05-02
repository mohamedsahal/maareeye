<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Database\Seeds\AddGroupInGroupTable;
use App\Database\Seeds\MaareeyeSeeder;

class Seed extends BaseController
{
    public function runSeeder()
    {
        $seeder = \Config\Database::seeder();
        $seeder->call(AddGroupInGroupTable::class);
        $seeder->call(MaareeyeSeeder::class);

        return 'Seeder executed successfully';
    }
}
