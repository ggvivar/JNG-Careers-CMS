<?php

namespace App\Models;

use CodeIgniter\Model;
namespace App\Models;

use CodeIgniter\Model;

class MessageTemplateDocumentModel extends Model
{
    protected $table = 'message_template_documents';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'message_template_id',
        'document_template_id',
        'date_created',
        'date_deleted',
    ];
}