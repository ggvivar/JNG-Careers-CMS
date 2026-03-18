<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserCompaniesTable extends Migration
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
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'company_id' => [
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
        $this->forge->addKey('user_id');
        $this->forge->addKey('company_id');
        $this->forge->addUniqueKey(['user_id', 'company_id']);
        $this->forge->createTable('user_companies');
        if ($this->db->fieldExists('company_id', 'users')) {
            $this->forge->dropColumn('users', 'company_id');
        }
    }

    public function down()
    {
        $this->forge->dropTable('user_companies');
    }
    
}