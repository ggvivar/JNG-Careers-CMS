<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddImageDescriptionContentTable extends Migration
{
    public function up()
    {
        //
         $fields = [];
        // vdebug($this->db->fieldExists('name', 'contents'));
            $fields['image_description'] = [
                'type' => 'LONGTEXT',
                'null' => true,
                'after' => 'image_url',
            ];
        if (! empty($fields)) {
            $this->forge->addColumn('contents', $fields);
        }
    }

    public function down()
    {
        //
    }
}
