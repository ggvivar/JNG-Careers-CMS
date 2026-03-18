<?php

namespace App\Models;

use CodeIgniter\Model;

class StatusFeatureModel extends Model
{
    protected $table = 'status_features';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'status_id',
        'feature_id',
        'date_created',
        'date_updated',
    ];
}