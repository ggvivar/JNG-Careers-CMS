<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRoleFeaturePermissionsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'role_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'module_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'feature_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'can_view' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_add' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_edit' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_delete' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_export' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'can_import' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'date_created' => ['type' => 'DATETIME', 'null' => true],
            'date_updated' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('role_id');
        $this->forge->addKey('module_id');
        $this->forge->addKey('feature_id');
        $this->forge->addUniqueKey(['role_id', 'feature_id']);
        $this->forge->createTable('role_feature_permissions');
    }

    public function down()
    {
        $this->forge->dropTable('role_feature_permissions');
    }
}