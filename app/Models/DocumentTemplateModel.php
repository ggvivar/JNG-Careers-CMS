<?php

namespace App\Models;

use CodeIgniter\Model;

class DocumentTemplateModel extends Model
{
    protected $table = 'document_templates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

   protected $allowedFields = [
    'name',
    'template_key',
    'template_type',
    'source_table',
    'file_name_pattern',
    'source_file_path',
    'available_vars',
    'description',
    'status_id',
    'date_created',
    'date_updated',
    'date_deleted',
];
}