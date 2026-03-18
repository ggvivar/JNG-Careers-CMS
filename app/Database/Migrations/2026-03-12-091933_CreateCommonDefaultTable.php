<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCommonDefaults extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'key1' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'key2' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'key3' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'key4' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'key5' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'value' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'definition' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('common_defaults');
    }

    public function down()
    {
        $this->forge->dropTable('common_defaults');
    }
}