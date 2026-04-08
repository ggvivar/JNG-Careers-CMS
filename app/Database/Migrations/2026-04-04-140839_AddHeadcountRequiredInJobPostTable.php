<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddHeadcountRequiredInJobPostTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('job_list', [
            'headcount_required' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'rank_hiring',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('job_list', 'headcount_required');
    }
}
