<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicantTokensTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'auto_increment' => true, 'unsigned' => true],
            'applicant_id' => ['type' => 'INT', 'unsigned' => true],
            'token' => ['type' => 'VARCHAR', 'constraint' => 128],
            'device_name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'ip_address' => ['type' => 'VARCHAR', 'constraint' => 45, 'null' => true],
            'user_agent' => ['type' => 'TEXT', 'null' => true],
            'expires_at' => ['type' => 'DATETIME', 'null' => true],
            'revoked_at' => ['type' => 'DATETIME', 'null' => true],
            'created_at' => ['type' => 'DATETIME'],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('token');
        $this->forge->addForeignKey('applicant_id', 'applicants', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('applicant_tokens');
    }

    public function down()
    {
        $this->forge->dropTable('applicant_tokens');
    }
}