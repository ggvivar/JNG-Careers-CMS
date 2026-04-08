<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicantEmploymentDetails extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'applicant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'employee_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'position' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'department' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
            ],
            'employment_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'date_hired' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'date_regularized' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'date_separated' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'salary' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('applicant_id');
        // $this->forge->addUniqueKey('applicant_id');

        $this->forge->createTable('applicant_employment_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('applicant_employment_details', true);
    }
}