<?php

namespace App\Models;

use CodeIgniter\Model;

class UserCompanyModel extends Model
{
    protected $table = 'user_companies';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'user_id',
        'company_id',
        'date_created',
        'date_updated',
    ];
}