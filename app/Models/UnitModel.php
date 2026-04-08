<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitModel extends Model
{
    protected $table = 'units';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'company_id',
        'name',
        'code',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}