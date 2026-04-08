<?php

namespace App\Models;

use CodeIgniter\Model;

class CommonDefaultModel extends Model
{
    protected $table            = 'common_defaults';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'key1',
        'key2',
        'key3',
        'key4',
        'key5',
        'value',
        'definition',
        'date_created',
        'date_updated',
        'date_deleted',
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Use your actual database datetime columns
    protected $useTimestamps = false;

    // Validation
    protected $validationRules = [
        'key1'  => 'required|max_length[255]',
        'value' => 'required|max_length[255]',
        'key2'  => 'permit_empty|max_length[255]',
        'key3'  => 'permit_empty|max_length[255]',
        'key4'  => 'permit_empty|max_length[255]',
        'key5'  => 'permit_empty|max_length[255]',
        'definition' => 'permit_empty',
    ];

    protected $validationMessages = [
        'key1' => [
            'required' => 'Group / Category is required.',
        ],
        'value' => [
            'required' => 'Value is required.',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
}