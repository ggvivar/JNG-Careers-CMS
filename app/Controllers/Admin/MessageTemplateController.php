<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MessageTemplateModel;

class MessageTemplateController extends BaseController
{
    public function index()
    {
        helper('dropdown');

        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('message_templates t')
            ->select('t.*, s.name as status_name')
            ->join('status s', 's.id = t.status_id', 'left')
            ->where('t.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('t.name', $q)
                ->orLike('t.template_key', $q)
                ->orLike('t.channel', $q)
                ->orLike('t.source_table', $q)
                ->orLike('t.subject', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $templates = $builder
            ->orderBy('t.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/message_templates/index', [
            'templates' => $templates,
            'searchQuery' => $q,
            'variableMap' => $this->buildVariableMap(),
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper(['dropdown', 'variable']);

        $statusOptions = dd_statuses_by_feature('message-templates');
        $channelOptions = [
            'email' => 'Email',
            'sms'   => 'SMS',
        ];
        $sourceTableOptions = template_source_tables();

        if (strtolower($this->request->getMethod()) === 'post') {
            $model = new MessageTemplateModel();

            $name = trim((string) $this->request->getPost('name'));
            $templateKey = trim((string) $this->request->getPost('template_key'));
            $channel = trim((string) $this->request->getPost('channel')) ?: 'email';
            $sourceTable = trim((string) $this->request->getPost('source_table')) ?: null;

            $bodyTemplate = $channel === 'sms'
                ? (string) $this->request->getPost('body_template_sms')
                : (string) $this->request->getPost('body_template');

            if ($name === '' || $templateKey === '') {
                return redirect()->back()->withInput()->with('error', 'Name and template key are required.');
            }

            $exists = $model->where('template_key', $templateKey)
                ->where('date_deleted', null)
                ->first();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Template key already exists.');
            }

            $availableVars = implode(', ', template_variables_from_table($sourceTable));

            $model->insert([
                'name'           => $name,
                'template_key'   => $templateKey,
                'channel'        => in_array($channel, ['email', 'sms'], true) ? $channel : 'email',
                'source_table'   => $sourceTable,
                'subject'        => trim((string) $this->request->getPost('subject')) ?: null,
                'body_template'  => $bodyTemplate ?: null,
                'available_vars' => $availableVars ?: null,
                'status_id'      => $this->request->getPost('status_id') ?: null,
                'date_created'   => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/message-templates')->with('success', 'Message template created.');
        }

        return view('admin/message_templates/form', [
            'mode'               => 'create',
            'template'           => null,
            'variableMap'        => $this->buildVariableMap(),
            'statusOptions'      => $statusOptions,
            'channelOptions'     => $channelOptions,
            'sourceTableOptions' => $sourceTableOptions,
        ]);
    }

    public function edit($id)
    {
        helper(['dropdown', 'variable']);

        $model = new MessageTemplateModel();
        $id = (int) $id;

        $template = $model->where('date_deleted', null)->find($id);

        if (! $template) {
            return redirect()->to('/admin/message-templates')->with('error', 'Message template not found.');
        }

        $statusOptions = dd_statuses_by_feature('message-templates');
        $channelOptions = [
            'email' => 'Email',
            'sms'   => 'SMS',
        ];
        $sourceTableOptions = template_source_tables();

        if (strtolower($this->request->getMethod()) === 'post') {
            $name = trim((string) $this->request->getPost('name'));
            $templateKey = trim((string) $this->request->getPost('template_key'));
            $channel = trim((string) $this->request->getPost('channel')) ?: 'email';
            $sourceTable = trim((string) $this->request->getPost('source_table')) ?: null;

            $bodyTemplate = $channel === 'sms'
                ? (string) $this->request->getPost('body_template_sms')
                : (string) $this->request->getPost('body_template');

            if ($name === '' || $templateKey === '') {
                return redirect()->back()->withInput()->with('error', 'Name and template key are required.');
            }

            $exists = $model->where('template_key', $templateKey)
                ->where('id !=', $id)
                ->where('date_deleted', null)
                ->first();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Template key already exists.');
            }

            $availableVars = implode(', ', template_variables_from_table($sourceTable));

            $model->update($id, [
                'name'           => $name,
                'template_key'   => $templateKey,
                'channel'        => in_array($channel, ['email', 'sms'], true) ? $channel : 'email',
                'source_table'   => $sourceTable,
                'subject'        => trim((string) $this->request->getPost('subject')) ?: null,
                'body_template'  => $bodyTemplate ?: null,
                'available_vars' => $availableVars ?: null,
                'status_id'      => $this->request->getPost('status_id') ?: null,
                'date_updated'   => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/message-templates')->with('success', 'Message template updated.');
        }

        return view('admin/message_templates/form', [
            'mode'               => 'edit',
            'template'           => $template,
            'variableMap'        => $this->buildVariableMap(),
            'statusOptions'      => $statusOptions,
            'channelOptions'     => $channelOptions,
            'sourceTableOptions' => $sourceTableOptions,
        ]);
    }

    public function delete($id)
    {
        (new MessageTemplateModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/message-templates')->with('success', 'Message template deleted.');
    }

    private function buildVariableMap(): array
    {
        helper('variable');

        $map = [];
        foreach (template_source_tables() as $table => $label) {
            $map[$table] = template_variables_from_table($table);
        }

        return $map;
    }
}