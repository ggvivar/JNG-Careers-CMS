<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'setting_key',
        'setting_value',
        'setting_type',
        'key_1',
        'key_2',
        'key_3',
        'autoload',
        'description',
        'date_created',
        'date_updated',
    ];

    public function getSetting(string $key, string $lang = 'en')
    {
        $row = $this->where('setting_key', $key)
            ->where('key_1', $lang)
            ->first();

        if (! $row && $lang !== 'en') {
            $row = $this->where('setting_key', $key)
                ->where('key_1', 'en')
                ->first();
        }

        if (! $row) {
            return null;
        }

        return $this->castValue($row['setting_value'], $row['setting_type']);
    }

    private function castValue($value, string $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) ((int) $value);

            case 'json':
                $decoded = json_decode((string) $value, true);
                return is_array($decoded) ? $decoded : [];

            default:
                return $value;
        }
    }
}