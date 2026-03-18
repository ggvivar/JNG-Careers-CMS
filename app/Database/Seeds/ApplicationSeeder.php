<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApplicationSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $applicants = $db->table('applicants')->get()->getResultArray();
        $jobPosts = $db->table('job_list')->get()->getResultArray();
        $applied = $db->table('status')->where('name', 'Applied')->get()->getRowArray();

        if (empty($jobPosts)) {
            return;
        }

        foreach ($applicants as $index => $applicant) {
            $jobPost = $jobPosts[$index % count($jobPosts)];

            $exists = $db->table('job_applications')
                ->where('applicant_id', $applicant['id'])
                ->where('job_list_id', $jobPost['id'])
                ->get()
                ->getRowArray();

            if (! $exists) {
                $db->table('job_applications')->insert([
                    'applicant_id' => $applicant['id'],
                    'job_list_id' => $jobPost['id'],
                    'status_id' => $applied['id'] ?? null,
                    'source' => 'Website',
                    'applied_at' => $now,
                    'date_created' => $now,
                ]);
            }
        }
    }
}