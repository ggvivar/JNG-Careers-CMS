<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobModel;
use App\Models\CompanyModel;
use App\Models\UnitModel;
use App\Models\GroupModel;

class JobController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('job j')
            ->select('
                j.id,
                j.name,
                j.job_code,
                j.description,
                j.requirement,
                j.company_id,
                j.unit_id,
                j.group_id,
                j.status_id,
                c.name as company_name,
                u.name as unit_name,
                g.name as group_name,
                s.name as status_name
            ')
            ->join('companies c', 'c.id = j.company_id', 'left')
            ->join('units u', 'u.id = j.unit_id', 'left')
            ->join('groups g', 'g.id = j.group_id', 'left')
            ->join('status s', 's.id = j.status_id', 'left')
            ->where('j.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('j.name', $q)
                ->orLike('j.job_code', $q)
                ->orLike('j.description', $q)
                ->orLike('j.requirement', $q)
                ->orLike('c.name', $q)
                ->orLike('u.name', $q)
                ->orLike('g.name', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $jobs = $builder
            ->orderBy('j.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/jobs/index', [
            'jobs' => $jobs,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');

        $statusOptions  = dd_statuses_by_feature('jobs');
        $companyOptions = $this->getCompanyOptions();

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'name'         => trim((string) $this->request->getPost('name')),
                'job_code'     => trim((string) $this->request->getPost('job_code')),
                'company_id'   => $this->request->getPost('company_id') ?: null,
                'unit_id'      => $this->request->getPost('unit_id') ?: null,
                'group_id'     => $this->request->getPost('group_id') ?: null,
                'description'  => trim((string) $this->request->getPost('description')),
                'requirement'  => trim((string) $this->request->getPost('requirement')),
                'status_id'    => $this->request->getPost('status_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ];

            if (empty($data['name']) || empty($data['job_code']) || empty($data['company_id']) || empty($data['unit_id']) || empty($data['group_id'])) {
                return redirect()->back()->withInput()->with('error', 'Please fill in all required fields.');
            }

            $model = new JobModel();

            if (! $model->insert($data)) {
                return redirect()->back()->withInput()->with('error', 'Failed to create job.');
            }

            return redirect()->to('/admin/jobs')->with('success', 'Job created.');
        }

        return view('admin/jobs/form', [
            'mode'           => 'create',
            'job'            => null,
            'statusOptions'  => $statusOptions,
            'companyOptions' => $companyOptions,
            'unitOptions'    => ['' => 'Select Unit'],
            'groupOptions'   => ['' => 'Select Group'],
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');

        $model = new JobModel();
        $job   = $model->where('date_deleted', null)->find((int) $id);

        if (! $job) {
            return redirect()->to('/admin/jobs')->with('error', 'Job not found.');
        }

        $statusOptions  = dd_statuses_by_feature('jobs');
        $companyOptions = $this->getCompanyOptions();
        $unitOptions    = $this->getUnitOptionsByCompanyId($job['company_id'] ?? null);
        $groupOptions   = $this->getGroupOptionsByUnitId($job['unit_id'] ?? null);

        if (strtolower($this->request->getMethod()) === 'post') {
            $data = [
                'name'         => trim((string) $this->request->getPost('name')),
                'job_code'     => trim((string) $this->request->getPost('job_code')),
                'company_id'   => $this->request->getPost('company_id') ?: null,
                'unit_id'      => $this->request->getPost('unit_id') ?: null,
                'group_id'     => $this->request->getPost('group_id') ?: null,
                'description'  => trim((string) $this->request->getPost('description')),
                'requirement'  => trim((string) $this->request->getPost('requirement')),
                'status_id'    => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ];

            if (empty($data['name']) || empty($data['job_code']) || empty($data['company_id']) || empty($data['unit_id']) || empty($data['group_id'])) {
                return redirect()->back()->withInput()->with('error', 'Please fill in all required fields.');
            }

            if (! $model->update((int) $id, $data)) {
                return redirect()->back()->withInput()->with('error', 'Failed to update job.');
            }

            return redirect()->to('/admin/jobs')->with('success', 'Job updated.');
        }

        return view('admin/jobs/form', [
            'mode'           => 'edit',
            'job'            => $job,
            'statusOptions'  => $statusOptions,
            'companyOptions' => $companyOptions,
            'unitOptions'    => $unitOptions,
            'groupOptions'   => $groupOptions,
        ]);
    }

    public function delete($id)
    {
        (new JobModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/jobs')->with('success', 'Job deleted.');
    }

    public function getUnitsByCompany()
    {
        $companyId = (int) ($this->request->getGet('company_id') ?? 0);

        $rows = (new UnitModel())
            ->where('company_id', $companyId)
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        $units = [];
        foreach ($rows as $row) {
            $units[] = [
                'value' => $row['id'],
                'label' => $row['name'],
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'units'   => $units,
        ]);
    }

    public function getGroupsByUnit()
    {
        $unitId = (int) ($this->request->getGet('unit_id') ?? 0);

        $rows = (new GroupModel())
            ->where('unit_id', $unitId)
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        $groups = [];
        foreach ($rows as $row) {
            $groups[] = [
                'value' => $row['id'],
                'label' => $row['name'],
            ];
        }

        return $this->response->setJSON([
            'success' => true,
            'groups'  => $groups,
        ]);
    }

    private function getCompanyOptions(): array
    {
        $rows = (new CompanyModel())
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        $options = ['' => 'Select Company'];
        foreach ($rows as $row) {
            $options[$row['id']] = $row['name'];
        }

        return $options;
    }

    private function getUnitOptionsByCompanyId($companyId): array
    {
        if (empty($companyId)) {
            return ['' => 'Select Unit'];
        }

        $rows = (new UnitModel())
            ->where('company_id', $companyId)
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        $options = ['' => 'Select Unit'];
        foreach ($rows as $row) {
            $options[$row['id']] = $row['name'];
        }

        return $options;
    }

    private function getGroupOptionsByUnitId($unitId): array
    {
        if (empty($unitId)) {
            return ['' => 'Select Group'];
        }

        $rows = (new GroupModel())
            ->where('unit_id', $unitId)
            ->where('date_deleted', null)
            ->orderBy('name', 'ASC')
            ->findAll();

        $options = ['' => 'Select Group'];
        foreach ($rows as $row) {
            $options[$row['id']] = $row['name'];
        }

        return $options;
    }
}