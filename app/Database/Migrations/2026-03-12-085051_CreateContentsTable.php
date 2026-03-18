<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateContentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'main_content_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'module_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'category_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'description' => ['type' => 'TEXT', 'null' => true],
            'body' => ['type' => 'LONGTEXT', 'null' => true],
            'attachment' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tags' => ['type' => 'TEXT', 'null' => true],
            'validity_date_start' => ['type' => 'DATETIME', 'null' => true],
            'validity_date_end' => ['type' => 'DATETIME', 'null' => true],
            'rank' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'status_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'creator_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approvermatrix_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'curr_approver' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'date_approved' => ['type' => 'DATETIME', 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('module_id');
        $this->forge->addKey('category_id');
        $this->forge->addKey('status_id');
        $this->forge->addKey('creator_id');
        $this->forge->addKey('approvermatrix_id');
        $this->forge->addKey('curr_approver');
        $this->forge->createTable('contents');
    }

    public function down()
    {
        $this->forge->dropTable('contents');
    }
}