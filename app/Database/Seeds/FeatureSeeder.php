<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FeatureSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $modules = $db->table('modules')->get()->getResultArray();
        $moduleMap = [];
        foreach ($modules as $m) {
            $moduleMap[strtolower(trim($m['name']))] = (int) $m['id'];
        }

        $features = [
            ['module' => 'Admin', 'name' => 'Users', 'code' => 'users', 'sort_order' => 1],
            ['module' => 'Admin', 'name' => 'Roles', 'code' => 'roles', 'sort_order' => 2],
            ['module' => 'Admin', 'name' => 'Modules', 'code' => 'modules', 'sort_order' => 3],
            ['module' => 'Admin', 'name' => 'Categories', 'code' => 'categories', 'sort_order' => 4],
            ['module' => 'Admin', 'name' => 'Status', 'code' => 'status', 'sort_order' => 5],
            ['module' => 'Admin', 'name' => 'Workflow', 'code' => 'workflows', 'sort_order' => 6],
            ['module' => 'Admin', 'name' => 'Companies', 'code' => 'companies', 'sort_order' => 7],
            ['module' => 'Admin', 'name' => 'Departments', 'code' => 'departments', 'sort_order' => 8],
            ['module' => 'Admin', 'name' => 'Site Settings', 'code' => 'site-settings', 'sort_order' => 9],
            ['module' => 'Admin', 'name' => 'Message Templates', 'code' => 'message-templates', 'sort_order' => 10],
            ['module' => 'Admin', 'name' => 'Document Templates', 'code' => 'document-templates', 'sort_order' => 11],

            ['module' => 'Career', 'name' => 'Jobs', 'code' => 'jobs', 'sort_order' => 1],
            ['module' => 'Career', 'name' => 'Job Posts', 'code' => 'job-posts', 'sort_order' => 2],
            ['module' => 'Career', 'name' => 'Applicants', 'code' => 'applicants', 'sort_order' => 3],
            ['module' => 'Career', 'name' => 'Applications', 'code' => 'applications', 'sort_order' => 4],

            ['module' => 'CMS', 'name' => 'Contents', 'code' => 'contents', 'sort_order' => 1],
        ];

        foreach ($features as $f) {
            $moduleId = $moduleMap[strtolower(trim($f['module']))] ?? null;
            if (! $moduleId) {
                continue;
            }

            $exists = $db->table('features')
                ->where('module_id', $moduleId)
                ->where('code', $f['code'])
                ->get()
                ->getRowArray();

            if (! $exists) {
                $db->table('features')->insert([
                    'module_id' => $moduleId,
                    'name' => $f['name'],
                    'code' => $f['code'],
                    'sort_order' => $f['sort_order'],
                    'date_created' => $now,
                ]);
            }
        }
    }
}