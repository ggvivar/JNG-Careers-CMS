<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DocumentTemplateModel;

class DocumentTemplateController extends BaseController
{
    public function index()
    {
        helper('dropdown');

        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('document_templates d')
            ->select('d.*, s.name as status_name')
            ->join('status s', 's.id = d.status_id', 'left')
            ->where('d.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('d.name', $q)
                ->orLike('d.template_key', $q)
                ->orLike('d.template_type', $q)
                ->orLike('d.source_table', $q)
                ->orLike('d.file_name_pattern', $q)
                ->orLike('d.description', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $templates = $builder
            ->orderBy('d.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/document_templates/index', [
            'templates'       => $templates,
            'searchQuery'     => $q,
            'variableMap'     => $this->buildVariableMap(),
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper(['dropdown', 'variable']);

        $statusOptions = dd_statuses_by_feature('document-templates');
        $templateTypeOptions = [
            'docx' => 'DOCX Template',
            'pdf'  => 'PDF Layout Source',
        ];
        $sourceTableOptions = template_source_tables();

        if (strtolower($this->request->getMethod()) === 'post') {
            $model = new DocumentTemplateModel();

            $name = trim((string) $this->request->getPost('name'));
            $templateKey = trim((string) $this->request->getPost('template_key'));
            $templateType = trim((string) $this->request->getPost('template_type')) ?: 'docx';
            $sourceTable = trim((string) $this->request->getPost('source_table')) ?: null;

            if ($name === '' || $templateKey === '') {
                return redirect()->back()->withInput()->with('error', 'Name and template key are required.');
            }

            $exists = $model->where('template_key', $templateKey)
                ->where('date_deleted', null)
                ->first();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Template key already exists.');
            }

            try {
                $sourceFilePath = $this->uploadDocumentTemplateFile('source_file', $templateType);
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }

            if ($sourceFilePath === null) {
                return redirect()->back()->withInput()->with('error', 'Source file is required.');
            }

            $availableVars = implode(', ', template_variables_from_table($sourceTable));

            $model->insert([
                'name'              => $name,
                'template_key'      => $templateKey,
                'template_type'     => in_array($templateType, ['docx', 'pdf'], true) ? $templateType : 'docx',
                'source_table'      => $sourceTable,
                'file_name_pattern' => trim((string) $this->request->getPost('file_name_pattern')) ?: null,
                'source_file_path'  => $sourceFilePath,
                'available_vars'    => $availableVars ?: null,
                'description'       => trim((string) $this->request->getPost('description')) ?: null,
                'status_id'         => $this->request->getPost('status_id') ?: null,
                'date_created'      => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/document-templates')->with('success', 'Document template created.');
        }

        return view('admin/document_templates/form', [
            'mode'                => 'create',
            'template'            => null,
            'statusOptions'       => $statusOptions,
            'variableMap'         => $this->buildVariableMap(),
            'templateTypeOptions' => $templateTypeOptions,
            'sourceTableOptions'  => $sourceTableOptions,
        ]);
    }

    public function edit($id)
    {
        helper(['dropdown', 'variable']);

        $model = new DocumentTemplateModel();
        $id = (int) $id;

        $template = $model->where('date_deleted', null)->find($id);

        if (! $template) {
            return redirect()->to('/admin/document-templates')->with('error', 'Document template not found.');
        }

        $statusOptions = dd_statuses_by_feature('document-templates');
        $templateTypeOptions = [
            'docx' => 'DOCX Template',
            'pdf'  => 'PDF Layout Source',
        ];
        $sourceTableOptions = template_source_tables();

        if (strtolower($this->request->getMethod()) === 'post') {
            $name = trim((string) $this->request->getPost('name'));
            $templateKey = trim((string) $this->request->getPost('template_key'));
            $templateType = trim((string) $this->request->getPost('template_type')) ?: 'docx';
            $sourceTable = trim((string) $this->request->getPost('source_table')) ?: null;

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

            try {
                $sourceFilePath = $this->uploadDocumentTemplateFile('source_file', $templateType);
            } catch (\Throwable $e) {
                return redirect()->back()->withInput()->with('error', $e->getMessage());
            }

            $availableVars = implode(', ', template_variables_from_table($sourceTable));

            $payload = [
                'name'              => $name,
                'template_key'      => $templateKey,
                'template_type'     => in_array($templateType, ['docx', 'pdf'], true) ? $templateType : 'docx',
                'source_table'      => $sourceTable,
                'file_name_pattern' => trim((string) $this->request->getPost('file_name_pattern')) ?: null,
                'available_vars'    => $availableVars ?: null,
                'description'       => trim((string) $this->request->getPost('description')) ?: null,
                'status_id'         => $this->request->getPost('status_id') ?: null,
                'date_updated'      => date('Y-m-d H:i:s'),
            ];

            if ($sourceFilePath !== null) {
                if (! empty($template['source_file_path'])) {
                    $oldPath = ROOTPATH . $template['source_file_path'];
                    if (is_file($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $payload['source_file_path'] = $sourceFilePath;
            }

            $model->update($id, $payload);

            return redirect()->to('/admin/document-templates')->with('success', 'Document template updated.');
        }

        return view('admin/document_templates/form', [
            'mode'                => 'edit',
            'template'            => $template,
            'variableMap'         => $this->buildVariableMap(),
            'statusOptions'       => $statusOptions,
            'templateTypeOptions' => $templateTypeOptions,
            'sourceTableOptions'  => $sourceTableOptions,
        ]);
    }

    public function delete($id)
    {
        (new DocumentTemplateModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/document-templates')->with('success', 'Document template deleted.');
    }

    private function uploadDocumentTemplateFile(string $fieldName, ?string $templateType = null): ?string
    {
        $file = $this->request->getFile($fieldName);

        if (! $file || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }

        $allowedExtensions = ['docx', 'pdf'];
        $ext = strtolower($file->getExtension());

        if (! in_array($ext, $allowedExtensions, true)) {
            throw new \RuntimeException('Invalid file type. Only DOCX and PDF are allowed.');
        }

        if ($templateType !== null && $ext !== strtolower($templateType)) {
            throw new \RuntimeException('Uploaded file type does not match selected template type.');
        }

        $targetDir = WRITEPATH . 'uploads/document_templates/';
        if (! is_dir($targetDir)) {
            mkdir($targetDir, 0775, true);
        }

        $newName = $file->getRandomName();
        $file->move($targetDir, $newName);

        return 'writable/uploads/document_templates/' . $newName;
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