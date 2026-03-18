<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantJobHistoryModel extends Model
{
    protected $table = 'applicant_job_history';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'company_name',
        'company_address',
        'job_title',
        'department',
        'start_date',
        'end_date',
        'currently_working',
        'responsibilities',
        'salary',
        'reason_for_leaving',
        'date_created',
    ];
}