<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApplicantJobHistorySeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();

        $data = [
            [
                'applicant_id' => 1,
                'company_name' => 'Tech Solutions Inc.',
                'company_address' => 'Ortigas, Pasig',
                'job_title' => 'Junior Developer',
                'department' => 'IT',
                'start_date' => '2018-01-10',
                'end_date' => '2021-03-20',
                'currently_working' => 0,
                'responsibilities' => 'Develop internal web systems',
                'salary' => 35000,
                'reason_for_leaving' => 'Career growth',
                'date_created' => date('Y-m-d H:i:s')
            ],
            [
                'applicant_id' => 2,
                'company_name' => 'Creative Agency PH',
                'company_address' => 'BGC Taguig',
                'job_title' => 'Graphic Designer',
                'department' => 'Marketing',
                'start_date' => '2020-06-01',
                'end_date' => null,
                'currently_working' => 1,
                'responsibilities' => 'Design marketing materials',
                'salary' => 30000,
                'reason_for_leaving' => '',
                'date_created' => date('Y-m-d H:i:s')
            ]
        ];

        $db->table('applicant_job_history')->insertBatch($data);
    }
}