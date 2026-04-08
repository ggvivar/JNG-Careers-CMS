<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ApplicantModel;
use App\Models\ApplicantEducationModel;
use App\Models\ApplicantJobHistoryModel;
use App\Models\JobApplicationModel;
use App\Models\ApplicantDocumentAttachmentModel;
use App\Models\ApplicantEmploymentDetailModel;

class ApplicantController extends BaseController
{
    protected ApplicantModel $applicantModel;
    protected ApplicantEducationModel $educationModel;
    protected ApplicantJobHistoryModel $jobHistoryModel;
    protected JobApplicationModel $jobApplicationModel;
    protected ApplicantDocumentAttachmentModel $documentAttachmentModel;
    protected ApplicantEmploymentDetailModel $employmentDetailModel;

    public function __construct()
    {
        $this->applicantModel = new ApplicantModel();
        $this->educationModel = new ApplicantEducationModel();
        $this->jobHistoryModel = new ApplicantJobHistoryModel();
        $this->jobApplicationModel = new JobApplicationModel();
        $this->documentAttachmentModel = new ApplicantDocumentAttachmentModel();
        $this->employmentDetailModel = new ApplicantEmploymentDetailModel();
    }

    public function create()
    {
        helper(['form', 'filesystem']);

        if (strtolower($this->request->getMethod()) === 'post') {
            $db = db_connect();
            $db->transBegin();

            try {
                $applicantData = $this->getApplicantPostData(false);

                $this->applicantModel->insert($applicantData);
                $applicantId = (int) $this->applicantModel->getInsertID();

                $this->saveEducationRows($applicantId);
                $this->saveJobHistoryRows($applicantId);
                $this->saveEmploymentDetails($applicantId);
                // $this->saveResumeUpload($applicantId);

                if ($db->transStatus() === false) {
                    throw new \RuntimeException('Failed to create applicant.');
                }

                $db->transCommit();

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success'      => true,
                        'message'      => 'Applicant created successfully.',
                        'applicant_id' => $applicantId,
                        'edit_url'     => site_url('admin/applicants/edit/' . $applicantId),
                        'view_url'     => site_url('admin/applicants/' . $applicantId),
                    ]);
                }

                return redirect()->to('/admin/applicants/' . $applicantId)
                    ->with('success', 'Applicant created.');
            } catch (\Throwable $e) {
                $db->transRollback();

                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

        return view('admin/applicants/form', [
            'mode'       => 'create',
            'applicant'  => [],
            'educations' => [],
            'jobHistory' => [],
            'documents'  => [],
            'employment' => [],
        ]);
    }

    public function edit($id)
    {
        helper(['form', 'filesystem']);

        $id = (int) $id;
        $applicant = $this->applicantModel->where('date_deleted', null)->find($id);

        if (! $applicant) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Applicant not found.',
                ]);
            }

            return redirect()->to('/admin/applicants')->with('error', 'Applicant not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $db = db_connect();
            $db->transBegin();

            try {
                $this->applicantModel->update($id, $this->getApplicantPostData(true));

                $this->replaceEducationRows($id);
                $this->replaceJobHistoryRows($id);
                $this->saveEmploymentDetails($id);
                // $this->saveResumeUpload($id);

                if ($db->transStatus() === false) {
                    throw new \RuntimeException('Failed to update applicant.');
                }

                $db->transCommit();

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success'      => true,
                        'message'      => 'Applicant updated successfully.',
                        'applicant_id' => $id,
                        'view_url'     => site_url('admin/applicants/' . $id),
                    ]);
                }

                return redirect()->to('/admin/applicants/' . $id)
                    ->with('success', 'Applicant updated.');
            } catch (\Throwable $e) {
                $db->transRollback();

                if ($this->request->isAJAX()) {
                    return $this->response->setStatusCode(500)->setJSON([
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]);
                }

                return redirect()->back()
                    ->withInput()
                    ->with('error', $e->getMessage());
            }
        }

        $educations = $this->educationModel->getByApplicantId($id);
        $jobHistory = $this->jobHistoryModel->getByApplicantId($id);
        $documents  = $this->documentAttachmentModel->getByApplicantId($id);
        $employment = $this->employmentDetailModel->getByApplicantId($id);

        return view('admin/applicants/form', [
            'mode'       => 'edit',
            'applicant'  => $applicant,
            'educations' => $educations,
            'jobHistory' => $jobHistory,
            'documents'  => $documents,
            'employment' => $employment ?? [],
        ]);
    }

    protected function getApplicantPostData(bool $isEdit = false): array
    {
        $data = [
            'firstname'    => trim((string) $this->request->getPost('firstname')),
            'middlename'   => trim((string) $this->request->getPost('middlename')) ?: null,
            'lastname'     => trim((string) $this->request->getPost('lastname')),
            'suffix'       => trim((string) $this->request->getPost('suffix')) ?: null,
            'email'        => trim((string) $this->request->getPost('email')) ?: null,
            'phone'        => trim((string) $this->request->getPost('phone')) ?: null,
            'birthdate'    => $this->request->getPost('birthdate') ?: null,
            'gender'       => $this->request->getPost('gender') ?: null,
            'civil_status' => $this->request->getPost('civil_status') ?: null,
            'nationality'  => trim((string) $this->request->getPost('nationality')) ?: null,
            'address'      => trim((string) $this->request->getPost('address')) ?: null,
            'city'         => trim((string) $this->request->getPost('city')) ?: null,
            'province'     => trim((string) $this->request->getPost('province')) ?: null,
            'zip_code'     => trim((string) $this->request->getPost('zip_code')) ?: null,
            // 'cover_letter' => trim((string) $this->request->getPost('cover_letter')) ?: null,
        ];

        if ($isEdit) {
            $data['date_updated'] = date('Y-m-d H:i:s');
        } else {
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['date_applied'] = date('Y-m-d H:i:s');
        }

        return $data;
    }

    protected function replaceEducationRows(int $applicantId): void
    {
        $this->educationModel->where('applicant_id', $applicantId)->delete();
        $this->saveEducationRows($applicantId);
    }

    protected function saveEducationRows(int $applicantId): void
    {
        $schools = $this->request->getPost('education_school_name') ?? [];
        $degrees = $this->request->getPost('education_degree') ?? [];
        $fields  = $this->request->getPost('education_field_of_study') ?? [];
        $starts  = $this->request->getPost('education_start_year') ?? [];
        $ends    = $this->request->getPost('education_end_year') ?? [];
        $honors  = $this->request->getPost('education_honors') ?? [];

        foreach ($schools as $i => $schoolName) {
            $schoolName = trim((string) $schoolName);

            if ($schoolName === '') {
                continue;
            }

            $this->educationModel->insert([
                'applicant_id'   => $applicantId,
                'school_name'    => $schoolName,
                'degree'         => trim((string) ($degrees[$i] ?? '')) ?: null,
                'field_of_study' => trim((string) ($fields[$i] ?? '')) ?: null,
                'start_year'     => trim((string) ($starts[$i] ?? '')) ?: null,
                'end_year'       => trim((string) ($ends[$i] ?? '')) ?: null,
                'honors'         => trim((string) ($honors[$i] ?? '')) ?: null,
                'date_created'   => date('Y-m-d H:i:s'),
            ]);
        }
    }

    protected function replaceJobHistoryRows(int $applicantId): void
    {
        $this->jobHistoryModel->where('applicant_id', $applicantId)->delete();
        $this->saveJobHistoryRows($applicantId);
    }

    protected function saveJobHistoryRows(int $applicantId): void
    {
        $companies         = $this->request->getPost('job_company_name') ?? [];
        $companyAddresses  = $this->request->getPost('job_company_address') ?? [];
        $jobTitles         = $this->request->getPost('job_title') ?? [];
        $departments       = $this->request->getPost('job_department') ?? [];
        $startDates        = $this->request->getPost('job_start_date') ?? [];
        $endDates          = $this->request->getPost('job_end_date') ?? [];
        $currentlyWorking  = $this->request->getPost('job_currently_working') ?? [];
        $responsibilities  = $this->request->getPost('job_responsibilities') ?? [];
        $salaries          = $this->request->getPost('job_salary') ?? [];
        $reasonsForLeaving = $this->request->getPost('job_reason_for_leaving') ?? [];

        foreach ($companies as $i => $companyName) {
            $companyName = trim((string) $companyName);

            if ($companyName === '') {
                continue;
            }

            $isCurrentlyWorking = isset($currentlyWorking[$i]) && (string) $currentlyWorking[$i] === '1';

            $this->jobHistoryModel->insert([
                'applicant_id'       => $applicantId,
                'company_name'       => $companyName,
                'company_address'    => trim((string) ($companyAddresses[$i] ?? '')) ?: null,
                'job_title'          => trim((string) ($jobTitles[$i] ?? '')) ?: null,
                'department'         => trim((string) ($departments[$i] ?? '')) ?: null,
                'start_date'         => !empty($startDates[$i]) ? $startDates[$i] : null,
                'end_date'           => $isCurrentlyWorking ? null : (!empty($endDates[$i]) ? $endDates[$i] : null),
                'currently_working'  => $isCurrentlyWorking ? 1 : 0,
                'responsibilities'   => trim((string) ($responsibilities[$i] ?? '')) ?: null,
                'salary'             => trim((string) ($salaries[$i] ?? '')) ?: null,
                'reason_for_leaving' => $isCurrentlyWorking ? null : (trim((string) ($reasonsForLeaving[$i] ?? '')) ?: null),
                'date_created'       => date('Y-m-d H:i:s'),
            ]);
        }
    }

    protected function saveEmploymentDetails(int $applicantId): void
    {
        $employeeNo       = trim((string) $this->request->getPost('employee_no'));
        $position         = trim((string) $this->request->getPost('position'));
        $department       = trim((string) $this->request->getPost('department'));
        $employmentType   = trim((string) $this->request->getPost('employment_type'));
        $dateHired        = $this->request->getPost('date_hired');
        $dateRegularized  = $this->request->getPost('date_regularized');
        $dateSeparated    = $this->request->getPost('date_separated');
        $salary           = trim((string) $this->request->getPost('salary'));
        $status           = trim((string) $this->request->getPost('employment_status'));
        $remarks          = trim((string) $this->request->getPost('employment_remarks'));

        $hasEmploymentData =
            $employeeNo !== '' ||
            $position !== '' ||
            $department !== '' ||
            $employmentType !== '' ||
            !empty($dateHired) ||
            !empty($dateRegularized) ||
            !empty($dateSeparated) ||
            $salary !== '' ||
            $status !== '' ||
            $remarks !== '';

        $existing = $this->employmentDetailModel->getByApplicantId($applicantId);

        if (! $hasEmploymentData) {
            if ($existing) {
                $this->employmentDetailModel->delete($existing['id']);
            }
            return;
        }

        $data = [
            'applicant_id'      => $applicantId,
            'employee_no'       => $employeeNo ?: null,
            'position'          => $position ?: null,
            'department'        => $department ?: null,
            'employment_type'   => $employmentType ?: null,
            'date_hired'        => $dateHired ?: null,
            'date_regularized'  => $dateRegularized ?: null,
            'date_separated'    => $dateSeparated ?: null,
            'salary'            => $salary ?: null,
            'status'            => $status ?: null,
            'remarks'           => $remarks ?: null,
            'date_updated'      => date('Y-m-d H:i:s'),
        ];

        if ($existing) {
            $this->employmentDetailModel->update($existing['id'], $data);
        } else {
            $data['date_created'] = date('Y-m-d H:i:s');
            $this->employmentDetailModel->insert($data);
        }
    }

    protected function saveResumeUpload(int $applicantId): void
    {
        $resume = $this->request->getFile('resume');

        if (! $resume || ! $resume->isValid() || $resume->hasMoved()) {
            return;
        }

        $uploadPath = FCPATH . 'uploads/applicants/' . $applicantId;

        if (! is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        $newName = $resume->getRandomName();
        $resume->move($uploadPath, $newName);

        $oldResume = $this->documentAttachmentModel->getResumeByApplicantId($applicantId);
        if ($oldResume) {
            $this->documentAttachmentModel->update($oldResume['id'], [
                'date_deleted' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);
        }

        $this->documentAttachmentModel->insert([
            'applicant_id' => $applicantId,
            'document_type' => 'Resume',
            'file_name' => $resume->getClientName(),
            'file_path' => 'uploads/applicants/' . $applicantId . '/' . $newName,
            'file_ext' => $resume->getExtension(),
            'file_size' => $resume->getSize(),
            'date_created' => date('Y-m-d H:i:s'),
        ]);
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

    public function profile()
    {
        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];
        $db = db_connect();

        $applications = $db->table('job_applications ja')
            ->select('ja.id, ja.applied_at, s.name as status_name, j.name as job_name')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.applicant_id', $applicantId)
            ->where('ja.date_deleted', null)
            ->orderBy('ja.id', 'DESC')
            ->get()
            ->getResultArray();

        $notifications = $db->table('applicant_notifications')
            ->where('applicant_id', $applicantId)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();

        $education = $this->educationModel->getByApplicantId($applicantId);
        $jobHistory = $this->jobHistoryModel->getByApplicantId($applicantId);

        return $this->response->setJSON([
            'status' => true,
            'data' => [
                'profile' => [
                    'id' => $applicant['id'],
                    'firstname' => $applicant['firstname'],
                    'middlename' => $applicant['middlename'] ?? null,
                    'lastname' => $applicant['lastname'],
                    'email' => $applicant['email'],
                    'phone' => $applicant['phone'] ?? null,
                    'birthdate' => $applicant['birthdate'] ?? null,
                    'gender' => $applicant['gender'] ?? null,
                    'civil_status' => $applicant['civil_status'] ?? null,
                    'nationality' => $applicant['nationality'] ?? null,
                    'address' => $applicant['address'] ?? null,
                    'city' => $applicant['city'] ?? null,
                    'province' => $applicant['province'] ?? null,
                    'zip_code' => $applicant['zip_code'] ?? null,
                    // 'cover_letter' => $applicant['cover_letter'] ?? null,
                ],
                'applications' => $applications,
                'notifications' => $notifications,
                'education' => $education,
                'job_history' => $jobHistory,
            ],
        ]);
    }

    public function view($id)
    {
        $id = (int) $id;

        $applicant = $this->applicantModel->getApplicantById($id);

        if (! $applicant) {
            return redirect()->to('/admin/applicants')->with('error', 'Applicant not found.');
        }

        $educations   = $this->educationModel->getByApplicantId($id);
        $jobHistory   = $this->jobHistoryModel->getByApplicantId($id);
        $applications = $this->jobApplicationModel->getByApplicantId($id);
        $documents    = $this->documentAttachmentModel->getByApplicantId($id);
        $employment   = $this->employmentDetailModel->getByApplicantId($id);

        return view('admin/applicants/view', [
            'applicant'    => $applicant,
            'educations'   => $educations,
            'jobHistory'   => $jobHistory,
            'applications' => $applications,
            'documents'    => $documents,
            'employment'   => $employment ?? [],
        ]);
    }

    public function List()
    {
        $q = trim((string) $this->request->getGet('q'));

        $builder = db_connect()->table('applicants')
            ->select('id, firstname, middlename, lastname, email, phone, city, province')
            ->where('date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('firstname', $q)
                ->orLike('middlename', $q)
                ->orLike('lastname', $q)
                ->orLike('email', $q)
                ->orLike('phone', $q)
                ->orLike('city', $q)
                ->orLike('province', $q)
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('id', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'rows' => $rows,
        ]);
    }

    public function Add()
    {
        if (! $this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid request.',
            ]);
        }

        $firstname  = trim((string) $this->request->getPost('firstname'));
        $middlename = trim((string) $this->request->getPost('middlename'));
        $lastname   = trim((string) $this->request->getPost('lastname'));
        $email      = trim((string) $this->request->getPost('email'));
        $phone      = trim((string) $this->request->getPost('phone'));
        $city       = trim((string) $this->request->getPost('city'));
        $province   = trim((string) $this->request->getPost('province'));

        if ($firstname === '' || $lastname === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'First name and last name are required.',
            ]);
        }

        if ($email !== '') {
            $exists = $this->applicantModel
                ->where('email', $email)
                ->where('date_deleted', null)
                ->first();

            if ($exists) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Applicant email already exists.',
                ]);
            }
        }

        $this->applicantModel->insert([
            'firstname'    => $firstname,
            'middlename'   => $middlename !== '' ? $middlename : null,
            'lastname'     => $lastname,
            'email'        => $email !== '' ? $email : null,
            'phone'        => $phone !== '' ? $phone : null,
            'city'         => $city !== '' ? $city : null,
            'province'     => $province !== '' ? $province : null,
            'date_created' => date('Y-m-d H:i:s'),
        ]);

        $id = (int) $this->applicantModel->getInsertID();

        $fullName = trim($firstname . ' ' . ($middlename !== '' ? $middlename . ' ' : '') . $lastname);
        $display = $fullName
            . ($email !== '' ? ' | ' . $email : '')
            . ($phone !== '' ? ' | ' . $phone : '');

        return $this->response->setJSON([
            'success' => true,
            'id' => $id,
            'name' => $display,
            'message' => 'Applicant created successfully.',
        ]);
    }

    public function updateInlineProfile($id)
    {
        $id = (int) $id;

        $this->applicantModel->update($id, [
            'phone'        => trim((string) $this->request->getPost('phone')) ?: null,
            'email'        => trim((string) $this->request->getPost('email')) ?: null,
            'address'      => trim((string) $this->request->getPost('address')) ?: null,
            'city'         => trim((string) $this->request->getPost('city')) ?: null,
            'province'     => trim((string) $this->request->getPost('province')) ?: null,
            'date_updated' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    public function saveEducationInline($applicantId)
    {
        $id = (int) $this->request->getPost('id');

        $data = [
            'applicant_id'   => (int) $applicantId,
            'school_name'    => trim((string) $this->request->getPost('school_name')) ?: null,
            'degree'         => trim((string) $this->request->getPost('degree')) ?: null,
            'field_of_study' => trim((string) $this->request->getPost('field_of_study')) ?: null,
            'start_year'     => trim((string) $this->request->getPost('start_year')) ?: null,
            'end_year'       => trim((string) $this->request->getPost('end_year')) ?: null,
            'honors'         => trim((string) $this->request->getPost('honors')) ?: null,
            'date_created'   => date('Y-m-d H:i:s'),
        ];

        if (empty($data['school_name'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'School name is required.',
            ]);
        }

        if ($id > 0) {
            unset($data['date_created']);
            $this->educationModel->update($id, $data);
        } else {
            $this->educationModel->insert($data);
            $id = (int) $this->educationModel->getInsertID();
        }

        $row = $this->educationModel->find($id);

        return $this->response->setJSON([
            'success' => true,
            'row' => $row,
        ]);
    }

    public function deleteEducationInline($applicantId)
    {
        $id = (int) $this->request->getPost('id');

        if ($id > 0) {
            $this->educationModel->delete($id);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function saveJobHistoryInline($applicantId)
    {
        $id = (int) $this->request->getPost('id');

        $data = [
            'applicant_id'       => (int) $applicantId,
            'company_name'       => trim((string) $this->request->getPost('company_name')) ?: null,
            'company_address'    => trim((string) $this->request->getPost('company_address')) ?: null,
            'job_title'          => trim((string) $this->request->getPost('job_title')) ?: null,
            'department'         => trim((string) $this->request->getPost('department')) ?: null,
            'start_date'         => $this->request->getPost('start_date') ?: null,
            'end_date'           => $this->request->getPost('end_date') ?: null,
            'currently_working'  => $this->request->getPost('currently_working') ? 1 : 0,
            'responsibilities'   => trim((string) $this->request->getPost('responsibilities')) ?: null,
            'salary'             => trim((string) $this->request->getPost('salary')) ?: null,
            'reason_for_leaving' => trim((string) $this->request->getPost('reason_for_leaving')) ?: null,
            'date_created'       => date('Y-m-d H:i:s'),
        ];

        if (empty($data['company_name'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Company name is required.',
            ]);
        }

        if (!empty($data['currently_working'])) {
            $data['end_date'] = null;
            $data['reason_for_leaving'] = null;
        }

        if ($id > 0) {
            unset($data['date_created']);
            $this->jobHistoryModel->update($id, $data);
        } else {
            $this->jobHistoryModel->insert($data);
            $id = (int) $this->jobHistoryModel->getInsertID();
        }

        $row = $this->jobHistoryModel->find($id);

        return $this->response->setJSON([
            'success' => true,
            'row' => $row,
        ]);
    }

    public function deleteJobHistoryInline($applicantId)
    {
        $id = (int) $this->request->getPost('id');

        if ($id > 0) {
            $this->jobHistoryModel->delete($id);
        }

        return $this->response->setJSON(['success' => true]);
    }
    public function uploadDocumentInline($applicantId)
{
    $applicantId = (int) $applicantId;

    $applicant = $this->applicantModel->getApplicantById($applicantId);
    if (! $applicant) {
        return $this->response->setStatusCode(404)->setJSON([
            'success' => false,
            'message' => 'Applicant not found.',
        ]);
    }

    $documentType = trim((string) $this->request->getPost('document_type'));
    $remarks      = trim((string) $this->request->getPost('remarks'));
    $file         = $this->request->getFile('document_file');

    if ($documentType === '') {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Document type is required.',
        ]);
    }

    if (! $file || ! $file->isValid() || $file->hasMoved()) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Please select a valid file.',
        ]);
    }

    $uploadPath = FCPATH . 'uploads/applicants/' . $applicantId . '/documents';
    if (! is_dir($uploadPath)) {
        mkdir($uploadPath, 0777, true);
    }

    $newName = $file->getRandomName();
    $file->move($uploadPath, $newName);

    $insertData = [
        'applicant_id'  => $applicantId,
        'document_type' => $documentType,
        'file_name'     => $file->getClientName(),
        'file_path'     => 'uploads/applicants/' . $applicantId . '/documents/' . $newName,
        'file_ext'      => $file->getExtension(),
        'file_size'     => $file->getSize(),
        'remarks'       => $remarks !== '' ? $remarks : null,
        'date_created'  => date('Y-m-d H:i:s'),
    ];

    $this->documentAttachmentModel->insert($insertData);
    $documentId = (int) $this->documentAttachmentModel->getInsertID();
    $row = $this->documentAttachmentModel->find($documentId);

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Document uploaded successfully.',
        'row'     => $row,
        'url'     => base_url($row['file_path']),
    ]);
}

public function deleteDocumentInline($applicantId)
{
    $applicantId = (int) $applicantId;
    $id = (int) $this->request->getPost('id');

    if ($id <= 0) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid document id.',
        ]);
    }

    $row = $this->documentAttachmentModel
        ->where('id', $id)
        ->where('applicant_id', $applicantId)
        ->where('date_deleted', null)
        ->first();

    if (! $row) {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Document not found.',
        ]);
    }

    $this->documentAttachmentModel->update($id, [
        'date_deleted' => date('Y-m-d H:i:s'),
        'date_updated' => date('Y-m-d H:i:s'),
    ]);

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Document deleted successfully.',
    ]);
}
}