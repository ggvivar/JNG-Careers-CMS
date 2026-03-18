<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
                'unsigned' => true
            ],
            'setting_key' => [
                'type' => 'VARCHAR',
                'constraint' => 150
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'setting_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'string'
            ],
            'key_1' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'en'
            ],
            'key_2' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'en'
            ],
            'key_3' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'en'
            ],
            'autoload' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['setting_key','language_code']);
        $this->forge->createTable('settings');
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}