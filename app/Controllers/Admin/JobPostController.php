<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class JobPostController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('job_list jl')
            ->select('
                jl.*,
                j.name as job_name,
                s.name as status_name,
                c.name as company_name,
                d.name as department_name
            ')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = jl.status_id', 'left')
            ->join('companies c', 'c.id = jl.company_id', 'left')
            ->join('departments d', 'd.id = jl.department_id', 'left')
            ->where('jl.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('j.name', $q)
                ->orLike('c.name', $q)
                ->orLike('d.name', $q)
                ->orLike('jl.location', $q)
                ->orLike('jl.salary_range', $q)
                ->orLike('jl.experience_range', $q)
                ->orLike('jl.headcount_required', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $perPage = 10;
        $page = max(1, (int) ($this->request->getGet('page') ?? 1));
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $posts = $builder
            ->orderBy('jl.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return view('admin/job_post/index', [
            'posts' => $posts,
            'searchQuery' => $q,
            'paginationLinks' => service('pager')->makeLinks($page, $perPage, $total),
        ]);
    }

    public function create()
    {
        helper('dropdown');

        $jobOptions = dd_options('job', 'id', 'name', [], ['name' => 'ASC']);
        $statusOptions = dd_statuses_by_feature('job-posts');
        $companyOptions = dd_options('companies', 'id', 'name', [], ['name' => 'ASC']);
        $departmentOptions = dd_options('departments', 'id', 'name', [], ['name' => 'ASC']);

        if (strtolower($this->request->getMethod()) === 'post') {
            db_connect()->table('job_list')->insert([
                'job_id' => $this->request->getPost('job_id') ?: null,
                'company_id' => $this->request->getPost('company_id') ?: null,
                'department_id' => $this->request->getPost('department_id') ?: null,
                'location' => trim((string) $this->request->getPost('location')) ?: null,
                'salary_range' => trim((string) $this->request->getPost('salary_range')) ?: null,
                'experience_range' => trim((string) $this->request->getPost('experience_range')) ?: null,
                'rank_hiring' => $this->request->getPost('rank_hiring') ?: null,
                'headcount_required' => $this->request->getPost('headcount_required') !== ''
                    ? (int) $this->request->getPost('headcount_required')
                    : null,
                'job_posted_date' => $this->normalizeDatetime($this->request->getPost('job_posted_date')),
                'status_id' => $this->request->getPost('status_id') ?: null,
                'valid_from' => $this->normalizeDatetime($this->request->getPost('valid_from')),
                'valid_to' => $this->normalizeDatetime($this->request->getPost('valid_to')),
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/job-posts')->with('success', 'Job post created.');
        }

        return view('admin/job_post/form', [
            'mode' => 'create',
            'post' => null,
            'jobOptions' => $jobOptions,
            'statusOptions' => $statusOptions,
            'companyOptions' => $companyOptions,
            'departmentOptions' => $departmentOptions,
            'levelOptions' => dd_common_defaults('Level'),
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $id = (int) $id;

        $post = db_connect()->table('job_list')
            ->where('id', $id)
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $post) {
            return redirect()->to('/admin/job-posts')->with('error', 'Job post not found.');
        }

        $jobOptions = dd_options('job', 'id', 'name', [], ['name' => 'ASC']);
        $statusOptions = dd_statuses_by_feature('job-posts');
        $companyOptions = dd_options('companies', 'id', 'name', [], ['name' => 'ASC']);
        $departmentOptions = dd_options('departments', 'id', 'name', [], ['name' => 'ASC']);

        if (strtolower($this->request->getMethod()) === 'post') {
            db_connect()->table('job_list')
                ->where('id', $id)
                ->update([
                    'job_id' => $this->request->getPost('job_id') ?: null,
                    'company_id' => $this->request->getPost('company_id') ?: null,
                    'department_id' => $this->request->getPost('department_id') ?: null,
                    'location' => trim((string) $this->request->getPost('location')) ?: null,
                    'salary_range' => trim((string) $this->request->getPost('salary_range')) ?: null,
                    'experience_range' => trim((string) $this->request->getPost('experience_range')) ?: null,
                    'rank_hiring' => $this->request->getPost('rank_hiring') ?: null,
                    'headcount_required' => $this->request->getPost('headcount_required') !== ''
                        ? (int) $this->request->getPost('headcount_required')
                        : null,
                    'job_posted_date' => $this->normalizeDatetime($this->request->getPost('job_posted_date')),
                    'status_id' => $this->request->getPost('status_id') ?: null,
                    'valid_from' => $this->normalizeDatetime($this->request->getPost('valid_from')),
                    'valid_to' => $this->normalizeDatetime($this->request->getPost('valid_to')),
                    'date_updated' => date('Y-m-d H:i:s'),
                ]);

            return redirect()->to('/admin/job-posts')->with('success', 'Job post updated.');
        }

        return view('admin/job_post/form', [
            'mode' => 'edit',
            'post' => $post,
            'jobOptions' => $jobOptions,
            'statusOptions' => $statusOptions,
            'companyOptions' => $companyOptions,
            'departmentOptions' => $departmentOptions,
        ]);
    }

    public function delete($id)
    {
        db_connect()->table('job_list')
            ->where('id', (int) $id)
            ->update([
                'date_deleted' => date('Y-m-d H:i:s'),
            ]);

        return redirect()->to('/admin/job-posts')->with('success', 'Job post deleted.');
    }

    private function normalizeDatetime($value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        $ts = strtotime($value);
        return $ts ? date('Y-m-d H:i:s', $ts) : null;
    }

    public function JobList()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('job_list jl')
            ->select('
                jl.id,
                j.name as job_name,
                s.name as status_name,
                c.name as company_name,
                d.name as department_name,
                jl.location,
                jl.salary_range,
                jl.experience_range,
                jl.headcount_required,
                jl.valid_from,
                jl.valid_to
            ')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('status s', 's.id = jl.status_id', 'left')
            ->join('companies c', 'c.id = jl.company_id', 'left')
            ->join('departments d', 'd.id = jl.department_id', 'left')
            ->where('jl.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('j.name', $q)
                ->orLike('c.name', $q)
                ->orLike('d.name', $q)
                ->orLike('jl.location', $q)
                ->orLike('jl.salary_range', $q)
                ->orLike('jl.experience_range', $q)
                ->orLike('jl.headcount_required', $q)
                ->orLike('s.name', $q)
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('jl.id', 'DESC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'rows' => $rows,
        ]);
    }
}