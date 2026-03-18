<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class DashboardController extends BaseController
{
    public function index()
    {
        $db = db_connect();

        $stats = [
            'users' => $db->table('users')->where('date_deleted', null)->countAllResults(),
            // 'jobs' => $db->table('job')->where('date_deleted', null)->countAllResults(), removed 
            'job_posts' => $db->table('job_list')->where('date_deleted', null)->countAllResults(),
            'applicants' => $db->table('applicants')->where('date_deleted', null)->countAllResults(),
            'applications' => $db->table('job_applications')->where('date_deleted', null)->countAllResults(),
            'contents' => $db->table('contents')->where('date_deleted', null)->countAllResults(),
        ];
        $stats_for_approval = 
            [
            'applications' => $db->table('job_applications')->where('status_id',1)->where('date_deleted', null)->countAllResults(),
            'contents' => $db->table('contents')->where('status_id',1)->where('date_deleted', null)->countAllResults(),
            ];
        // var_dump($stats_for_approval);
        // die();
        return view('admin/dashboard/index', [
            'stats' => $stats,
            'stats_for_approval' =>$stats_for_approval
        ]);
    }
}