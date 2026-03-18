<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $rows = [
            ['name' => 'Super Admin'],
            ['name' => 'Admin Manager'],
            ['name' => 'Career Manager'],
            ['name' => 'CMS Editor'],
        ];

        foreach ($rows as $row) {
            $exists = $db->table('roles')->where('name', $row['name'])->get()->getRowArray();
            if (! $exists) {
                $db->table('roles')->insert([
                    'name' => $row['name'],
                    'date_created' => $now,
                ]);
            }
        }
    }
}