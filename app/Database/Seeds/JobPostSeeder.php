<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class JobPostSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $jobs = $db->table('job')->get()->getResultArray();
        $company = $db->table('companies')->where('code', 'JNG')->get()->getRowArray();
        $department = $db->table('departments')->where('code', 'IT')->get()->getRowArray();
        $active = $db->table('status')->where('name', 'Active')->get()->getRowArray();

        foreach ($jobs as $job) {
            $exists = $db->table('job_list')->where('job_id', $job['id'])->get()->getRowArray();

            if (! $exists) {
                $db->table('job_list')->insert([
                    'job_id' => $job['id'],
                    'company_id' => $company['id'] ?? null,
                    'department_id' => $department['id'] ?? null,
                    'location' => 'Pasig City',
                    'salary_range' => '40000-70000',
                    'experience_range' => '2-5 years',
                    'rank_hiring' => 1,
                    'job_posted_date' => $now,
                    'status_id' => $active['id'] ?? null,
                    'valid_from' => $now,
                    'valid_to' => date('Y-m-d H:i:s', strtotime('+30 days')),
                    'date_created' => $now,
                ]);
            }
        }
    }
}