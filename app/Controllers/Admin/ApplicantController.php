<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ApplicantModel;
use App\Models\ApplicantEducationModel;
use App\Models\ApplicantJobHistoryModel;
use App\Models\JobApplicationModel;

class ApplicantController extends BaseController
{
    protected ApplicantModel $applicantModel;
    protected ApplicantEducationModel $educationModel;
    protected ApplicantJobHistoryModel $jobHistoryModel;
    protected JobApplicationModel $jobApplicationModel;

    public function __construct()
    {
        $this->applicantModel = new ApplicantModel();
        $this->educationModel = new ApplicantEducationModel();
        $this->jobHistoryModel = new ApplicantJobHistoryModel();
        $this->jobApplicationModel = new JobApplicationModel();
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 10;

        $result = $this->applicantModel->getPaginatedApplicants($q, $perPage, $page);

        return view('admin/applicants/index', [
            'applicants'      => $result['rows'],
            'searchQuery'     => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $result['total']),
        ]);
    }

    public function view($id)
    {
        $id = (int) $id;

        $applicant = $this->applicantModel->getApplicantById($id);

        if (! $applicant) {
            return redirect()->to('/admin/applicants')->with('error', 'Applicant not found.');
        }

        $educations = $this->educationModel->getByApplicantId($id);
        $jobHistory = $this->jobHistoryModel->getByApplicantId($id);
        $applications = $this->jobApplicationModel->getByApplicantId($id);

        return view('admin/applicants/view', [
            'applicant'    => $applicant,
            'educations'   => $educations,
            'jobHistory'   => $jobHistory,
            'applications' => $applications,
        ]);
    }
}