<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicantsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'firstname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'middlename' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'lastname' => ['type' => 'VARCHAR', 'constraint' => 100],
            'suffix' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'birthdate' => ['type' => 'DATE', 'null' => true],
            'gender' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'civil_status' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nationality' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'address' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'city' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'province' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'zip_code' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'resume' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'cover_letter' => ['type' => 'TEXT', 'null' => true],
            'certification' => ['type' => 'JSON', 'null' => true],
            'date_applied' => ['type' => 'DATETIME', 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('username');
        $this->forge->createTable('applicants');
    }

    public function down()
    {
        $this->forge->dropTable('applicants');
    }
}