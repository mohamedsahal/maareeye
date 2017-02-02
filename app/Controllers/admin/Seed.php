<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Database\Seeds\AddGroupInGroupTable ;
class Seed extends BaseController
{
    public function runSeeder()
    {
        $seeder = \Config\Database::seeder();
        $seeder->call(AddGroupInGroupTable::class);

        return 'Seeder executed successfully';
    }
}
