<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveModuleFeatureMessageTemplate extends Migration
{
    public function up()
    {
        foreach (['module_id', 'feature_id'] as $field) {
            if ($this->db->fieldExists($field, 'message_templates')) {
                $this->forge->dropColumn('message_templates', $field);
            }
        }
    }

    public function down()
    {
        $fields = [];

        if (! $this->db->fieldExists('module_id', 'message_templates')) {
            $fields['module_id'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ];
        }

        if (! $this->db->fieldExists('feature_id', 'message_templates')) {
            $fields['feature_id'] = [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('message_templates', $fields);
        }
    }
}