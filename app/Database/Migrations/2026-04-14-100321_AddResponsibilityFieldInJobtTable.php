<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResponsibilityFieldInJobtTable extends Migration
{
     public function up()
    {
        $this->forge->addColumn('job', [
            'responsibility' => [
                'type'       => 'text',
                'null'       => true,
                'after'      => 'description',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('job', 'responsibility');
    }
}
