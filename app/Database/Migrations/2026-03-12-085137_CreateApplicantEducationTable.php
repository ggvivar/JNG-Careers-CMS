<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicantEducationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'applicant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'school_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'degree' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'field_of_study' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'start_year' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'end_year' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'honors' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('applicant_id');
        $this->forge->createTable('applicant_education');
    }

    public function down()
    {
        $this->forge->dropTable('applicant_education');
    }
}