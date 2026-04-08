<?php

namespace App\Models;

use CodeIgniter\Model;

class CommonModel extends Model
{
    protected array $allowedSorts = [];

    protected function applySearch($builder, ?string $q, array $columns)
    {
        $q = trim((string) $q);

        if ($q === '' || empty($columns)) {
            return $builder;
        }

        $builder->groupStart();
        foreach ($columns as $i => $col) {
            $i === 0
                ? $builder->like($col, $q)
                : $builder->orLike($col, $q);
        }
        $builder->groupEnd();

        return $builder;
    }

    protected function applySorting($builder, ?string $sortBy, ?string $direction)
    {
        $direction = strtolower((string) $direction);
        $direction = in_array($direction, ['asc', 'desc'], true) ? $direction : 'asc';

        $column = $this->allowedSorts[$sortBy] ?? array_values($this->allowedSorts)[0] ?? $this->primaryKey;

        return $builder->orderBy($column, $direction);
    }

    protected function paginateBuilder($builder, int $page, int $perPage)
    {
        $offset = ($page - 1) * $perPage;

        $countBuilder = clone $builder;
        $total = $countBuilder->countAllResults(false);

        $rows = $builder
            ->limit($perPage, $offset)
            ->get()
            ->getResultArray();

        return [$rows, $total];
    }
}