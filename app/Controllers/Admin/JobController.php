<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\JobModel;

class JobController extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $q = trim((string) $this->request->getGet('q'));

        $builder = $db->table('job j')
            ->select('j.id, j.name, j.description, j.requirement, j.status_id, s.name as status_name')
            ->join('status s', 's.id = j.status_id', 'left')
            ->where('j.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('j.name', $q)
                ->orLike('j.description', $q)
                ->orLike('j.requirement', $q)
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
        $statusOptions = dd_statuses_by_feature('jobs');

        if (strtolower($this->request->getMethod()) === 'post') {
            (new JobModel())->insert([
                'name' => trim((string) $this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'requirement' => $this->request->getPost('requirement'),
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_created' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/jobs')->with('success', 'Job created.');
        }

        return view('admin/jobs/form', [
            'mode' => 'create',
            'job' => null,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function edit($id)
    {
        helper('dropdown');
        $statusOptions = dd_statuses_by_feature('jobs');

        $model = new JobModel();
        $job = $model->where('date_deleted', null)->find((int) $id);

        if (! $job) {
            return redirect()->to('/admin/jobs')->with('error', 'Job not found.');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $model->update((int) $id, [
                'name' => trim((string) $this->request->getPost('name')),
                'description' => $this->request->getPost('description'),
                'requirement' => $this->request->getPost('requirement'),
                'status_id' => $this->request->getPost('status_id') ?: null,
                'date_updated' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->to('/admin/jobs')->with('success', 'Job updated.');
        }

        return view('admin/jobs/form', [
            'mode' => 'edit',
            'job' => $job,
            'statusOptions' => $statusOptions,
        ]);
    }

    public function delete($id)
    {
        (new JobModel())->update((int) $id, [
            'date_deleted' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to('/admin/jobs')->with('success', 'Job deleted.');
    }
}