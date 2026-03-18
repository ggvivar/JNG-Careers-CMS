<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveBodyTemplateDocumentTemplateTable extends Migration
{
    public function up()
    {
        if ($this->db->fieldExists('body_template', 'document_templates')) {
            $this->forge->dropColumn('document_templates', 'body_template');
        }
    }

    public function down()
    {
        if (! $this->db->fieldExists('body_template', 'document_templates')) {
            $this->forge->addColumn('document_templates', [
                'body_template' => [
                    'type' => 'LONGTEXT',
                    'null' => true,
                ],
            ]);
        }
    }
}