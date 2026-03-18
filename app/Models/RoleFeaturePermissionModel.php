<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleFeaturePermissionModel extends Model
{
    protected $table = 'role_feature_permissions';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'role_id',
        'module_id',
        'feature_id',
        'can_view',
        'can_add',
        'can_edit',
        'can_delete',
        'can_export',
        'can_import',
        'date_created',
        'date_updated',
    ];
}