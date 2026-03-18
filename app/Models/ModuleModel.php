<?php

namespace App\Models;

use CodeIgniter\Model;

class ModuleModel extends Model
{
    protected $table = 'modules';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'key1',
        'key2',
        'key3',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}