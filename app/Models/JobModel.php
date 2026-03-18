<?php

namespace App\Models;

use CodeIgniter\Model;

class JobModel extends Model
{
    protected $table = 'job';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'description',
        'requirement',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}