<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowTransitionsTable extends Migration
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
            'feature_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_id_from' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null'     => true
            ],
            'status_id_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'grace_period' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' =>true
            ],
            'require_remarks' => [
                'type' => 'boolean',
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' =>0
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
        $this->forge->addKey(['feature_id', 'status_id_from']);
        $this->forge->addKey(['feature_id', 'status_id_to']);
        $this->forge->createTable('workflow_transitions');
    }

    public function down()
    {
        $this->forge->dropTable('workflow_transitions');
    }
}
