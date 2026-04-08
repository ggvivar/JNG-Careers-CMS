<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAssignedToAndDueAtInApplicationTable extends Migration
{
    
    public function up()
    {
        $fields = $this->db->getFieldNames('job_applications');
        if (! in_array('assigned_to', $fields, true)) {
            $this->forge->addColumn('job_applications', [
                'assigned_to' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'status_id',
                ],
            ]);
        }

        if (! in_array('due_at', $fields, true)) {
            $this->forge->addColumn('job_applications', [
                'due_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'assigned_to',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('job_applications');

        if (in_array('assigned_to', $fields, true)) {
            $this->forge->dropColumn('job_applications', 'assigned_to');
        }

        if (in_array('due_at', $fields, true)) {
            $this->forge->dropColumn('job_applications', 'due_at');
        }
    }
}
