<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ModuleSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $rows = [
            ['name' => 'Admin'],
            ['name' => 'Career'],
            ['name' => 'CMS'],
        ];

        foreach ($rows as $row) {
            $exists = $db->table('modules')->where('name', $row['name'])->get()->getRowArray();
            if (! $exists) {
                $db->table('modules')->insert([
                    'name' => $row['name'],
                    'date_created' => $now,
                ]);
            }
        }
    }
}