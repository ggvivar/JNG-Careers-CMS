<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CommonDefaultSeeder extends Seeder
{
    public function run()
    {
        $table = $this->db->table('common_defaults');

        $rows = [
            [
                'key1' => 'Gender',
                'key2' => null,
                'key3' => null,
                'key4' => null,
                'key5' => null,
                'value' => 'Male',
                'definition' => 'Male gender',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => null,
                'date_deleted' => null,
            ],
            [
                'key1' => 'Gender',
                'key2' => null,
                'key3' => null,
                'key4' => null,
                'key5' => null,
                'value' => 'Female',
                'definition' => 'Female gender',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => null,
                'date_deleted' => null,
            ],
            [
                'key1' => 'Civil Status',
                'key2' => null,
                'key3' => null,
                'key4' => null,
                'key5' => null,
                'value' => 'Single',
                'definition' => 'Single civil status',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => null,
                'date_deleted' => null,
            ],
            [
                'key1' => 'Civil Status',
                'key2' => null,
                'key3' => null,
                'key4' => null,
                'key5' => null,
                'value' => 'Married',
                'definition' => 'Married civil status',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => null,
                'date_deleted' => null,
            ],
            [
                'key1' => 'Civil Status',
                'key2' => null,
                'key3' => null,
                'key4' => null,
                'key5' => null,
                'value' => 'Widowed',
                'definition' => 'Widowed civil status',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => null,
                'date_deleted' => null,
            ],
            [
                'key1' => 'Civil Status',
                'key2' => null,
                'key3' => null,
                'key4' => null,
                'key5' => null,
                'value' => 'Separated',
                'definition' => 'Separated civil status',
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => null,
                'date_deleted' => null,
            ],
        ];

        foreach ($rows as $row) {
            $exists = $this->db->table('common_defaults')
                ->where('key1', $row['key1'])
                ->where('value', $row['value'])
                ->where('date_deleted', null)
                ->countAllResults();

            if (! $exists) {
                $table->insert($row);
            }
        }
    }
}