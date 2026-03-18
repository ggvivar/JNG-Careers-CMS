<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $rows = [
            [
                'name' => 'Joy Nostalg Group',
                'code' => 'JNG',
                'address' => 'Pasig City',
                'contact_no' => '0287000000',
                'email' => 'info@joynostalg.com',
            ],
            [
                'name' => 'JN Property Ventures',
                'code' => 'JNPV',
                'address' => 'Taguig City',
                'contact_no' => '0287111111',
                'email' => 'property@joynostalg.com',
            ],
        ];

        foreach ($rows as $row) {
            $exists = $db->table('companies')->where('name', $row['name'])->get()->getRowArray();
            if (! $exists) {
                $row['date_created'] = $now;
                $db->table('companies')->insert($row);
            }
        }
    }
}