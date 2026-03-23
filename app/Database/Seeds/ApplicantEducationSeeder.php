<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ApplicantEducationSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
       
        $data = [
            [
                'applicant_id' => 1,
                'school_name' => 'University of the Philippines',
                // 'school_address' => 'Diliman, QC',
                'degree' => 'BS Computer Science',
                'field_of_study' => 'Software Development',
                'start_year' => '2013',
                'end_year' => '2017',
                // 'graduated' => 1,
                'honors' => 'Cum Laude',
                'date_created' => date('Y-m-d H:i:s')
            ],
            [
                'applicant_id' => 2,
                'school_name' => 'De La Salle University',
                // 'school_address' => 'Taft Ave',
                'degree' => 'BS Information Systems',
                'field_of_study' => 'Business IT',
                'start_year' => '2015',
                'end_year' => '2019',
                // 'graduated' => 1,
                'honors' => '',
                'date_created' => date('Y-m-d H:i:s')
            ]
        ];

        $db->table('applicant_education')->insertBatch($data);
    }
}