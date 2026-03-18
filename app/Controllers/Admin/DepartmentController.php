<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DepartmentModel;

class DepartmentController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('departments d')
            ->select('d.*, c.name as company_name, s.name as status_name')
            ->join('companies c', 'c.id = d.company_id', 'left')
            // ->join('users u', 'u.id = d.manager_user_id', 'left')
            ->join('status s', 's.id = d.status_id', 'left')
            ->where('d.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('d.name', $q)
                ->orLike('d.code', $q)
                ->orLike('c.name', $q)
                ->orLike('u.name', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $departments = $builder
            ->orderBy('d.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();
        // var_dump($departments);
        // die();
        return view('admin/departments/index', [
            'departments' => $departments,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');
        $companyOptions = dd_options('companies', 'id', 'name', [], ['name' => 'ASC']);
        $statusOptions = dd_statuses_by_feature('departments');
        $managerOptions = dd_options('users', 'id', 'name', [], ['name' => 'ASC']);

        if (strtolower($this->request->getMethod()) === 'post') {
            (new DepartmentModel())->insert([
                'company_id' => $this->request->getPost('company_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')) ?: null,
                'manager_user_id' => $this->request->getPost('manager_user_id') ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/departments')->with('success', 'Department created.');
        }

        return view('admin/departments/form', [
            'mode' => 'create',
            'department' => null,
            'companyOptions' => $companyOptions,
            'statusOptions' => $statusOptions,
            'managerOptions' => $managerOptions,
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $companyOptions = dd_options('companies', 'id', 'name', [], ['name' => 'ASC']);
        $statusOptions = dd_statuses_by_feature('departments');
        $managerOptions = dd_options('users', 'id', 'name', [], ['name' => 'ASC']);

        $model = new DepartmentModel();
        $department = $model->where('date_deleted', null)->find((int) $id);

        if (! $department) {
            return redirect()->to('/admin/departments')->with('error', 'Department not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'company_id' => $this->request->getPost('company_id') ?: null,
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')) ?: null,
                'manager_user_id' => $this->request->getPost('manager_user_id') ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/departments')->with('success', 'Department updated.');
        }

        return view('admin/departments/form', [
            'mode' => 'edit',
            'department' => $department,
            'companyOptions' => $companyOptions,
            'statusOptions' => $statusOptions,
            'managerOptions' => $managerOptions,
        ]);
    }

    public function delete($id)
    {
        (new DepartmentModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/departments')->with('success', 'Department deleted.');
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