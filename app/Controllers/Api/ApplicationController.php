<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\ApplicantMailer;
use App\Models\ApplicantNotificationModel;
use App\Models\JobApplicationModel;

class ApplicationController extends BaseController
{
    public function mine()
    {
        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];

        $rows = db_connect()->table('job_applications ja')
            ->select('
                ja.*,
                j.name as job_name,
                s.name as status_name,
                c.name as company_name
            ')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('companies c', 'c.id = jl.company_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.applicant_id', $applicantId)
            ->where('ja.date_deleted', null)
            ->orderBy('ja.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'count' => count($rows),
            'data' => $rows,
        ]);
    }

    public function detail($id)
    {
        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];

        $row = db_connect()->table('job_applications ja')
            ->select('
                ja.*,
                j.name as job_name,
                s.name as status_name,
                c.name as company_name
            ')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('companies c', 'c.id = jl.company_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->where('ja.id', (int) $id)
            ->where('ja.applicant_id', $applicantId)
            ->where('ja.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $row) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Application not found.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $row,
        ]);
    }

    public function create()
    {
        helper('dropdown');

        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];

        $jobListId = (int) ($this->request->getPost('job_list_id') ?? 0);
        $source = trim((string) $this->request->getPost('source')) ?: 'API';

        if ($jobListId <= 0) {
            return $this->response->setStatusCode(422)->setJSON([
                'status' => false,
                'message' => 'job_list_id is required.',
            ]);
        }

        $db = db_connect();

        $jobPost = $db->table('job_list')
            ->where('id', $jobListId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $jobPost) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Job post not found.',
            ]);
        }

        $exists = $db->table('job_applications')
            ->where('applicant_id', $applicantId)
            ->where('job_list_id', $jobListId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if ($exists) {
            return $this->response->setStatusCode(409)->setJSON([
                'status' => false,
                'message' => 'You already applied for this job.',
            ]);
        }

        $appliedStatusId = dd_status_id('Applied', 'applications');

        $model = new JobApplicationModel();
        $model->insert([
            'applicant_id' => $applicantId,
            'job_list_id' => $jobListId,
            'status_id' => $appliedStatusId,
            'source' => $source,
            'applied_at' => date('Y-m-d H:i:s'),
            'date_created' => date('Y-m-d H:i:s'),
        ]);

        $job = $db->table('job_list jl')
            ->select('j.name as job_name')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->where('jl.id', $jobListId)
            ->get()
            ->getRowArray();

        (new ApplicantNotificationModel())->insert([
            'applicant_id' => $applicantId,
            'title' => 'Application Submitted',
            'message' => 'Your application for ' . ($job['job_name'] ?? 'the selected job') . ' has been received.',
            'type' => 'success',
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        (new ApplicantMailer())->applicationSubmitted(
            $applicant['email'],
            trim(($applicant['firstname'] ?? '') . ' ' . ($applicant['lastname'] ?? '')),
            $job['job_name'] ?? 'the selected job'
        );

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Application created successfully.',
            'data' => [
                'id' => $model->getInsertID(),
            ],
        ]);
    }

    public function edit($id)
    {
        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];
        $id = (int) $id;

        $db = db_connect();

        $application = $db->table('job_applications')
            ->where('id', $id)
            ->where('applicant_id', $applicantId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Application not found.',
            ]);
        }

        $payload = [
            'source' => trim((string) $this->request->getPost('source')) ?: $application['source'],
            'date_updated' => date('Y-m-d H:i:s'),
        ];

        $jobListId = (int) ($this->request->getPost('job_list_id') ?? 0);
        if ($jobListId > 0 && $jobListId !== (int) $application['job_list_id']) {
            $jobPost = $db->table('job_list')
                ->where('id', $jobListId)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if (! $jobPost) {
                return $this->response->setStatusCode(404)->setJSON([
                    'status' => false,
                    'message' => 'New job post not found.',
                ]);
            }

            $duplicate = $db->table('job_applications')
                ->where('applicant_id', $applicantId)
                ->where('job_list_id', $jobListId)
                ->where('id !=', $id)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($duplicate) {
                return $this->response->setStatusCode(409)->setJSON([
                    'status' => false,
                    'message' => 'You already applied for that job.',
                ]);
            }

            $payload['job_list_id'] = $jobListId;
        }

        $db->table('job_applications')->where('id', $id)->update($payload);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Application updated successfully.',
        ]);
    }

    public function withdraw($id)
    {
        $applicant = service('request')->applicant;
        $applicantId = (int) $applicant['id'];

        $db = db_connect();
        $row = $db->table('job_applications')
            ->where('id', (int) $id)
            ->where('applicant_id', $applicantId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $row) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Application not found.',
            ]);
        }

        $db->table('job_applications')->where('id', (int) $id)->update([
            'date_deleted' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Application withdrawn successfully.',
        ]);
    }
}