<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CommonDefaultModel;

class CommonDefaultController extends BaseController
{
    protected CommonDefaultModel $model;

    public function __construct()
    {
        $this->model = new CommonDefaultModel();
        helper('dropdown');
    }

    public function index()
{
    $q     = trim((string) $this->request->getGet('q'));
    $group = trim((string) $this->request->getGet('group'));

    $builder = $this->model
        ->select('*')
        ->where('date_deleted', null);

    if ($q !== '') {
        $builder->groupStart()
            ->like('key1', $q)
            ->orLike('key2', $q)
            ->orLike('key3', $q)
            ->orLike('key4', $q)
            ->orLike('key5', $q)
            ->orLike('value', $q)
            ->orLike('definition', $q)
            ->groupEnd();
    }

    if ($group !== '') {
        $builder->where('key1', $group);
    }

    $defaults = $builder
        ->orderBy('key1', 'ASC')
        ->orderBy('value', 'ASC')
        ->findAll();

    return view('admin/common_defaults/index', [
        'defaults'        => $defaults,
        'searchQuery'     => $q,
        'currentGroup'    => $group,
        'groupOptions'    => ['' => 'All Groups'] + dd_common_default_groups(),
        'paginationLinks' => '',
    ]);
}

    public function create()
    {
        $default = [
            'key1'       => '',
            'key2'       => '',
            'key3'       => '',
            'key4'       => '',
            'key5'       => '',
            'value'      => '',
            'definition' => '',
        ];

        if ($this->request->getMethod() === 'post') {
            $data = $this->prepareData('create');

            if (! $this->model->insert($data)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', implode('<br>', $this->model->errors()));
            }

            return redirect()
                ->to(site_url('admin/common-defaults'))
                ->with('success', 'Default value created.');
        }

        return view('admin/common_defaults/form', [
            'mode'    => 'create',
            'default' => $default,
        ]);
    }

    public function edit($id = null)
    {
        $id = (int) $id;

        $default = $this->model
            ->where('date_deleted', null)
            ->find($id);

        if (! $default) {
            return redirect()
                ->to(site_url('admin/common-defaults'))
                ->with('error', 'Default value not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $data = $this->prepareData('update');

            if (! $this->model->update($id, $data)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', implode('<br>', $this->model->errors()));
            }

            return redirect()
                ->to(site_url('admin/common-defaults'))
                ->with('success', 'Default value updated.');
        }

        return view('admin/common_defaults/form', [
            'mode'    => 'edit',
            'default' => $default,
        ]);
    }

    public function delete($id = null)
    {
        $id = (int) $id;

        $default = $this->model
            ->where('date_deleted', null)
            ->find($id);

        if (! $default) {
            return redirect()
                ->to(site_url('admin/common-defaults'))
                ->with('error', 'Default value not found.');
        }

        $this->model->update($id, [
            'date_deleted' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
        ]);

        return redirect()
            ->to(site_url('admin/common-defaults'))
            ->with('success', 'Default value deleted.');
    }

    private function prepareData(string $mode = 'create'): array
    {
        $data = [
            'key1'       => trim((string) $this->request->getPost('key1')),
            'key2'       => trim((string) $this->request->getPost('key2')) ?: null,
            'key3'       => trim((string) $this->request->getPost('key3')) ?: null,
            'key4'       => trim((string) $this->request->getPost('key4')) ?: null,
            'key5'       => trim((string) $this->request->getPost('key5')) ?: null,
            'value'      => trim((string) $this->request->getPost('value')),
            'definition' => trim((string) $this->request->getPost('definition')) ?: null,
        ];

        if ($mode === 'create') {
            $data['date_created'] = date('Y-m-d H:i:s');
        }

        $data['date_updated'] = date('Y-m-d H:i:s');

        return $data;
    }
    public function saveInline()
{
    $id = (int) ($this->request->getPost('id') ?? 0);

    $data = [
        'key1'         => trim((string) $this->request->getPost('key1')) ?: null,
        'key2'         => trim((string) $this->request->getPost('key2')) ?: null,
        'key3'         => trim((string) $this->request->getPost('key3')) ?: null,
        'key4'         => trim((string) $this->request->getPost('key4')) ?: null,
        'key5'         => trim((string) $this->request->getPost('key5')) ?: null,
        'value'        => trim((string) $this->request->getPost('value')),
        'definition'   => trim((string) $this->request->getPost('definition')) ?: null,
        'date_updated' => date('Y-m-d H:i:s'),
    ];

    if ($data['key1'] === null || $data['value'] === '') {
        return $this->response->setStatusCode(422)->setJSON([
            'status'  => 'error',
            'message' => 'Group and Value are required.',
        ]);
    }

    $duplicateBuilder = $this->model
        ->where('date_deleted', null)
        ->where('key1', $data['key1'])
        ->where('value', $data['value']);

    if ($id > 0) {
        $existing = $this->model->where('date_deleted', null)->find($id);

        if (! $existing) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'  => 'error',
                'message' => 'Record not found.',
            ]);
        }

        $duplicateBuilder->where('id !=', $id);
    }

    $duplicate = $duplicateBuilder->countAllResults();

    if ($duplicate > 0) {
        return $this->response->setStatusCode(409)->setJSON([
            'status'  => 'error',
            'message' => 'Duplicate value already exists in this group.',
        ]);
    }

    if ($id > 0) {
        if (! $this->model->update($id, $data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => implode(' ', $this->model->errors()),
            ]);
        }
    } else {
        $data['date_created'] = date('Y-m-d H:i:s');

        if (! $this->model->insert($data)) {
            return $this->response->setStatusCode(422)->setJSON([
                'status'  => 'error',
                'message' => implode(' ', $this->model->errors()),
            ]);
        }

        $id = (int) $this->model->getInsertID();
    }

    return $this->response->setJSON([
        'status'  => 'success',
        'id'      => $id,
        'message' => 'Saved successfully.',
    ]);
}

public function createGroup()
{
    $group = trim((string) $this->request->getPost('group'));

    if ($group === '') {
        return $this->response->setStatusCode(422)->setJSON([
            'status'  => 'error',
            'message' => 'Group name is required.',
        ]);
    }

    $exists = $this->model
        ->where('date_deleted', null)
        ->where('key1', $group)
        ->countAllResults();

    if ($exists > 0) {
        return $this->response->setStatusCode(409)->setJSON([
            'status'  => 'error',
            'message' => 'Group already exists.',
        ]);
    }

    return $this->response->setJSON([
        'status'  => 'success',
        'group'   => $group,
        'message' => 'Group ready. Add a value to save it.',
    ]);
}

public function renameGroup()
{
    $oldGroup = trim((string) $this->request->getPost('old_group'));
    $newGroup = trim((string) $this->request->getPost('new_group'));

    if ($oldGroup === '' || $newGroup === '') {
        return $this->response->setStatusCode(422)->setJSON([
            'status'  => 'error',
            'message' => 'Old group and new group are required.',
        ]);
    }

    if ($oldGroup === $newGroup) {
        return $this->response->setJSON([
            'status'  => 'success',
            'group'   => $newGroup,
            'message' => 'No changes made.',
        ]);
    }

    $exists = $this->model
        ->where('date_deleted', null)
        ->where('key1', $newGroup)
        ->countAllResults();

    if ($exists > 0) {
        return $this->response->setStatusCode(409)->setJSON([
            'status'  => 'error',
            'message' => 'New group name already exists.',
        ]);
    }

    $this->model
        ->where('date_deleted', null)
        ->where('key1', $oldGroup)
        ->set([
            'key1' => $newGroup,
            'date_updated' => date('Y-m-d H:i:s'),
        ])
        ->update();

    return $this->response->setJSON([
        'status'  => 'success',
        'group'   => $newGroup,
        'message' => 'Group renamed successfully.',
    ]);
}

public function deleteGroup()
{
    $group = trim((string) $this->request->getPost('group'));

    if ($group === '') {
        return $this->response->setStatusCode(422)->setJSON([
            'status'  => 'error',
            'message' => 'Group is required.',
        ]);
    }

    $this->model
        ->where('date_deleted', null)
        ->where('key1', $group)
        ->set([
            'date_deleted' => date('Y-m-d H:i:s'),
            'date_updated' => date('Y-m-d H:i:s'),
        ])
        ->update();

    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Group deleted successfully.',
    ]);
}

public function deleteInline($id)
{
    $row = $this->model->where('date_deleted', null)->find((int) $id);

    if (! $row) {
        return $this->response->setStatusCode(404)->setJSON([
            'status'  => 'error',
            'message' => 'Record not found.',
        ]);
    }

    $this->model->update((int) $id, [
        'date_deleted' => date('Y-m-d H:i:s'),
        'date_updated' => date('Y-m-d H:i:s'),
    ]);

    return $this->response->setJSON([
        'status'  => 'success',
        'message' => 'Deleted successfully.',
    ]);
}
}