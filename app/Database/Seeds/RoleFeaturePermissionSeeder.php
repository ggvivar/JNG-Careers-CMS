<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RoleFeaturePermissionSeeder extends Seeder
{
    public function run()
    {
        $db = db_connect();
        $now = date('Y-m-d H:i:s');

        $roles = $db->table('roles')->get()->getResultArray();
        $features = $db->table('features')->get()->getResultArray();

        $roleMap = [];
        foreach ($roles as $r) {
            $roleMap[$r['name']] = $r['id'];
        }

        $featureMap = [];
        foreach ($features as $f) {
            $featureMap[$f['code']] = $f;
        }

        $rules = [
            'Super Admin' => [
                'users','roles','modules','categories','status','companies','departments',
                'site-settings','message-templates','document-templates',
                'jobs','job-posts','applicants','applications','contents',
            ],
            'Admin Manager' => [
                'users','roles','modules','categories','status','companies','departments',
                'site-settings','message-templates','document-templates',
            ],
            'Career Manager' => [
                'jobs','job-posts','applicants','applications',
            ],
            'CMS Editor' => [
                'contents',
            ],
        ];

        foreach ($rules as $roleName => $codes) {
            $roleId = $roleMap[$roleName] ?? null;
            if (! $roleId) {
                continue;
            }

            foreach ($codes as $code) {
                $feature = $featureMap[$code] ?? null;
                if (! $feature) {
                    continue;
                }

                $data = [
                    'role_id' => $roleId,
                    'module_id' => $feature['module_id'],
                    'feature_id' => $feature['id'],
                    'can_view' => 1,
                    'can_add' => 1,
                    'can_edit' => 1,
                    'can_delete' => $roleName === 'CMS Editor' ? 0 : 1,
                    'can_export' => 1,
                    'can_import' => $roleName === 'CMS Editor' ? 0 : 1,
                    'date_created' => $now,
                    'date_updated' => $now,
                ];

                $exists = $db->table('role_feature_permissions')
                    ->where('role_id', $roleId)
                    ->where('feature_id', $feature['id'])
                    ->get()
                    ->getRowArray();

                if (! $exists) {
                    $db->table('role_feature_permissions')->insert($data);
                }
            }
        }
    }
}