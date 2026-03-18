<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateApproverMatrixTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'approver_id_1' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approver_id_2' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approver_id_3' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'approver_id_4' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
            'date_deleted' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('approvermatrix');
    }

    public function down()
    {
        $this->forge->dropTable('approvermatrix');
    }
}