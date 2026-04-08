<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApplicantDocumentAttachments extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'applicant_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'document_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_ext' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
            ],
            'file_size' => [
                'type' => 'INT',
                'null' => true,
            ],
            'remarks' => [
                'type' => 'TEXT',
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
        $this->forge->addKey('applicant_id');
        $this->forge->createTable('applicant_document_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('applicant_document_attachments');
    }
}