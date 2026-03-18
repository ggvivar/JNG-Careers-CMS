<?php

namespace App\Models;

use CodeIgniter\Model;

class FeatureModel extends Model
{
    protected $table = 'features';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'module_id',
        'name',
        'code',
        'description',
        'sort_order',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}