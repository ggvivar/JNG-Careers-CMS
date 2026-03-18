<?php

namespace App\Models;

use CodeIgniter\Model;

class JobApplicationModel extends Model
{
    protected $table = 'job_applications';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'job_list_id',
        'status_id',
        'source',
        'applied_at',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}