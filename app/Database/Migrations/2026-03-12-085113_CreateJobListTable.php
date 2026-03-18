<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobListTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'job_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'company_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'department_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'salary_range' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'experience_range' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'rank_hiring' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'job_posted_date' => ['type' => 'DATETIME', 'null' => true],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'valid_from' => ['type' => 'DATETIME', 'null' => true],
            'valid_to' => ['type' => 'DATETIME', 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('job_id');
        $this->forge->addKey('company_id');
        $this->forge->addKey('department_id');
        $this->forge->addKey('status_id');
        $this->forge->createTable('job_list');
    }

    public function down()
    {
        $this->forge->dropTable('job_list');
    }
}