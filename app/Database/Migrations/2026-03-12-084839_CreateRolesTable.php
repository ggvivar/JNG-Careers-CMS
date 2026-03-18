<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRolesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status_id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('roles');
    }

    public function down()
    {
        $this->forge->dropTable('roles');
    }
}