<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantEducationModel extends Model
{
    protected $table = 'applicant_education';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'school_name',
        'degree',
        'field_of_study',
        'start_year',
        'end_year',
        'honors',
        'date_created',
    ];

    public function getByApplicantId(int $applicantId): array
    {
        return $this->where('applicant_id', $applicantId)
            ->orderBy('id', 'DESC')
            ->findAll();
    }
}