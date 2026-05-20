<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLevelInJobPostTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('job_list', [
            'level' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'experience_range',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('job_list', 'level');
    }
}
