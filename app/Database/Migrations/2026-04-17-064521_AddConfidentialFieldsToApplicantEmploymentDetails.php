<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddConfidentialFieldsToApplicantEmploymentDetails extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('position', 'applicant_employment_details')) {
            $fields['position'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'employee_no',
            ];
        }

        if (! $this->db->fieldExists('department', 'applicant_employment_details')) {
            $fields['department'] = [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'position',
            ];
        }

        if (! $this->db->fieldExists('employment_type', 'applicant_employment_details')) {
            $fields['employment_type'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'department',
            ];
        }

        if (! $this->db->fieldExists('salary_encrypted', 'applicant_employment_details')) {
            $fields['salary_encrypted'] = [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'employment_type',
            ];
        }

        if (! $this->db->fieldExists('salary_band', 'applicant_employment_details')) {
            $fields['salary_band'] = [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'salary_encrypted',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('applicant_employment_details', $fields);
        }

        if ($this->db->fieldExists('salary', 'applicant_employment_details')) {
            $this->forge->dropColumn('applicant_employment_details', 'salary');
        }
    }

    public function down()
    {
        $fields = [];

        if (! $this->db->fieldExists('salary', 'applicant_employment_details')) {
            $fields['salary'] = [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'after'      => 'employment_type',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('applicant_employment_details', $fields);
        }

        foreach (['salary_band', 'salary_encrypted', 'employment_type', 'department', 'position'] as $column) {
            if ($this->db->fieldExists($column, 'applicant_employment_details')) {
                $this->forge->dropColumn('applicant_employment_details', $column);
            }
        }
    }
}