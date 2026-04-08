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

            'Draft'     => ['jobs', 'job-posts', 'contents', 'mfi'],
            'Submitted' => ['contents', 'job-posts', 'mfi'],
            'Approved'  => ['contents', 'job-posts', 'mfi'],
            'Rejected'  => ['contents', 'job-posts', 'mfi'],
            'Published' => ['contents'],
            'Archived'  => ['contents'],
            'Closed'    => ['job-posts'],

            // Applications workflow
            'Pre-Screening'     => ['applications'],
            'Shortlist'         => ['applications'],
            '1st Interview'     => ['applications'],
            '2nd Interview'     => ['applications'],
            '3rd Interview'     => ['applications'],
            'Exam'              => ['applications'],
            'BI'                => ['applications'],
            'Job Offer Approval'=> ['applications'],
            'Re-Route'          => ['applications'],
            'Offer'             => ['applications'],
            'Hired'             => ['applications'],
            'On-Boarding'       => ['applications'],
            'Hold'              => ['applications'],
            'Withdraw'          => ['applications'],
            'Failed'            => ['applications'],
            'No Show'           => ['applications'],
            'Decline'           => ['applications'],
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