<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDocumentTemplatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'template_key' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'template_type' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'html',
            ],
            'file_name_pattern' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'body_template' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'source_file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'available_vars' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_deleted' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status_id');
        $this->forge->addUniqueKey('template_key');
        $this->forge->createTable('document_templates');
    }

    public function down()
    {
        $this->forge->dropTable('document_templates');
    }
}