<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\UserCompanyModel;

class UserController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('users u')
            ->select("
                u.id,
                u.username,
                u.email,
                u.name,
                u.department_id,
                u.role_id,
                u.status_id,
                d.name as department_name,
                r.name as role_name,
                GROUP_CONCAT(c.name ORDER BY c.name SEPARATOR ', ') as company_names
            ")
            ->join('departments d', 'd.id = u.department_id', 'left')
            ->join('roles r', 'r.id = u.role_id', 'left')
            ->join('user_companies uc', 'uc.user_id = u.id', 'left')
            ->join('companies c', 'c.id = uc.company_id', 'left')
            ->where('u.date_deleted', null)
            ->groupBy('u.id');

        if ($q !== '') {
            $builder->groupStart()
                ->like('u.username', $q)
                ->orLike('u.email', $q)
                ->orLike('u.name', $q)
                ->orLike('d.name', $q)
                ->orLike('r.name', $q)
                ->orLike('c.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = count($countBuilder->get()->getResultArray());

        $users = $builder
            ->orderBy('u.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/users/index', [
            'users' => $users,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');

        $roleOptions = dd_options('roles', 'id', 'name', [], ['name' => 'ASC']);
        $companyOptions = dd_options('companies', 'id', 'name', [], ['name' => 'ASC']);
        $departmentOptions = dd_options('departments', 'id', 'name', [], ['name' => 'ASC']);

        if (strtolower($this->request->getMethod()) === 'post') {
            $db = db_connect();

            $username = trim((string) $this->request->getPost('username'));
            $email = trim((string) $this->request->getPost('email'));
            $name = trim((string) $this->request->getPost('name'));
            $departmentId = $this->request->getPost('department_id') ?: null;
            $roleId = $this->request->getPost('role_id') ?: null;
            $password = (string) $this->request->getPost('password');
            $companyIds = $this->request->getPost('company_ids') ?? [];

            if ($username === '' || $password === '') {
                return redirect()->back()->withInput()->with('error', 'Username and password are required.');
            }

            $exists = $db->table('users')
                ->groupStart()
                    ->where('username', $username)
                    ->orWhere('email', $email)
                ->groupEnd()
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Username or email already exists.');
            }

            $userModel = new UserModel();
            $userCompanyModel = new UserCompanyModel();

            $userModel->insert([
                'username' => $username,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'email' => $email ?: null,
                'name' => $name ?: null,
                'department_id' => $departmentId,
                'role_id' => $roleId,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            $userId = $userModel->getInsertID();
            $now = date('Y-m-d H:i:s');

            foreach ($companyIds as $companyId) {
                $userCompanyModel->insert([
                    'user_id' => $userId,
                    'company_id' => (int) $companyId,
                    'date_created' => $now,
                    'date_updated' => $now,
                ]);
            }

            return redirect()->to('/admin/users')->with('success', 'User created.');
        }

        return view('admin/users/form', [
            'mode' => 'create',
            'user' => null,
            'roleOptions' => $roleOptions,
            'companyOptions' => $companyOptions,
            'departmentOptions' => $departmentOptions,
            'selectedCompanies' => [],
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $db = db_connect();
        $id = (int) $id;

        $user = $db->table('users')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $user) {
            return redirect()->to('/admin/users')->with('error', 'User not found.');
        }

        $roleOptions = dd_options('roles', 'id', 'name', [], ['name' => 'ASC']);
        $companyOptions = dd_options('companies', 'id', 'name', [], ['name' => 'ASC']);
        $departmentOptions = dd_options('departments', 'id', 'name', [], ['name' => 'ASC']);

        $selectedCompaniesRows = $db->table('user_companies')
            ->select('company_id')
            ->where('user_id', $id)
            ->get()
            ->getResultArray();

        $selectedCompanies = array_map(fn($r) => (int) $r['company_id'], $selectedCompaniesRows);

        if (strtolower($this->request->getMethod()) === 'post') {
            $username = trim((string) $this->request->getPost('username'));
            $email = trim((string) $this->request->getPost('email'));
            $name = trim((string) $this->request->getPost('name'));
            $departmentId = $this->request->getPost('department_id') ?: null;
            $roleId = $this->request->getPost('role_id') ?: null;
            $password = (string) $this->request->getPost('password');
            $companyIds = $this->request->getPost('company_ids') ?? [];

            $exists = $db->table('users')
                ->groupStart()
                    ->where('username', $username)
                    ->orWhere('email', $email)
                ->groupEnd()
                ->where('id !=', $id)
                ->where('date_deleted', null)
                ->get()
                ->getRowArray();

            if ($exists) {
                return redirect()->back()->withInput()->with('error', 'Username or email already exists.');
            }

            $payload = [
                'username' => $username,
                'email' => $email ?: null,
                'name' => $name ?: null,
                'department_id' => $departmentId,
                'role_id' => $roleId,
                'date_updated' => date('Y-m-d H:i:s'),
            ];

            if ($password !== '') {
                $payload['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            $userModel = new UserModel();
            $userCompanyModel = new UserCompanyModel();

            $userModel->update($id, $payload);

            $db->table('user_companies')->where('user_id', $id)->delete();

            $now = date('Y-m-d H:i:s');
            foreach ($companyIds as $companyId) {
                $userCompanyModel->insert([
                    'user_id' => $id,
                    'company_id' => (int) $companyId,
                    'date_created' => $now,
                    'date_updated' => $now,
                ]);
            }

            return redirect()->to('/admin/users')->with('success', 'User updated.');
        }

        return view('admin/users/form', [
            'mode' => 'edit',
            'user' => $user,
            'roleOptions' => $roleOptions,
            'companyOptions' => $companyOptions,
            'departmentOptions' => $departmentOptions,
            'selectedCompanies' => $selectedCompanies,
        ]);
    }

    public function delete($id)
    {
        $id = (int) $id;

        if ((int) session()->get('admin_id') === $id) {
            return redirect()->to('/admin/users')->with('error', 'You cannot delete your own account.');
        }

        db_connect()->table('users')
            ->where('id', $id)
            ->update([
                'date_deleted' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/users')->with('success', 'User deleted.');
    }
}