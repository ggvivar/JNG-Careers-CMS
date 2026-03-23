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

    public function getByApplicantId(int $applicantId): array
    {
        return $this->db->table($this->table . ' ja')
            ->select('ja.*, j.name as job_name, s.name as status_name')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.applicant_id', $applicantId)
            ->where('ja.date_deleted', null)
            ->orderBy('ja.id', 'DESC')
            ->get()
            ->getResultArray();
    }
}