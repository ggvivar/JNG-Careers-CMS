<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $companies = $db->table('companies')->get()->getResultArray();
        $companyMap = [];
        foreach ($companies as $c) {
            $companyMap[$c['code']] = $c['id'];
        }

        $rows = [
            [
                'company_id' => $companyMap['JNG'] ?? null,
                'name' => 'Human Resources',
                'code' => 'HR',
            ],
            [
                'company_id' => $companyMap['JNG'] ?? null,
                'name' => 'Information Technology',
                'code' => 'IT',
            ],
            [
                'company_id' => $companyMap['JNPV'] ?? null,
                'name' => 'Marketing',
                'code' => 'MKT',
            ],
        ];

        foreach ($rows as $row) {
            $exists = $db->table('departments')
                ->where('company_id', $row['company_id'])
                ->where('name', $row['name'])
                ->get()
                ->getRowArray();

            if (! $exists) {
                $row['date_created'] = $now;
                $db->table('departments')->insert($row);
            }
        }
    }
}