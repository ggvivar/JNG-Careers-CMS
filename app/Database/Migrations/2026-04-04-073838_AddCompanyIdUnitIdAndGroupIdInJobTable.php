<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompanyIdUnitIdAndGroupIdInJobTable extends Migration
{
     public function up()
    {
        $fields = [
            'company_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'job_code',
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'company_id',
            ],
            'group_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'after'      => 'unit_id',
            ],
        ];

        $this->forge->addColumn('job', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('job', 'unit_id');
        $this->forge->dropColumn('job', 'group_id');
    }
}
