<?php

namespace App\Models;

use CodeIgniter\Model;

class GroupModel extends Model
{
    protected $table = 'groups';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'unit_id',
        'name',
        'code',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}