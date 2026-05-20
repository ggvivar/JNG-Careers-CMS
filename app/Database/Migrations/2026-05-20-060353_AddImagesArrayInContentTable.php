<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImagesArrayInContentTable extends Migration
{
    public function up()
    {
        $fields = [
            'images' => [
                'type'       => 'TEXT',
                'null'       => true,
                'after'      => 'image_description',
            ],
           
        ];
        

        $this->forge->addColumn('contents', $fields);
    }

    public function down()
    {
        //
    }
}
