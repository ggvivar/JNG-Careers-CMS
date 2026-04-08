<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGroupTable extends Migration
{
    public function up()
    {
         $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'code' => [
                'type'       => 'VARCHAR',
                'constraint' => 11,
            ],
            'status_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
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
        $this->forge->addKey('unit_id');
        $this->forge->createTable('groups');
    }

    public function down()
    {
        //
        
        $this->forge->dropTable('groups');
    }
}
