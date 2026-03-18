<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveScopeFieldDocumentTemplateTable extends Migration
{
    public function up()
    {
        foreach (['module_id', 'feature_id', 'category_id'] as $field) {
            if ($this->db->fieldExists($field, 'document_templates')) {
                $this->forge->dropColumn('document_templates', $field);
            }
        }
    }

    public function down()
    {
        $fields = [];

        foreach (['module_id', 'feature_id', 'category_id'] as $field) {
            if (! $this->db->fieldExists($field, 'document_templates')) {
                $fields[$field] = [
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => true,
                    'null' => true,
                ];
            }
        }

        if (! empty($fields)) {
            $this->forge->addColumn('document_templates', $fields);
        }
    }
}