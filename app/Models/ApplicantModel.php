<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantTokenModel extends Model
{
    protected $table = 'applicant_tokens';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
    'username',
    'password',
    'firstname',
    'middlename',
    'lastname',
    'suffix',
    'email',
    'phone',
    'birthdate',
    'gender',
    'civil_status',
    'nationality',
    'address',
    'city',
    'province',
    'zip_code',
    'resume',
    'cover_letter',
    'date_applied',
    'date_created',
    'date_updated',
    'date_deleted',
];
}