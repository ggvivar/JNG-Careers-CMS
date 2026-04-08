<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WorkflowTransitionSeeder extends Seeder
{
    public function run()
    {
        helper('dropdown');

        $db  = db_connect();
        $now = date('Y-m-d H:i:s');

        // Get Applications feature
        $featureId = dd_feature_id('applications');
        if (! $featureId) {
            return;
        }

        // Map status name -> ID
        $statuses = $db->table('status')
            ->where('date_deleted', null)
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($statuses as $s) {
            $map[strtolower(trim($s['name']))] = (int) $s['id'];
        }

        // YOUR EXACT WORKFLOW
        $workflow = [
            'Pre-Screening' => ['days'=>3,'next'=>['Shortlist','Withdraw','Failed']],
            'Shortlist' => ['days'=>3,'next'=>['1st Interview','Hold','Withdraw','Failed']],
            '1st Interview' => ['days'=>3,'next'=>['2nd Interview','Exam','BI','Hold','Withdraw','Failed','No Show']],
            '2nd Interview' => ['days'=>5,'next'=>['3rd Interview','Exam','BI','Hold','Withdraw','Failed','No Show']],
            '3rd Interview' => ['days'=>5,'next'=>['Exam','BI','Hold','Withdraw','Failed','No Show']],
            'Exam' => ['days'=>5,'next'=>['Offer','BI','Hold','Withdraw','Failed']],
            'BI' => ['days'=>5,'next'=>['Job Offer Approval','Offer','Exam','Hold','Withdraw','Failed']],
            'Job Offer Approval' => ['days'=>5,'next'=>['Offer','Hold','Re-Route']],
            'Re-Route' => ['days'=>5,'next'=>['Job Offer Approval','Hold']],
            'Offer' => ['days'=>4,'next'=>['Hired','Exam','Hold','Withdraw','Decline','Failed','No Show']],
            'Hired' => ['days'=>null,'next'=>['On-Boarding','No Show']],
            'On-Boarding' => ['days'=>null,'next'=>[]],
        ];

        foreach ($workflow as $from => $config) {
            $fromId = $map[strtolower($from)] ?? null;
            if (! $fromId) continue;

            $sort = 1;

            foreach ($config['next'] as $to) {
                $toId = $map[strtolower($to)] ?? null;
                if (! $toId) continue;

                // prevent duplicates
                $exists = $db->table('workflow_transitions')
                    ->where('feature_id', $featureId)
                    ->where('status_id_from', $fromId)
                    ->where('status_id_to', $toId)
                    ->where('date_deleted', null)
                    ->get()
                    ->getRowArray();

                if ($exists) continue;

                $db->table('workflow_transitions')->insert([
                    'feature_id' => $featureId,
                    'status_id_from' => $fromId,
                    'status_id_to' => $toId,
                    'grace_period' => $config['days'],
                    'sort_order' => $sort++,
                    'require_remarks' => 0,
                    'status_id' => 1,
                    'date_created' => $now,
                    'date_updated' => $now,
                ]);
            }
        }
    }
}