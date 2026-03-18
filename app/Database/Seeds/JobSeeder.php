<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JobSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $draftStatus = $db->table('status')->where('name', 'Draft')->get()->getRowArray();

        $rows = [
            [
                'name' => 'Software Developer',
                'description' => 'Build and maintain web applications.',
                'requirement' => 'PHP, MySQL, JavaScript',
                'status_id' => $draftStatus['id'] ?? null,
            ],
            [
                'name' => 'HR Officer',
                'description' => 'Manage recruitment and employee records.',
                'requirement' => 'HR experience and communication skills',
                'status_id' => $draftStatus['id'] ?? null,
            ],
        ];

        foreach ($rows as $row) {
            $exists = $db->table('job')->where('name', $row['name'])->get()->getRowArray();
            if (! $exists) {
                $row['date_created'] = $now;
                $db->table('job')->insert($row);
            }
        }
    }
}