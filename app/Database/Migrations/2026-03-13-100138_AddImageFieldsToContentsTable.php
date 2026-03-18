<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageFieldsToContentsTable extends Migration
{
    public function up()
    {
        $fields = [];

        if (! $this->db->fieldExists('image_path', 'contents')) {
            $fields['image_path'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'attachment',
            ];
        }

        if (! $this->db->fieldExists('image_url', 'contents')) {
            $fields['image_url'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'image_path',
            ];
        }

        if (! $this->db->fieldExists('external_link', 'contents')) {
            $fields['external_link'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'image_url',
            ];
        }

        if (! empty($fields)) {
            $this->forge->addColumn('contents', $fields);
        }
    }

    public function down()
    {
        foreach (['external_link', 'image_url', 'image_path'] as $field) {
            if ($this->db->fieldExists($field, 'contents')) {
                $this->forge->dropColumn('contents', $field);
            }
        }
    }
}