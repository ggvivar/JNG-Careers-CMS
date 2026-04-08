<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class WorkflowTransitionController extends BaseController
{
    public function index()
{
    helper('dropdown');
    $db = db_connect();
    $q = trim((string) $this->request->getGet('q'));

    $builder = $db->table('features f')
        ->select("
            f.id as feature_id,
            f.name as feature_name,
            f.code as feature_code,
            m.name as module_name,
            COUNT(wt.id) as transition_count
        ")
        ->join('modules m', 'm.id = f.module_id', 'left')
        ->join('workflow_transitions wt', 'wt.feature_id = f.id AND wt.date_deleted IS NULL', 'left')
        ->where('f.date_deleted', null)
        ->groupBy('f.id');

    if ($q !== '') {
        $builder->groupStart()
            ->like('f.name', $q)
            ->orLike('f.code', $q)
            ->orLike('m.name', $q)
            ->groupEnd();
    }

    $rows = $builder
        ->orderBy('m.name', 'ASC')
        ->orderBy('f.sort_order', 'ASC')
        ->orderBy('f.name', 'ASC')
        ->get()
        ->getResultArray();

    return view('admin/workflow_transitions/index', [
        'rows' => $rows,
        'searchQuery' => $q,
    ]);
}

    public function create()
    {
        return redirect()->to('/admin/workflows')->with('error', 'Please choose a workflow feature first.');
    }

    public function edit($id)
    {
        helper('dropdown');
        $db = db_connect();
        $id = (int) $id;

        $row = $db->table('workflow_transitions')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $row) {
            return redirect()->to('/admin/workflows')->with('error', 'Workflow transition not found.');
        }

        $featureRow = $db->table('features')
            ->select('id, name, code')
            ->where('id', (int) $row['feature_id'])
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $featureRow) {
            return redirect()->to('/admin/workflows')->with('error', 'Workflow feature not found.');
        }

        $statusOptions = dd_statuses_by_feature($featureRow['code']);

        $messageTemplateOptions = [];
        $templateRows = $db->table('message_templates')
            ->select('template_key, name, channel, source_table')
            ->where('date_deleted', null)
            ->where('channel', 'email')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($templateRows as $templateRow) {
            $messageTemplateOptions[$templateRow['template_key']] =
                $templateRow['name'] . ' [' . $templateRow['template_key'] . ']';
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $fromStatusId = (int) ($this->request->getPost('status_id_from') ?: 0);
            $toStatusId = (int) ($this->request->getPost('status_id_to') ?: 0);
            $notificationDays = $this->request->getPost('grace_period');
            $sortOrder = (int) ($this->request->getPost('sort_order') ?: 0);
            $requireRemarks = $this->request->getPost('require_remarks') ? 1 : 0;
            $isActive = $this->request->getPost('status_id') ? 1 : 0;
            $sendEmail = $this->request->getPost('send_email') ? 1 : 0;
            $emailTemplateKey = trim((string) $this->request->getPost('email_template_key'));
            
            // if ($fromStatusId <= 0 || $toStatusId <= 0) {
            //     return redirect()->back()->withInput()->with('error', 'From Status and To Status are required.');
            // }

            if ($fromStatusId === $toStatusId) {
                return redirect()->back()->withInput()->with('error', 'From Status and To Status cannot be the same.');
            }

            if ($sendEmail && $emailTemplateKey === '') {
                return redirect()->back()->withInput()->with('error', 'Email template is required when Send Email is enabled.');
            }

            $exists = $db->table('workflow_transitions')
                ->where('feature_id', (int) $featureRow['id'])
                ->where('status_id_from', $fromStatusId)
                ->where('status_id_to', $toStatusId)
                ->where('id !=', $id)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'This workflow transition already exists.');
            }

            $db->table('workflow_transitions')
                ->where('id', $id)
                ->update([
                    'status_id_from' => $fromStatusId,
                    'status_id_to' => $toStatusId,
                    'grace_period' => $notificationDays !== '' ? (int) $notificationDays : null,
                    'sort_order' => $sortOrder,
                    'require_remarks' => $requireRemarks,
                    'status_id' => $isActive,
                    'send_email' => $sendEmail,
                    'email_template_key' => $emailTemplateKey !== '' ? $emailTemplateKey : null,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

            return redirect()->to('/admin/workflows/edit-feature/' . $featureRow['code'])
                ->with('success', 'Workflow transition updated.');
        }

        return view('admin/workflow_transitions/form', [
            'mode' => 'edit',
            'row' => $row,
            'feature' => $featureRow,
            'statusOptions' => $statusOptions,
            'messageTemplateOptions' => $messageTemplateOptions,
        ]);
    }

    public function editFeature($featureCode)
    {
        helper('dropdown');
        $db = db_connect();

        $featureRow = $db->table('features')
            ->select('id, name, code')
            ->where('code', $featureCode)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $featureRow) {
            return redirect()->to('/admin/workflows')->with('error', 'Workflow feature not found.');
        }

        $featureId = (int) $featureRow['id'];
        $statusOptions = dd_statuses_by_feature($featureCode);

        $messageTemplateOptions = [];
        $templateRows = $db->table('message_templates')
            ->select('template_key, name, channel, source_table')
            ->where('date_deleted', null)
            ->where('channel', 'email')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($templateRows as $templateRow) {
            $messageTemplateOptions[$templateRow['template_key']] =
                $templateRow['name'] . ' [' . $templateRow['template_key'] . ']';
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $fromStatusId = (int) ($this->request->getPost('status_id_from') ?: 0);
            $toStatusId = (int) ($this->request->getPost('status_id_to') ?: 0);
            $notificationDays = $this->request->getPost('grace_period');
            $sortOrder = (int) ($this->request->getPost('sort_order') ?: 0);
            $requireRemarks = $this->request->getPost('require_remarks') ? 1 : 0;
            $isActive = $this->request->getPost('status_id') ? 1 : 0;
            $sendEmail = $this->request->getPost('send_email') ? 1 : 0;
            $emailTemplateKey = trim((string) $this->request->getPost('email_template_key'));

            // if ($fromStatusId <= 0 || $toStatusId <= 0) {
            //     return redirect()->back()->withInput()->with('error', 'From Status and To Status are required.');
            // }

            if ($fromStatusId === $toStatusId) {
                return redirect()->back()->withInput()->with('error', 'From Status and To Status cannot be the same.');
            }

            if ($sendEmail && $emailTemplateKey === '') {
                return redirect()->back()->withInput()->with('error', 'Email template is required when Send Email is enabled.');
            }

            $exists = $db->table('workflow_transitions')
                ->where('feature_id', $featureId)
                ->where('status_id_from', $fromStatusId)
                ->where('status_id_to', $toStatusId)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'This workflow transition already exists.');
            }

            $db->table('workflow_transitions')->insert([
                'feature_id' => $featureId,
                'status_id_from' => $fromStatusId,
                'status_id_to' => $toStatusId,
                'grace_period' => $notificationDays !== '' ? (int) $notificationDays : null,
                'sort_order' => $sortOrder,
                'require_remarks' => $requireRemarks,
                'status_id' => $isActive,
                'send_email' => $sendEmail,
                'email_template_key' => $emailTemplateKey !== '' ? $emailTemplateKey : null,
                'date_created' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/workflows/edit-feature/' . $featureCode)
                ->with('success', 'Workflow transition added.');
        }

        $rows = $db->table('workflow_transitions wt')
            ->select("
                wt.*,
                fs.name as from_status_name,
                ts.name as to_status_name
            ")
            ->join('status fs', 'fs.id = wt.status_id_from', 'left')
            ->join('status ts', 'ts.id = wt.status_id_to', 'left')
            ->where('wt.feature_id', $featureId)
            ->where('wt.date_deleted', null)
            ->orderBy('fs.name', 'ASC')
            ->orderBy('wt.sort_order', 'ASC')
            ->orderBy('ts.name', 'ASC')
            ->get()
            ->getResultArray();

        $groupedTransitions = [];
        foreach ($rows as $row) {
            $groupedTransitions[$row['from_status_name']][] = $row;
        }

        return view('admin/workflow_transitions/edit_feature', [
            'feature' => $featureRow,
            'statusOptions' => $statusOptions,
            'groupedTransitions' => $groupedTransitions,
            'messageTemplateOptions' => $messageTemplateOptions,
        ]);
    }

    public function delete($id)
    {
        $db = db_connect();
        $id = (int) $id;

        $row = $db->table('workflow_transitions as wt')
            ->select('wt.id, wt.feature_id, f.code as feature_code')
            ->join('features f', 'f.id = wt.feature_id', 'left')
            ->where('wt.id', $id)
            ->where('wt.date_deleted', null)
            ->get()
            ->getRowArray();

        $db->table('workflow_transitions')
            ->where('id', $id)
            ->update([
                'date_deleted' => date('Y-m-d H:i:s'),
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

        if (! empty($row['feature_code'])) {
            return redirect()->to('/admin/workflows/edit-feature/' . $row['feature_code'])
                ->with('success', 'Workflow transition deleted.');
        }

        return redirect()->to('/admin/workflows')->with('success', 'Workflow transition deleted.');
    }

    public function statusesByFeature()
    {
        helper('dropdown');
        $db = db_connect();

        $featureId = (int) ($this->request->getGet('feature_id') ?: 0);
        if ($featureId <= 0) {
            return $this->response->setJSON([]);
        }

        $featureRow = $db->table('features')
            ->select('id, code')
            ->where('id', $featureId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $featureRow) {
            return $this->response->setJSON([]);
        }

        $statuses = dd_statuses_by_feature($featureRow['code']);
        $rows = [];

        foreach ($statuses as $id => $name) {
            $rows[] = [
                'id' => (int) $id,
                'name' => $name,
            ];
        }

        return $this->response->setJSON($rows);
    }
}