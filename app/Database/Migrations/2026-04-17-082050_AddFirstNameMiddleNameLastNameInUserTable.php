<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddFirstNameMiddleNameLastNameInUserTable extends Migration
{
    public function up()
    {
        $fields = [
            'firstname' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'name',
            ],
            'middlename' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'firstname',
            ],
            'lastname' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'middlename',
            ],
        ];
        

        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['firstname','middlename','lastname']);
    }
}