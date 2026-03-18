<?php

if (! function_exists('dd_options')) {
    function dd_options(
        string $table,
        string $idField = 'id',
        string $labelField = 'name',
        array $where = [],
        array $orderBy = ['id' => 'ASC'],
        bool $softDelete = true
    ): array {
        $db = db_connect();
        $builder = $db->table($table)->select("{$idField}, {$labelField}");

        if ($softDelete && $db->fieldExists('date_deleted', $table)) {
            $builder->where('date_deleted', null);
        }

        foreach ($where as $k => $v) {
            $builder->where($k, $v);
        }

        foreach ($orderBy as $k => $dir) {
            $builder->orderBy($k, $dir);
        }

        $rows = $builder->get()->getResultArray();
        $out = [];

        foreach ($rows as $r) {
            $out[$r[$idField]] = $r[$labelField];
        }

        return $out;
    }
}

if (! function_exists('dd_feature_id')) {
    function dd_feature_id(string $featureCode): ?int
    {
        $row = db_connect()->table('features')
            ->select('id')
            ->where('LOWER(TRIM(code))', strtolower(trim($featureCode)))
            ->where('date_deleted', null)
            ->get()
            ->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}

if (! function_exists('dd_statuses_by_feature')) {
    function dd_statuses_by_feature(string $featureCode): array
    {
        $featureId = dd_feature_id($featureCode);
        if (! $featureId) {
            return [];
        }

        $rows = db_connect()->table('status s')
            ->select('s.id, s.name')
            ->join('status_features sf', 'sf.status_id = s.id', 'inner')
            ->where('sf.feature_id', $featureId)
            ->where('s.date_deleted', null)
            ->orderBy('s.name', 'ASC')
            ->get()
            ->getResultArray();

        $out = [];
        foreach ($rows as $r) {
            $out[$r['id']] = $r['name'];
        }

        return $out;
    }
}

if (! function_exists('dd_status_id')) {
    function dd_status_id(string $statusName, string $featureCode): ?int
    {
        $featureId = dd_feature_id($featureCode);
        if (! $featureId) {
            return null;
        }

        $row = db_connect()->table('status s')
            ->select('s.id')
            ->join('status_features sf', 'sf.status_id = s.id', 'inner')
            ->where('sf.feature_id', $featureId)
            ->where('LOWER(TRIM(s.name))', strtolower(trim($statusName)))
            ->where('s.date_deleted', null)
            ->get()
            ->getRowArray();

        return $row ? (int) $row['id'] : null;
    }
}