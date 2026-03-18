<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFeaturesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'module_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'code' => ['type' => 'VARCHAR', 'constraint' => 100],
            'description' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'sort_order' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 0],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('module_id');
        $this->forge->addKey('status_id');
        $this->forge->createTable('features');
    }

    public function down()
    {
        $this->forge->dropTable('features');
    }
}