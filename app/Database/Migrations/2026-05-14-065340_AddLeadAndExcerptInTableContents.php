<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLeadAndExcerptInTableContents extends Migration
{
    public function up()
    {
         $fields = [
            'lead' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'slug',
            ],
            'excerpt' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'lead',
            ],
        ];
        

        $this->forge->addColumn('contents', $fields);
    }

    public function down()
    {
        //
    }
}
