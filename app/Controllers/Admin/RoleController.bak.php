<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;

class RoleController extends BaseController
{
    public function index()
    {
        $db = db_connect(); 
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('roles r')
            ->select('r.*, s.name as status_name')
            ->join('status s', 's.id = r.status_id', 'left')
            ->where('r.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('r.name', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
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

        return view('admin/roles/index', [
            'roles' => $roles,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');

        $statusOptions = dd_statuses_by_feature('roles');

        if (strtolower($this->request->getMethod()) === 'post') {
            $model = new RoleModel();

            $name = trim((string) $this->request->getPost('name'));
            $statusId = $this->request->getPost('status_id') ?: null;

            if ($name === '') {
                return redirect()->back()->withInput()->with('error', 'Role name is required.');
            }

            $exists = $model->where('name', $name)
                ->where('date_deleted', null)
                ->first();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Role name already exists.');
            }

            $model->insert([
                'name' => $name,
                'status_id' => $statusId,
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

        $id = (int) $id;
        $model = new RoleModel();
        $role = $model->where('date_deleted', null)->find($id);

        if (! $role) {
            return redirect()->to('/admin/roles')->with('error', 'Role not found.');
        }

        $statusOptions = dd_statuses_by_feature('roles');

        if (strtolower($this->request->getMethod()) === 'post') {
            $name = trim((string) $this->request->getPost('name'));
            $statusId = $this->request->getPost('status_id') ?: null;

            if ($name === '') {
                return redirect()->back()->withInput()->with('error', 'Role name is required.');
            }

            $exists = $model->where('name', $name)
                ->where('id !=', $id)
                ->where('date_deleted', null)
                ->first();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Role name already exists.');
            }

            $model->update($id, [
                'name' => $name,
                'status_id' => $statusId,
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
        $id = (int) $id;

        db_connect()->table('roles')
            ->where('id', $id)
            ->update([
                'date_deleted' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/roles')->with('success', 'Role deleted.');
    }

    /**
     * Compatibility alias for old route:
     * /admin/roles/{id}/modules
     */
    public function modules($roleId)
    {
        return $this->permissions($roleId);
    }

    /**
     * Preferred permissions editor.
     */
    public function permissions($roleId)
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
            ->select('
                f.id,
                f.name as feature_name,
                f.code as feature_code,
                f.sort_order,
                m.id as module_id,
                m.name as module_name
            ')
            ->join('modules m', 'm.id = f.module_id', 'left')
            ->where('f.date_deleted', null)
            ->orderBy('m.name', 'ASC')
            ->orderBy('f.sort_order', 'ASC')
            ->orderBy('f.name', 'ASC')
            ->get()
            ->getResultArray();

        if (strtolower($this->request->getMethod()) === 'post') {
            $permissions = $this->request->getPost('permissions') ?? [];
            $now = date('Y-m-d H:i:s');

            $db->table('role_feature_permissions')
                ->where('role_id', $roleId)
                ->delete();

            foreach ($features as $feature) {
                $featureId = (int) $feature['id'];
                $row = $permissions[$featureId] ?? [];

                $payload = [
                    'role_id' => $roleId,
                    'feature_id' => $featureId,
                    'can_view' => ! empty($row['can_view']) ? 1 : 0,
                    'can_add' => ! empty($row['can_add']) ? 1 : 0,
                    'can_edit' => ! empty($row['can_edit']) ? 1 : 0,
                    'can_delete' => ! empty($row['can_delete']) ? 1 : 0,
                    'can_export' => ! empty($row['can_export']) ? 1 : 0,
                    'can_import' => ! empty($row['can_import']) ? 1 : 0,
                    'date_created' => $now,
                    'date_updated' => $now,
                ];

                $db->table('role_feature_permissions')->insert($payload);
            }

            return redirect()->to('/admin/roles/' . $roleId . '/modules')->with('success', 'Role permissions updated.');
        }

        $permissionRows = $db->table('role_feature_permissions')
            ->where('role_id', $roleId)
            ->get()
            ->getResultArray();

        $permissionMap = [];
        foreach ($permissionRows as $row) {
            $permissionMap[(int) $row['feature_id']] = $row;
        }

        $grouped = [];
        foreach ($features as $feature) {
            $moduleName = $feature['module_name'] ?: 'Unassigned';
            $featureId = (int) $feature['id'];

            $grouped[$moduleName][] = [
                'id' => $featureId,
                'feature_name' => $feature['feature_name'],
                'feature_code' => $feature['feature_code'],
                'permissions' => $permissionMap[$featureId] ?? [
                    'can_view' => 0,
                    'can_add' => 0,
                    'can_edit' => 0,
                    'can_delete' => 0,
                    'can_export' => 0,
                    'can_import' => 0,
                ],
            ];
        }

        return view('admin/roles/modules', [
            'role' => $role,
            'groupedFeatures' => $grouped,
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

        if (strtolower($this->request->getMethod()) === 'post') {
            $userIds = $this->request->getPost('user_ids') ?? [];
            $userIds = array_map('intval', (array) $userIds);

            $db->table('users')
                ->where('role_id', $roleId)
                ->update([
                    'role_id' => null,
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

            if (! empty($userIds)) {
                $db->table('users')
                    ->whereIn('id', $userIds)
                    ->update([
                        'role_id' => $roleId,
                        'date_updated' => date('Y-m-d H:i:s'),
                    ]);
            }

            return redirect()->to('/admin/roles/' . $roleId . '/users')->with('success', 'Role users updated.');
        }

        $users = $db->table('users')
            ->select('id, username, name, email, role_id')
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->orderBy('username', 'ASC')
            ->get()
            ->getResultArray();

        $assignedUserIds = [];
        foreach ($users as $user) {
            if ((int) ($user['role_id'] ?? 0) === $roleId) {
                $assignedUserIds[] = (int) $user['id'];
            }
        }

        return view('admin/roles/users', [
            'role' => $role,
            'users' => $users,
            'assignedUserIds' => $assignedUserIds,
        ]);
    }
}