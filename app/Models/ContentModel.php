<?php

namespace App\Models;

use CodeIgniter\Model;

class ContentModel extends Model
{
    protected $table = 'contents';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        'main_content_id',
        'module_id',
        'category_id',
        'name',
        'slug',
        'description',
        'body',
        'attachment',
        'image_path',
        'image_url',
        'image_description',
        'external_link',
        'tags',
        'validity_date_start',
        'validity_date_end',
        'rank',
        'status_id',
        'creator_id',
        'approvermatrix_id',
        'curr_approver',
        'date_approved',
        'date_created',
        'date_updated',
        'date_deleted',
    ];

    protected $createdField  = 'date_created';
    protected $updatedField  = 'date_updated';
    protected $deletedField  = 'date_deleted';
}