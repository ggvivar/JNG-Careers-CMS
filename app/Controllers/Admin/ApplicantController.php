<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class ApplicantController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('applicants a')
            ->select('a.*')
            ->where('a.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('a.firstname', $q)
                ->orLike('a.lastname', $q)
                ->orLike('a.email', $q)
                ->orLike('a.phone', $q)
                ->groupEnd();
        }

        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $rows = $builder->orderBy('a.id', 'DESC')->limit($perPage, $offset)->get()->getResultArray();

        return view('admin/applicants/index', [
            'applicants' => $rows,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function view($id)
    {
        $db = db_connect();
        $id = (int) $id;

        $applicant = $db->table('applicants')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $applicant) {
            return redirect()->to('/admin/applicants')->with('error', 'Applicant not found.');
        }

        $educations = $db->table('applicant_education')
            ->where('applicant_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $jobHistory = $db->table('applicant_job_history')
            ->where('applicant_id', $id)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        $applications = $db->table('job_applications ja')
            ->select('ja.*, j.name as job_name, s.name as status_name')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.applicant_id', $id)
            ->where('ja.date_deleted', null)
            ->orderBy('ja.id', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/applicants/view', [
            'applicant' => $applicant,
            'educations' => $educations,
            'jobHistory' => $jobHistory,
            'applications' => $applications,
        ]);
    }
}