<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeExcerptandLeadTypeInContentTable extends Migration
{
    public function up()
    {
         $fields = [
            'name' => ['type' => 'VARCHAR', null=> true, 'constraint' => 150],

            'lead' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'excerpt' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
           
        ];
         $this->forge->modifyColumn('contents', $fields);
    }

    public function down()
    {
        //
    }
}
