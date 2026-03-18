<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $rows = [
            [
                'username' => 'jdelacruz',
                'password' => password_hash('applicant123', PASSWORD_DEFAULT),
                'firstname' => 'Juan',
                'middlename' => 'Santos',
                'lastname' => 'Dela Cruz',
                'email' => 'juan@example.com',
                'phone' => '09171234567',
                'city' => 'Pasig',
                'province' => 'Metro Manila',
                'resume' => 'juan_resume.pdf',
                'date_applied' => $now,
            ],
            [
                'username' => 'mgarcia',
                'password' => password_hash('applicant123', PASSWORD_DEFAULT),
                'firstname' => 'Maria',
                'middlename' => 'Lopez',
                'lastname' => 'Garcia',
                'email' => 'maria@example.com',
                'phone' => '09179876543',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'resume' => 'maria_resume.pdf',
                'date_applied' => $now,
            ],
        ];

        foreach ($rows as $row) {
            $exists = $db->table('applicants')->where('email', $row['email'])->get()->getRowArray();
            if (! $exists) {
                $row['date_created'] = $now;
                $db->table('applicants')->insert($row);
            }
        }
    }
}