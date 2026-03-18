<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class JobPortalController extends BaseController
{
    public function jobs()
    {
        $db = db_connect();
        $q = trim((string) ($this->request->getGet('q') ?? ''));

        $builder = $db->table('job_list jl')
            ->select('
                jl.id,
                jl.location,
                jl.salary_range,
                jl.experience_range,
                jl.rank_hiring,
                jl.job_posted_date,
                jl.valid_from,
                jl.valid_to,
                j.name as job_name,
                j.description,
                j.requirement,
                c.name as company_name,
                d.name as department_name,
                s.name as status_name
            ')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('companies c', 'c.id = jl.company_id', 'left')
            ->join('departments d', 'd.id = jl.department_id', 'left')
            ->join('status s', 's.id = jl.status_id', 'left')
            ->where('jl.date_deleted', null)
            ->groupStart()
                ->where('s.name', 'Active')
                ->orWhere('s.name', 'Published')
            ->groupEnd();

        if ($q !== '') {
            $builder->groupStart()
                ->like('j.name', $q)
                ->orLike('j.description', $q)
                ->orLike('c.name', $q)
                ->orLike('d.name', $q)
                ->orLike('jl.location', $q)
                ->groupEnd();
        }

        $rows = $builder
            ->orderBy('jl.rank_hiring', 'ASC')
            ->orderBy('jl.id', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => true,
            'count' => count($rows),
            'data' => $rows,
        ]);
    }

    public function jobDetail($id)
    {
        $db = db_connect();

        $row = $db->table('job_list jl')
            ->select('
                jl.*,
                j.name as job_name,
                j.description,
                j.requirement,
                c.name as company_name,
                d.name as department_name,
                s.name as status_name
            ')
            ->join('job j', 'j.id = jl.job_id', 'left')
            ->join('companies c', 'c.id = jl.company_id', 'left')
            ->join('departments d', 'd.id = jl.department_id', 'left')
            ->join('status s', 's.id = jl.status_id', 'left')
            ->where('jl.id', (int) $id)
            ->where('jl.date_deleted', null)
            ->get()
            ->getRowArray();

        if (! $row) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => false,
                'message' => 'Job post not found.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data' => $row,
        ]);
    }
}