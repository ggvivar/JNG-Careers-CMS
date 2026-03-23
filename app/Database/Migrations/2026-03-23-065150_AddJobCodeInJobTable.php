<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJobCodeInJobTable extends Migration
{
    public function up()
    {
           $fields['job_code'] = [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'name',
            ];
        if (! empty($fields)) {
            $this->forge->addColumn('job', $fields);
        }
    }

    public function down()
    {
        //
    }
}
