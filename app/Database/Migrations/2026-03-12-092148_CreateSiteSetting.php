<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiteSettings extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 100],
            'site_url' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'api_key' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'favicon' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'social_media' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tel_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],  
            'email_contact' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'logo' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'key3' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('site_settings');
    }

    public function down()
    {
        $this->forge->dropTable('site_settings');
    }
}