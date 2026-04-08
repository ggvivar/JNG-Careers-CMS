<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkflowHistoriesTable extends Migration
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
            'record_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'status_id_from' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'status_id_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'assigned_from' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true
            ],
            'remarks' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'change_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'due_at' => [
                'type' => 'DATETIME',
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
        $this->forge->addKey(['feature_id', 'record_id']); 
        $this->forge->createTable('workflow_histories');
    }

    public function down()
    {
        $this->forge->dropTable('workflow_histories');
    }
}
