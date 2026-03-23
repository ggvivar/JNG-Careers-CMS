<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ApplicationController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));
        
        $builder = $db->table('job_applications ja')
            // ->select('ja.*, a.firstname, a.lastname, a.email, j.name as job_name, s.name as status_name')
            ->select('ja.id, ja.applicant_id, ja.job_list_id, ja.status_id, ja.source, ja.applied_at, a.firstname, a.lastname, a.email, j.name as job_name, s.name as status_name')
            ->join('applicants a', 'a.id = ja.applicant_id', 'left')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('a.firstname', $q)
                ->orLike('a.lastname', $q)
                ->orLike('a.email', $q)
                ->orLike('j.name', $q)
                ->orLike('s.name', $q)
                ->orLike('ja.source', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $applications = $builder
            ->orderBy('ja.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        $pager = service('pager');

        return view('admin/applications/index', [
            'applications' => $applications,
            'searchQuery' => $q,
            'paginationLinks' => $pager->makeLinks($page, $perPage, $total),
        ]);
    }

    public function view($id)
    {
        helper('dropdown');
        $db = db_connect();
        $id = (int) $id;

        $application = $db->table('job_applications ja')
            ->select('ja.*, a.firstname, a.middlename, a.lastname, a.email, a.phone, j.name as job_name, s.name as status_name')
            ->join('applicants a', 'a.id = ja.applicant_id', 'left')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.id', $id)
            ->where('ja.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found.');
        }

        $statusOptions = dd_statuses_by_feature('Applications');

        return view('admin/applications/view', [
            'application' => $application,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function updateStatus($id)
    {
        $db = db_connect();
        $id = (int) $id;

        $application = $db->table('job_applications')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found.');
        }

        $db->table('job_applications')
            ->where('id', $id)
            ->update([
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/applications/' . $id)->with('success', 'Application status updated.');
    }
}