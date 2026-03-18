<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class StatusSeeder extends Seeder
{
    public function run()
    {
        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        $features = $db->table('features')->get()->getResultArray();
        $featureMap = [];
        foreach ($features as $f) {
            $featureMap[strtolower(trim($f['code']))] = (int) $f['id'];
        }

        $statusMap = [
            'Active'   => ['users', 'roles', 'modules', 'categories', 'status', 'companies', 'departments', 'site-settings', 'message-templates', 'document-templates', 'jobs', 'job-posts', 'contents'],
            'Inactive' => ['users', 'roles', 'companies', 'departments', 'site-settings', 'message-templates', 'document-templates'],
            'Draft'     => ['jobs', 'job-posts', 'contents'],
            'Submitted' => ['contents'],
            'Approved'  => ['contents'],
            'Rejected'  => ['applications', 'contents'],
            'Published' => ['contents'],
            'Archived'  => ['contents'],
            'Applied'   => ['applications'],
            'Screening' => ['applications'],
            'Interview' => ['applications'],
            'Offered'   => ['applications'],
            'Hired'     => ['applications'],
            'Closed'    => ['job-posts'],
        ];

        foreach ($statusMap as $statusName => $featureCodes) {
            $status = $db->table('status')
                ->where('name', $statusName)
                ->get()
                ->getRowArray();

            if (! $status) {
                $db->table('status')->insert([
                    'name' => $statusName,
                    'date_created' => $now,
                ]);
                $statusId = (int) $db->insertID();
            } else {
                $statusId = (int) $status['id'];
            }

            foreach ($featureCodes as $code) {
                $featureId = $featureMap[strtolower(trim($code))] ?? null;
                if (! $featureId) {
                    continue;
                }

                $exists = $db->table('status_features')
                    ->where('status_id', $statusId)
                    ->where('feature_id', $featureId)
                    ->get()
                    ->getRowArray();

                if (! $exists) {
                    $db->table('status_features')->insert([
                        'status_id' => $statusId,
                        'feature_id' => $featureId,
                        'date_created' => $now,
                        'date_updated' => $now,
                    ]);
                }
            }
        }
    }
}