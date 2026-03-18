<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username' => ['type' => 'VARCHAR', 'constraint' => 100],
            'password' => ['type' => 'VARCHAR', 'constraint' => 255],
            'email' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150, 'null' => true],
            'company_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'department_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'role_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('company_id');
        $this->forge->addKey('department_id');
        $this->forge->addKey('role_id');
        $this->forge->addKey('status_id');
        $this->forge->addUniqueKey('username');
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}