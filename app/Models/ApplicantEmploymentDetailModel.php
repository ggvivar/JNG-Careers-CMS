<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantEmploymentDetailModel extends Model
{
    protected $table = 'applicant_employment_details';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'employee_no',
        'position',
        'department',
        'employment_type',
        'date_hired',
        'date_regularized',
        'date_separated',
        'salary',
        'status',
        'remarks',
        'date_created',
        'date_updated',
    ];

    public function getByApplicantId(int $applicantId): ?array
    {
        return $this->where('applicant_id', $applicantId)->first();
    }
}