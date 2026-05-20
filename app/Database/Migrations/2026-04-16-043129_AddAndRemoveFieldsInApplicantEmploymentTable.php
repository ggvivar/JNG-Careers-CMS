<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAndRemoveFieldsInApplicantEmploymentTable extends Migration
{
    public function up()
    {

        $this->forge->addColumn('applicant_employment_details', [
            'job_post_id' => [
                'type'       => 'int',
                'after'      => 'applicant_id',
            ],
        ]);
        
        $this->forge->addColumn('applicant_employment_details', [
            'date_start' => [
                'type' => 'DATETIME',
                'null' => true,
                'after'      => 'date_hired',
            ],
        ]);
        $this->forge->dropColumn('applicant_employment_details', 'department');
        $this->forge->dropColumn('applicant_employment_details', 'employment_type');
        $this->forge->dropColumn('applicant_employment_details', 'salary');
        $this->forge->dropColumn('applicant_employment_details', 'position');

    }
public function down()
    {
    }
}