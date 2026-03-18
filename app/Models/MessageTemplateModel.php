<?php

namespace App\Models;

use CodeIgniter\Model;

class MessageTemplateModel extends Model
{
    protected $table = 'message_templates';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'name',
        'template_key',
        'channel',
        'source_table',
        'subject',
        'body_template',
        'available_vars',
        'status_id',
        'date_created',
        'date_updated',
        'date_deleted',
    ];
}