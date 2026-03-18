<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table = 'category';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'module_id',
        'name',
        'key1',
        'key2',
        'key3',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}