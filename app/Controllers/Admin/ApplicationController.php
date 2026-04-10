<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\Workflow;

class ApplicationController extends BaseController
{
    public function index()
{
    helper('dropdown');

    $db = db_connect();

    $q = trim((string) $this->request->getGet('q'));

    $statusRaw = $this->request->getGet('status_id');
    $assignedRaw = $this->request->getGet('assignedUserId');
    $overdueRaw = $this->request->getGet('overdue');

    $statusId = ($statusRaw !== null && $statusRaw !== '') ? (int) $statusRaw : null;
    $assignedUserId = ($assignedRaw !== null && $assignedRaw !== '') ? (int) $assignedRaw : null;
    $isOverdue = (string) $overdueRaw === '1';

    $sort = trim((string) $this->request->getGet('sort'));
    $dir = strtolower(trim((string) $this->request->getGet('dir'))) === 'asc' ? 'ASC' : 'DESC';

    $sortable = [
        'id' => 'ja.id',
        'applicant' => 'a.firstname',
        'email' => 'a.email',
        'job' => 'j.name',
        'status' => 's.name',
        'processor' => 'u.name',
        'applied_at' => 'ja.applied_at',
        'due_at' => 'ja.due_at',
    ];

    $sortColumn = $sortable[$sort] ?? 'ja.id';

    $builder = $db->table('job_applications ja')
        ->select('
            ja.id,
            ja.applicant_id,
            ja.job_list_id,
            ja.status_id,
            ja.assigned_to,
            ja.source,
            ja.applied_at,
            ja.due_at,
            a.firstname,
            a.lastname,
            a.email,
            j.name as job_name,
            s.name as status_name,
            u.name as processor_name
        ')
        ->join('applicants a', 'a.id = ja.applicant_id', 'left')
        ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
        ->join('job j', 'j.id = jl.job_id', 'left')
        ->join('status s', 's.id = ja.status_id', 'left')
        ->join('users u', 'u.id = ja.assigned_to', 'left')
        ->where('ja.date_deleted IS NULL', null, false);

    if ($q !== '') {
        $builder->groupStart()
            ->like('a.firstname', $q)
            ->orLike('a.lastname', $q)
            ->orLike('a.email', $q)
            ->orLike('j.name', $q)
            ->orLike('s.name', $q)
            ->orLike('u.name', $q)
            ->orLike('ja.source', $q)
            ->groupEnd();
    }

    if ($statusId !== null) {
        $builder->where('ja.status_id', $statusId);
    }

    if ($assignedUserId !== null) {
        $builder->where('ja.assigned_to', $assignedUserId);
    }

    if ($isOverdue) {
        $builder->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', date('Y-m-d H:i:s'));
    }

    $perPage = 10;
    $page = max(1, (int) ($this->request->getGet('page') ?? 1));
    $offset = ($page - 1) * $perPage;

    $countBuilder = clone $builder;
    $total = $countBuilder->countAllResults();

    $applications = $builder
        ->orderBy($sortColumn, $dir)
        ->orderBy('ja.id', 'DESC')
        ->limit($perPage, $offset)
        ->get()
        ->getResultArray();

    $statusOptions = dd_statuses_by_feature('applications');
    $assignedUserOptions = dd_options('users', 'id', 'name', [], ['name' => 'ASC']);

    return view('admin/applications/index', [
        'applications' => $applications,
        'searchQuery' => $q,
        'statusOptions' => $statusOptions,
        'assignedUserOptions' => $assignedUserOptions,
        'currentStatusId' => $statusId,
        'currentAssignedUserId' => $assignedUserId,
        'currentOverdue' => $isOverdue,
        'currentSort' => $sort,
        'currentDir' => strtolower($dir),
        'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total, 'default_full'),
    ]);
}

    public function create()
    {
        helper('dropdown');
        $db = db_connect();
        $Workflow = new Workflow('applications');

        $applicantRows = $db->table('applicants')
            ->select('id, firstname, lastname, email')
            ->where('date_deleted', null)
            ->orderBy('firstname', 'ASC')
            ->get()
            ->getResultArray();

        $jobRows = $db->table('job_list jl')
            ->select('jl.id, j.name')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->where('jl.date_deleted', null)
            ->orderBy('j.name', 'ASC')
            ->get()
            ->getResultArray();

        $processorRows = $db->table('users')
            ->select('id, name')
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $applicantOptions = [];
        foreach ($applicantRows as $row) {
            $label = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
            if (!empty($row['email'])) {
                $label .= ' (' . $row['email'] . ')';
            }
            $applicantOptions[$row['id']] = $label;
        }

        $jobOptions = [];
        foreach ($jobRows as $row) {
            $jobOptions[$row['id']] = $row['name'];
        }

        $processorOptions = [];
        foreach ($processorRows as $row) {
            $processorOptions[$row['id']] = $row['name'];
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $applicantId = (int) ($this->request->getPost('applicant_id') ?: 0);
            $jobListId = (int) ($this->request->getPost('job_list_id') ?: 0);
            $assignedUserId = $this->request->getPost('assigned_user_id') !== '' ? (int) $this->request->getPost('assigned_user_id') : null;
            $source = trim((string) $this->request->getPost('source'));
            $appliedAt = trim((string) $this->request->getPost('applied_at'));

            $defaultStatusId = dd_status_id('Pre-Screening', 'applications');

            if ($applicantId <= 0 || $jobListId <= 0 || ! $defaultStatusId) {
                return redirect()->back()->withInput()->with('error', 'Applicant, job, and default Workflow status are required.');
            }

            $exists = $db->table('job_applications')
                ->where('applicant_id', $applicantId)
                ->where('job_list_id', $jobListId)
                ->where('status_id !=', 23)
                ->where('status_id !=', 24)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'This applicant already has an application for this job.');
            }

            $nextStatuses = $Workflow->getNextStatuses($defaultStatusId);
            $initialDueAt = null;

            if (! empty($nextStatuses)) {
                $firstTransition = reset($nextStatuses);
                $initialDueAt = $Workflow->getDueAt($firstTransition['grace_period'] ?? null);
            }

            $db->table('job_applications')->insert([
                'applicant_id' => $applicantId,
                'job_list_id' => $jobListId,
                'status_id' => $defaultStatusId,
                'assigned_to' => $assignedUserId,
                'source' => $source !== '' ? $source : 'Manual',
                'applied_at' => $appliedAt !== '' ? $appliedAt : date('Y-m-d H:i:s'),
                'due_at' => $initialDueAt,
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            $applicationId = (int) $db->insertID();

            $Workflow->log(
                $applicationId,
                null,
                $defaultStatusId,
                null,
                $assignedUserId,
                'Application created',
                (int) session()->get('admin_id'),
                $initialDueAt
            );

            return redirect()->to('/admin/applications/' . $applicationId)->with('success', 'Application created.');
        }

        return view('admin/applications/form', [
            'mode' => 'create',
            'application' => null,
            'applicantOptions' => $applicantOptions,
            'jobOptions' => $jobOptions,
            'sourceOptions' => dd_common_defaults('Source'),
            'processorOptions' => $processorOptions,
        ]);
    }

    public function view($id)
    {
        helper('dropdown');
        $db = db_connect();
        $Workflow = new Workflow('applications');
        $id = (int) $id;

        $application = $db->table('job_applications ja')
            ->select('
                ja.*,
                a.firstname,
                a.middlename,
                a.lastname,
                a.email,
                a.phone,
                j.name as job_name,
                s.name as status_name,
                u.name as processor_name
            ')
            ->join('applicants a', 'a.id = ja.applicant_id', 'left')
            ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = ja.status_id', 'left')
            ->join('users u', 'u.id = ja.assigned_to', 'left')
            ->where('ja.id', $id)
            ->where('ja.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found.');
        }

        $processorRows = $db->table('users')
            ->select('id, name')
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        $processorOptions = [];
        foreach ($processorRows as $row) {
            $processorOptions[$row['id']] = $row['name'];
        }

        $history = $Workflow->history($id);

        return view('admin/applications/view', [
            'application' => $application,
            'processorOptions' => $processorOptions,
            'sourceOptions' => dd_common_defaults('Source'),
            'history' => $history,
        ]);
    }

    public function nextStatuses($id)
    {
        $db = db_connect();
        $Workflow = new Workflow('applications');

        $application = $db->table('job_applications')
            ->where('id', (int) $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON(array_values(
            $Workflow->getNextStatuses((int) $application['status_id'])
        ));
    }

    public function updateStatus($id)
    {
        $db = db_connect();
        $Workflow = new Workflow('applications');
        $id = (int) $id;

        $application = $db->table('job_applications')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found.');
        }

        $newStatusId = (int) ($this->request->getPost('status_id') ?: 0);
        $remarks = trim((string) $this->request->getPost('remarks'));

        $result = $Workflow->validateTransition((int) $application['status_id'], $newStatusId, $remarks);

        if (! $result['ok']) {
            return redirect()->back()->withInput()->with('error', $result['message']);
        }

        $transition = $result['transition'];
        $dueAt = $Workflow->getDueAt(
            isset($transition['grace_period']) ? (int) $transition['grace_period'] : null
        );

        $db->table('job_applications')
            ->where('id', $id)
            ->update([
                'status_id' => $newStatusId,
                'due_at' => $dueAt,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        $Workflow->log(
            $id,
            isset($application['status_id']) ? (int) $application['status_id'] : null,
            $newStatusId,
            isset($application['assigned_to']) ? (int) $application['assigned_to'] : null,
            isset($application['assigned_to']) ? (int) $application['assigned_to'] : null,
            $remarks !== '' ? $remarks : 'Status updated',
            (int) session()->get('admin_id'),
            $dueAt
        );

        return redirect()->to('/admin/applications/' . $id)->with('success', 'Application status updated.');
    }

    public function assignProcessor($id)
    {
        $db = db_connect();
        $Workflow = new Workflow('applications');
        $id = (int) $id;

        $application = $db->table('job_applications')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $application) {
            return redirect()->to('/admin/applications')->with('error', 'Application not found.');
        }

        $assignedUserId = $this->request->getPost('assignedUserId') !== ''
            ? (int) $this->request->getPost('assignedUserId')
            : null;

        $db->table('job_applications')
            ->where('id', $id)
            ->update([
                'assigned_to' => $assignedUserId,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        $Workflow->log(
            $id,
            isset($application['status_id']) ? (int) $application['status_id'] : null,
            isset($application['status_id']) ? (int) $application['status_id'] : null,
            isset($application['assigned_to']) ? (int) $application['assigned_to'] : null,
            $assignedUserId,
            'Processor reassigned',
            (int) session()->get('admin_id'),
            $application['due_at'] ?? null
        );

        return redirect()->to('/admin/applications/' . $id)->with('success', 'Processor assigned.');
    }
    public function updateWorkflow($id)
{
    $db = db_connect();
    $workflow = new \App\Libraries\Workflow('applications');
    $id = (int) $id;

    $application = $db->table('job_applications')
        ->where('id', $id)
        ->where('date_deleted', null)
        ->get()
        ->getRowArray();

    if (! $application) {
        return redirect()->to('/admin/applications')->with('error', 'Application not found.');
    }

    $currentStatusId = isset($application['status_id']) ? (int) $application['status_id'] : null;
    $currentAssignedUserId = isset($application['assigned_to']) && $application['assigned_to'] !== null
        ? (int) $application['assigned_to']
        : null;

    $newStatusId = $this->request->getPost('status_id') !== ''
        ? (int) $this->request->getPost('status_id')
        : null;

    $newAssignedUserId = $this->request->getPost('assignedUserId') !== ''
        ? (int) $this->request->getPost('assignedUserId')
        : null;

    $remarks = trim((string) $this->request->getPost('remarks'));

    $remarksAdded  = $remarks!== null;
    $statusChanged = $newStatusId !== null && $newStatusId !== $currentStatusId;
    $processorChanged = $newAssignedUserId !== $currentAssignedUserId;
    
    if (! $statusChanged && ! $processorChanged && ! $remarksAdded) {
        return redirect()->to('/admin/applications/' . $id)->with('error', 'No changes detected.');
    }

    $dueAt = $application['due_at'] ?? null;
    $emailResult = null;

    if ($statusChanged) {
        $validation = $workflow->validateTransition($currentStatusId, $newStatusId, $remarks);

        if (! $validation['ok']) {
            return redirect()->back()->withInput()->with('error', $validation['message']);
        }

        $transition = $validation['transition'];
        $dueAt = $workflow->getDueAt(
            isset($transition['due_at']) ? (int) $transition['due_at'] : null
        );

        $applicantRow = $db->table('applicants')
            ->select('firstname, lastname, email')
            ->where('id', (int) $application['applicant_id'])
            ->get()
            ->getRowArray();

        $jobRow = $db->table('job_list jl')
            ->select('j.name as job_name')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->where('jl.id', (int) $application['job_list_id'])
            ->get()
            ->getRowArray();

        $currentStatusRow = $db->table('status')
            ->select('name')
            ->where('id', $currentStatusId)
            ->get()
            ->getRowArray();

        $nextStatusRow = $db->table('status')
            ->select('name')
            ->where('id', $newStatusId)
            ->get()
            ->getRowArray();

        $emailResult = $workflow->sendTransitionEmail(
            $applicantRow['email'] ?? null,
            $transition,
            [
                'firstname' => $applicantRow['firstname'] ?? '',
                'lastname' => $applicantRow['lastname'] ?? '',
                'email' => $applicantRow['email'] ?? '',
                'job_name' => $jobRow['job_name'] ?? '',
                'current_status' => $currentStatusRow['name'] ?? '',
                'next_status' => $nextStatusRow['name'] ?? '',
                'remarks' => $remarks,
                'application_id' => $id,
                'due_at' => $dueAt,
            ]
        );
    }

    $updateData = [
        'date_updated' => date('Y-m-d H:i:s'),
    ];

    if ($statusChanged) {
        $updateData['status_id'] = $newStatusId;
        $updateData['due_at'] = $dueAt;
    }

    if ($processorChanged) {
        $updateData['assigned_to'] = $newAssignedUserId;
    }

    $db->table('job_applications')
        ->where('id', $id)
        ->update($updateData);

    $logRemarks = [];

    if ($statusChanged) {
        $logRemarks[] = 'Status updated';
    }

    if ($processorChanged) {
        $logRemarks[] = 'Processor reassigned';
    }

    if ($remarks !== '') {
        $logRemarks[] = $remarks;
    }

    $workflow->log(
        $id,
        $currentStatusId,
        $statusChanged ? $newStatusId : $currentStatusId,
        $currentAssignedUserId,
        $processorChanged ? $newAssignedUserId : $currentAssignedUserId,
        implode(' | ', $logRemarks),
        (int) session()->get('admin_id'),
        $statusChanged ? $dueAt : ($application['due_at'] ?? null)
    );

    $successMessage = 'Workflow updated.';

    if ($statusChanged && $processorChanged) {
        $successMessage = 'Status and processor updated.';
    } elseif ($statusChanged) {
        $successMessage = 'Status updated.';
    } elseif ($processorChanged) {
        $successMessage = 'Processor updated.';
    }

    return redirect()->to('/admin/applications/' . $id)->with('success', $successMessage);
}
public function assigned()
{
    helper('dropdown');

    $db = db_connect();
    $adminId = (int) (session()->get('admin_id') ?? 0);

    if ($adminId <= 0) {
        return redirect()->to('/admin')->with('error', 'User session not found.');
    }

    $q = trim((string) $this->request->getGet('q'));
    $statusId = $this->request->getGet('status_id') !== '' ? (int) $this->request->getGet('status_id') : null;
    $isOverdue = (string) $this->request->getGet('overdue') === '1';

    $sort = trim((string) $this->request->getGet('sort'));
    $dir = strtolower(trim((string) $this->request->getGet('dir'))) === 'asc' ? 'ASC' : 'DESC';

    $sortable = [
        'id' => 'ja.id',
        'applicant' => 'a.firstname',
        'email' => 'a.email',
        'job' => 'j.name',
        'status' => 's.name',
        'applied_at' => 'ja.applied_at',
        'due_at' => 'ja.due_at',
    ];

    $sortColumn = $sortable[$sort] ?? 'ja.id';

    $builder = $db->table('job_applications ja')
        ->select('
            ja.id,
            ja.applicant_id,
            ja.job_list_id,
            ja.status_id,
            ja.assigned_to,
            ja.source,
            ja.applied_at,
            ja.due_at,
            a.firstname,
            a.lastname,
            a.email,
            j.name as job_name,
            s.name as status_name,
            u.name as processor_name
        ')
        ->join('applicants a', 'a.id = ja.applicant_id', 'left')
        ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
        ->join('job j', 'j.id = jl.job_id', 'left')
        ->join('status s', 's.id = ja.status_id', 'left')
        ->join('users u', 'u.id = ja.assigned_to', 'left')
        ->where('ja.date_deleted', null)
        ->where('ja.assigned_to', $adminId);

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

    if ($statusId !== null) {
        $builder->where('ja.status_id', $statusId);
    }

    if ($isOverdue) {
        $builder->where('ja.due_at IS NOT NULL', null, false)
            ->where('ja.due_at <', date('Y-m-d H:i:s'));
    }

    $perPage = 10;
    $page = max(1, (int) ($this->request->getGet('page') ?? 1));
    $offset = ($page - 1) * $perPage;

    $countBuilder = clone $builder;
    $total = $countBuilder->countAllResults();

    $applications = $builder
        ->orderBy($sortColumn, $dir)
        ->orderBy('ja.id', 'DESC')
        ->limit($perPage, $offset)
        ->get()
        ->getResultArray();
        $closedStatusNames = ['Hired', 'Decline', 'Failed', 'Withdraw', 'No Show', 'On-Boarding'];
        $closedStatusIds = [];

        foreach ($closedStatusNames as $name) {
            $sid = dd_status_id($name, 'applications');
            if ($sid) {
                $closedStatusIds[] = $sid;
            }
        }

        if (!empty($closedStatusIds)) {
            $builder->whereNotIn('ja.status_id', $closedStatusIds);
        }
    return view('admin/applications/assigned', [
        'applications' => $applications,
        'searchQuery' => $q,
        'statusOptions' => dd_statuses_by_feature('applications'),
        'currentStatusId' => $statusId,
        'currentOverdue' => $isOverdue,
        'currentSort' => $sort,
        'currentDir' => strtolower($dir),
        'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
    ]);
}
}