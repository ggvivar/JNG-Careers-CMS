<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailTemplateInWorkflowTransitionTable extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('workflow_transitions');

        if (! in_array('email_template_key', $fields, true)) {
            $this->forge->addColumn('workflow_transitions', [
                'email_template_key' => [
                    'type' => 'VARCHAR',
                    'constraint' => 100,
                    'null' => true,
                    'after' => 'grace_period',
                ],
            ]);
        }

        if (! in_array('send_email', $fields, true)) {
            $this->forge->addColumn('workflow_transitions', [
                'send_email' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'after' => 'email_template_key',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('workflow_transitions');

        if (in_array('send_email', $fields, true)) {
            $this->forge->dropColumn('workflow_transitions', 'send_email');
        }

        if (in_array('email_template_key', $fields, true)) {
            $this->forge->dropColumn('workflow_transitions', 'email_template_key');
        }
    }
}