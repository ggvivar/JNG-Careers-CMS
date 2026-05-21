<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeNametoNullableInContentTable extends Migration
{
    public function up()
    {
         $fields = [
            'name' => ['type' => 'VARCHAR', null=> true, 'constraint' => 150],
           
        ];
         $this->forge->modifyColumn('contents', $fields);
    }

    public function down()
    {
        //
    }
}
