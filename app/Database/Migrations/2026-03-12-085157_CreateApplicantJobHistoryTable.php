<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicantJobHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'applicant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'company_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'company_address' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'job_title' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'department' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'start_date' => ['type' => 'DATE', 'null' => true],
            'end_date' => ['type' => 'DATE', 'null' => true],
            'currently_working' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'responsibilities' => ['type' => 'TEXT', 'null' => true],
            'salary' => ['type' => 'DECIMAL', 'constraint' => '12,2', 'null' => true],
            'reason_for_leaving' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('applicant_id');
        $this->forge->createTable('applicant_job_history');
    }

    public function down()
    {
        $this->forge->dropTable('applicant_job_history');
    }
}