<?php

namespace App\Models;

use CodeIgniter\Model;

class ApproverMatrixModel extends Model
{
    protected $table = 'approvermatrix';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'approver_id_1',
        'approver_id_2',
        'approver_id_3',
        'approver_id_4',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}