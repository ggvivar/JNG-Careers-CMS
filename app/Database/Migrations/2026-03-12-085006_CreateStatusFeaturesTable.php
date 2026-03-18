<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStatusFeaturesTable extends Migration
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
            'status_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'feature_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'date_created' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'date_updated' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('status_id');
        $this->forge->addKey('feature_id');
        $this->forge->addUniqueKey(['status_id', 'feature_id']);
        $this->forge->createTable('status_features');
    }

    public function down()
    {
        $this->forge->dropTable('status_features');
    }
}