<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        helper('rbac');
        helper('dropdown');

        $db = db_connect();
        $adminId = (int) (session()->get('admin_id') ?? 0);

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

            $stats['my_processing'] = $db->table('job_applications')
                ->where('date_deleted', null)
                ->where('assigned_to', $adminId)
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
                ->where('date_deleted', null)
                ->where('status_id', 9)
                ->countAllResults();
        }

        if (rbac_can_feature('contents', 'can_approve') || rbac_can_feature('contents', 'can_view')) {
            $stats_for_approval['contents'] = $db->table('contents')
                ->where('date_deleted', null)
                ->where('status_id', 1)
                ->countAllResults();
        }

        $overdueStats = [];
        $overdueApplications = [];
        $myProcessingOverdue = 0;

        if (rbac_can_feature('applications', 'can_view')) {
            $overdueStats['applications_overdue'] = $db->table('job_applications')
                ->where('date_deleted', null)
                ->where('due_at IS NOT NULL', null, false)
                ->where('due_at <', date('Y-m-d H:i:s'))
                ->countAllResults();

            $myProcessingOverdue = $db->table('job_applications')
                ->where('date_deleted', null)
                ->where('assigned_to', $adminId)
                ->where('due_at IS NOT NULL', null, false)
                ->where('due_at <', date('Y-m-d H:i:s'))
                ->countAllResults();

            $overdueApplications = $db->table('job_applications ja')
                ->select("
                    ja.id,
                    ja.due_at,
                    ja.applied_at,
                    a.firstname,
                    a.lastname,
                    j.name as job_name,
                    s.name as status_name,
                    u.name as processor_name
                ")
                ->join('applicants a', 'a.id = ja.applicant_id', 'left')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                ->join('job j', 'j.id = jl.job_id', 'left')
                ->join('status s', 's.id = ja.status_id', 'left')
                ->join('users u', 'u.id = ja.assigned_to', 'left')
                ->where('ja.date_deleted', null)
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', date('Y-m-d H:i:s'))
                ->orderBy('ja.due_at', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();
        }

        return view('admin/dashboard/index', [
            'stats' => $stats,
            'stats_for_approval' => $stats_for_approval,
            'overdueStats' => $overdueStats,
            'overdueApplications' => $overdueApplications,
            'myProcessingOverdue' => $myProcessingOverdue,
        ]);
    }
}