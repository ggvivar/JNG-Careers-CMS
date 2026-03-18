<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSourceTableToMessageAndDocumentTemplatesTable extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('source_table', 'message_templates')) {
            $this->forge->addColumn('message_templates', [
                'source_table' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'channel',
                ],
            ]);
        }

        if (! $this->db->fieldExists('source_table', 'document_templates')) {
            $this->forge->addColumn('document_templates', [
                'source_table' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'template_type',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('source_table', 'message_templates')) {
            $this->forge->dropColumn('message_templates', 'source_table');
        }

        if ($this->db->fieldExists('source_table', 'document_templates')) {
            $this->forge->dropColumn('document_templates', 'source_table');
        }
    }
}