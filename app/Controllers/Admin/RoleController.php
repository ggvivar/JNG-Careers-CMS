<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\ModuleModel;

class RoleController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('roles r')
            ->select('r.*')
            ->where('r.date_deleted', null);

        if ($q !== '') {
            $builder->like('r.name', $q);
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $roles = $builder
            ->orderBy('r.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        // foreach ($roles as &$role) {
        //     $mods = $db->table('role_modules rm')
        //         ->select('m.name')
        //         ->join('modules m', 'm.id = rm.module_id', 'left')
        //         ->where('rm.role_id', (int) $role['id'])
        //         ->orderBy('m.name', 'ASC')
        //         ->get()
        //         ->getResultArray();

        //     $role['modules_list'] = implode(', ', array_column($mods, 'name'));
        // }

        $pager = service('pager');

        return view('admin/roles/index', [
            'roles' => $roles,
            'searchQuery' => $q,
            'paginationLinks' => $pager->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('Role');

        if ($this->request->getMethod() === 'post') {
            (new RoleModel())->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/roles')->with('success', 'Role created.');
        }

        return view('admin/roles/form', [
            'mode' => 'create',
            'role' => null,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('Roles');

        $model = new RoleModel();
        $role = $model->where('date_deleted', null)->find((int) $id);

        if (! $role) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found.');
        }

        if ($this->request->getMethod() === 'post') {
            $model->update((int) $id, [
                'name' => trim((string) $this->request->getPost('name')),
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/roles')->with('success', 'Role updated.');
        }

        return view('admin/roles/form', [
            'mode' => 'edit',
            'role' => $role,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function delete($id)
    {
        $db = db_connect();
        $id = (int) $id;

        $db->table('roles')->where('id', $id)->update([
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        $db->table('role_modules')->where('role_id', $id)->delete();

        return redirect()->to('/admin/roles')->with('success', 'Role deleted.');
    }

    public function modules($roleId)
    {
        $db = db_connect();
        $roleId = (int) $roleId;

        $role = $db->table('roles')
            ->where('id', $roleId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $role) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found.');
        }

        $modules = (new ModuleModel())
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        $existingRows = $db->table('role_modules')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        $existing = [];
        foreach ($existingRows as $row) {
            $existing[(int) $row['module_id']] = $row;
        }

        if ($this->request->getMethod() === 'post') {
            $enabled = $this->request->getPost('enabled') ?? [];
            $canAdd = $this->request->getPost('can_add') ?? [];
            $canEdit = $this->request->getPost('can_edit') ?? [];
            $canDelete = $this->request->getPost('can_delete') ?? [];
            $canExport = $this->request->getPost('can_export') ?? [];
            $canImport = $this->request->getPost('can_import') ?? [];

            $db->table('role_modules')->where('role_id', $roleId)->delete();

            $batch = [];
            $now = date('Y-m-d H:i:s');

            foreach ($modules as $module) {
                $moduleId = (int) $module['id'];

                if (! isset($enabled[$moduleId])) {
                    continue;
                }

                $batch[] = [
                    'role_id' => $roleId,
                    'module_id' => $moduleId,
                    'can_add' => isset($canAdd[$moduleId]) ? 1 : 0,
                    'can_edit' => isset($canEdit[$moduleId]) ? 1 : 0,
                    'can_delete' => isset($canDelete[$moduleId]) ? 1 : 0,
                    'can_export' => isset($canExport[$moduleId]) ? 1 : 0,
                    'can_import' => isset($canImport[$moduleId]) ? 1 : 0,
                    'date_created' => $now,
                    'date_updated' => $now,
                ];
            }

            if (! empty($batch)) {
                $db->table('role_modules')->insertBatch($batch);
            }

            return redirect()->to('/admin/roles')->with('success', 'Role permissions updated.');
        }

        return view('admin/roles/modules', [
            'role' => $role,
            'modules' => $modules,
            'existing' => $existing,
        ]);
    }

    public function users($roleId)
    {
        $db = db_connect();
        $roleId = (int) $roleId;

        $role = $db->table('roles')
            ->where('id', $roleId)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $role) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found.');
        }
        

        if ($this->request->getMethod() === 'post') {
            $userId = (int) $this->request->getPost('user_id');

            $db->table('users')
                ->where('id', $userId)
                ->where('date_deleted', null)
                ->update([
                    'role_id' => $roleId,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

            return redirect()->to('/admin/roles/' . $roleId . '/users')->with('success', 'User assigned.');
        }
        
        $users = $db->table('users')
            ->where('date_deleted', null)
            ->orderBy('id', 'DESC')
            ->get()
            ->getResultArray();

        return view('admin/roles/users', [
            'role' => $role,
            'users' => $users,
        ]);
    }
    public function features($roleId)
{
    $db = db_connect();
    $roleId = (int) $roleId;

    $role = $db->table('roles')
        ->where('id', $roleId)
        ->where('date_deleted', null)
        ->get()
        ->getRowArray();

    if (! $role) {
        return redirect()->to('/admin/roles')->with('error', 'Role not found.');
    }

    $features = $db->table('features f')
        ->select('f.*, m.name as module_name')
        ->join('modules m', 'm.id = f.module_id', 'left')
        ->where('f.date_deleted', null)
        ->orderBy('m.name', 'ASC')
        ->orderBy('f.sort_order', 'ASC')
        ->get()
        ->getResultArray();

    $existingRows = $db->table('role_feature_permissions')
        ->where('role_id', $roleId)
        ->get()
        ->getResultArray();

    $existing = [];
    foreach ($existingRows as $row) {
        $existing[(int) $row['feature_id']] = $row;
    }

    if (strtolower($this->request->getMethod()) === 'post') {
        $enabled   = $this->request->getPost('enabled') ?? [];
        $canView   = $this->request->getPost('can_view') ?? [];
        $canAdd    = $this->request->getPost('can_add') ?? [];
        $canEdit   = $this->request->getPost('can_edit') ?? [];
        $canDelete = $this->request->getPost('can_delete') ?? [];
        $canExport = $this->request->getPost('can_export') ?? [];
        $canImport = $this->request->getPost('can_import') ?? [];

        $db->table('role_feature_permissions')->where('role_id', $roleId)->delete();

        $batch = [];
        $now = date('Y-m-d H:i:s');

        foreach ($features as $feature) {
            $featureId = (int) $feature['id'];

            if (! isset($enabled[$featureId])) {
                continue;
            }

            $batch[] = [
                'role_id' => $roleId,
                'module_id' => $feature['module_id'],
                'feature_id' => $featureId,
                'can_view' => isset($canView[$featureId]) ? 1 : 0,
                'can_add' => isset($canAdd[$featureId]) ? 1 : 0,
                'can_edit' => isset($canEdit[$featureId]) ? 1 : 0,
                'can_delete' => isset($canDelete[$featureId]) ? 1 : 0,
                'can_export' => isset($canExport[$featureId]) ? 1 : 0,
                'can_import' => isset($canImport[$featureId]) ? 1 : 0,
                'date_created' => $now,
                'date_updated' => $now,
            ];
        }

        if (! empty($batch)) {
            $db->table('role_feature_permissions')->insertBatch($batch);
        }

        return redirect()->to('/admin/roles')->with('success', 'Role feature permissions updated.');
    }

    return view('admin/roles/features', [
        'role' => $role,
        'features' => $features,
        'existing' => $existing,
    ]);
}
}