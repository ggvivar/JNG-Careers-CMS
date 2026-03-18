<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CompanyModel;

class CompanyController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('companies c')
            ->select('c.*, s.name as status_name')
            ->join('status s', 's.id = c.status_id', 'left')
            ->where('c.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('c.name', $q)
                ->orLike('c.code', $q)
                ->orLike('c.address', $q)
                ->orLike('c.contact_no', $q)
                ->orLike('c.email', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $companies = $builder
            ->orderBy('c.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/companies/index', [
            'companies' => $companies,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('companies');

        if (strtolower($this->request->getMethod()) === 'post') {
            (new CompanyModel())->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')) ?: null,
                'address' => trim((string) $this->request->getPost('address')) ?: null,
                'contact_no' => trim((string) $this->request->getPost('contact_no')) ?: null,
                'email' => trim((string) $this->request->getPost('email')) ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/companies')->with('success', 'Company created.');
        }

        return view('admin/companies/form', [
            'mode' => 'create',
            'company' => null,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('companies');

        $model = new CompanyModel();
        $company = $model->where('date_deleted', null)->find((int) $id);

        if (! $company) {
            return redirect()->to('/admin/companies')->with('error', 'Company not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'name' => trim((string) $this->request->getPost('name')),
                'code' => trim((string) $this->request->getPost('code')) ?: null,
                'address' => trim((string) $this->request->getPost('address')) ?: null,
                'contact_no' => trim((string) $this->request->getPost('contact_no')) ?: null,
                'email' => trim((string) $this->request->getPost('email')) ?: null,
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/companies')->with('success', 'Company updated.');
        }

        return view('admin/companies/form', [
            'mode' => 'edit',
            'company' => $company,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function delete($id)
    {
        (new CompanyModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/companies')->with('success', 'Company deleted.');
    }
}