<?php

namespace App\Database\Seeders;

use CodeIgniter\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'setting_key' => 'site_name',
                'setting_value' => 'Careers@Joy-Notalg CMS',
                'setting_type' => 'string',
                'key_1' => 'en',
                'key_2' => '',
                'key_3' => '',
                'autoload' => 1,
                'description' => '',
                'date_created' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'admin_email',
                'setting_value' => 'careers@joy-nostalg.com',
                'setting_type' => 'string',
                'key_1' => 'en',
                'key_2' => '',
                'key_3' => '',
                'autoload' => 1,
                'description' => 'site admin email',
                'date_created' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'maintenance_mode',
                'setting_value' => '0',
                'setting_type' => 'boolean',
                'key_1' => 'en',
                'key_2' => '',
                'key_3' => '',
                'autoload' => 1,
                'description' => 'Toggle if site is maintenance',
                'date_created' => date('Y-m-d H:i:s')
            ],
            [
                'setting_key' => 'homepage_widgets',
                'setting_value' => '["news","blog","events"]',
                'setting_type' => 'json',
                'key_1' => 'en',
                'key_2' => '',
                'key_3' => '',
                'autoload' => 1,
                'description' => 'List of widgets on homepage.',
                'date_created' => date('Y-m-d H:i:s')
            ]
        ];

        $this->db->table('settings')->insertBatch($data);
    }
}