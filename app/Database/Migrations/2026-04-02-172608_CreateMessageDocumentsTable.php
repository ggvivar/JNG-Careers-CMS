<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMessageDocumentsTable extends Migration
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
            'message_template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'document_template_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
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
        $this->forge->addKey(['message_template_id', 'document_template_id']); 
        $this->forge->createTable('message_template_documents');
    }

    public function down()
    {
        $this->forge->dropTable('message_template_documents');
    }
}
