<?php

namespace App\Models;

use CodeIgniter\Model;

class CompanyModel extends Model
{
    protected $table = 'companies';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'code',
        'address',
        'contact_no',
        'email',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}