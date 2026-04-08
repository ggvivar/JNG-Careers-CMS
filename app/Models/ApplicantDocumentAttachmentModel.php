<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantDocumentAttachmentModel extends Model
{
    protected $table = 'applicant_document_attachments';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'document_type',
        'file_name',
        'file_path',
        'file_ext',
        'file_size',
        'remarks',
        'date_created',
        'date_updated',
        'date_deleted',
    ];

    public function getByApplicantId(int $applicantId): array
    {
        return $this->where('applicant_id', $applicantId)
            ->where('date_deleted', null)
            ->orderBy('id', 'DESC')
            ->findAll();
    }

    public function getResumeByApplicantId(int $applicantId): ?array
    {
        return $this->where('applicant_id', $applicantId)
            ->where('document_type', 'Resume')
            ->where('date_deleted', null)
            ->orderBy('id', 'DESC')
            ->first();
    }
}