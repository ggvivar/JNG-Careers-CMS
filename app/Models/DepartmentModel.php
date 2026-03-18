<?php

namespace App\Models;

use CodeIgniter\Model;

class DepartmentModel extends Model
{
    protected $table = 'departments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'company_id',
        'name',
        'code',
        'manager_user_id',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}