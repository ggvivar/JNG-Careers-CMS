<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlugtoContentAndJobPostTable extends Migration
{
    public function up()
    {
        $fields = [];
        // vdebug($this->db->fieldExists('name', 'contents'));
            $fields['slug'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'name',
            ];
        if (! empty($fields)) {
            $this->forge->addColumn('contents', $fields);
        }
        $fields = [];
            $fields['slug'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'name',
            ];
        if (! empty($fields)) {
            $this->forge->addColumn('job', $fields);
        }
    }

    public function down()
    {
        foreach (['slug'] as $field) {
            if ($this->db->fieldExists($field, 'contents')) {
                $this->forge->dropColumn('slug', $field);
            } 
            if ($this->db->fieldExists($field, 'job')) {
                $this->forge->dropColumn('slug', $field);
            }
        }
    }
}
