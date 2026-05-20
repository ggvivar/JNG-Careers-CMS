<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubCategoryToFeaturesTable extends Migration
{
    public function up()
    {
        $fields = [
            'sub_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
                'null'       => true,
                'after'      => 'module_id',
            ],
        ];

        $this->forge->addColumn('features', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('features', 'sub_category');
    }
}