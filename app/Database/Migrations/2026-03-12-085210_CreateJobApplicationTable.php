<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobApplicationsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'applicant_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'job_list_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'source' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'applied_at' => ['type' => 'DATETIME', 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('applicant_id');
        $this->forge->addKey('job_list_id');
        $this->forge->addKey('status_id');
        $this->forge->createTable('job_applications');
    }

    public function down()
    {
        $this->forge->dropTable('job_applications');
    }
}