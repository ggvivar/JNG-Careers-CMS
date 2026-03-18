<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantTokenModel extends Model
{
    protected $table = 'applicant_tokens';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'applicant_id',
        'token',
        'device_name',
        'ip_address',
        'user_agent',
        'expires_at',
        'revoked_at',
        'created_at',
    ];
}