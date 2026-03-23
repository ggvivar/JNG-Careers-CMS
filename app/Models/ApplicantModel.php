<?php

namespace App\Models;

use CodeIgniter\Model;

class ApplicantModel extends Model
{
    protected $table = 'applicants';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'firstname',
        'middlename',
        'lastname',
        'suffix',
        'email',
        'phone',
        'birthdate',
        'gender',
        'civil_status',
        'nationality',
        'address',
        'city',
        'province',
        'zip_code',
        'date_applied',
        'date_created',
        'date_updated',
        'date_deleted',
    ];

    public function getPaginatedApplicants(?string $q = '', int $perPage = 10, int $page = 1): array
    {
        $q = trim((string) $q);
        $offset = ($page - 1) * $perPage;

        $builder = $this->db->table($this->table . ' a')
            ->select('a.*')
            ->where('a.date_deleted', null);

        if ($q !== '') {
            $builder->groupStart()
                ->like('a.firstname', $q)
                ->orLike('a.lastname', $q)
                ->orLike('a.email', $q)
                ->orLike('a.phone', $q)
                ->groupEnd();
        }

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults();

        $rows = $builder->orderBy('a.id', 'DESC')
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return [
            'rows'  => $rows,
            'total' => $total,
        ];
    }

    public function getApplicantById(int $id): ?array
    {
        return $this->where('id', $id)
            ->where('date_deleted', null)
            ->first();
    }
}