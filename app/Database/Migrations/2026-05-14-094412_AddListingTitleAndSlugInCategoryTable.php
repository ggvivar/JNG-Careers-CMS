<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddListingTitleInCategoryTable extends Migration
{
    public function up()
    {
        $fields = [
            'listing_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'name',
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'listing_title',
            ],
        ];
        

        $this->forge->addColumn('category', $fields);
    }

    public function down()
    {
        //
    }
}
