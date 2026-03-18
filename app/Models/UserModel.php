<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'username',
        'password',
        'email',
        'name',
        'company_id',
        'department_id',
        'role_id',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}