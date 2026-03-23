<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        helper('rbac');

        $db = db_connect();

        $stats = [];

        if (rbac_can_feature('users', 'can_view')) {
            $stats['users'] = $db->table('users')
                ->where('date_deleted', null)
                ->countAllResults();
        }

        if (rbac_can_feature('job-posts', 'can_view')) {
            $stats['job_posts'] = $db->table('job_list')
                ->where('date_deleted', null)
                ->countAllResults();
        }

        if (rbac_can_feature('applicants', 'can_view')) {
            $stats['applicants'] = $db->table('applicants')
                ->where('date_deleted', null)
                ->countAllResults();
        }

        if (rbac_can_feature('applications', 'can_view')) {
            $stats['applications'] = $db->table('job_applications')
                ->where('date_deleted', null)
                ->countAllResults();
        }

        if (rbac_can_feature('contents', 'can_view')) {
            $stats['contents'] = $db->table('contents')
                ->where('date_deleted', null)
                ->countAllResults();
        }

        $stats_for_approval = [];

        if (rbac_can_feature('applications', 'can_approve') || rbac_can_feature('applications', 'can_view')) {
            $stats_for_approval['applications'] = $db->table('job_applications')
                ->where('status_id', 1)
                ->where('date_deleted', null)
                ->countAllResults();
        }

        if (rbac_can_feature('contents', 'can_approve') || rbac_can_feature('contents', 'can_view')) {
            $stats_for_approval['contents'] = $db->table('contents')
                ->where('status_id', 1)
                ->where('date_deleted', null)
                ->countAllResults();
        }

        return view('admin/dashboard/index', [
            'stats' => $stats,
            'stats_for_approval' => $stats_for_approval,
        ]);
    }
}