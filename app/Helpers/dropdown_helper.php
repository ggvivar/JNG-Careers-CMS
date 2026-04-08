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

        $builder = $db->table($table)
            ->select("{$idField}, {$labelField}");

        if ($softDelete && $db->fieldExists('date_deleted', $table)) {
            $builder->where('date_deleted', null);
        }

        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        $rows = $builder->get()->getResultArray();

        $options = [];
        foreach ($rows as $row) {
            $options[$row[$idField]] = $row[$labelField];
        }

        return $options;
    }
}

if (! function_exists('dd_feature_id')) {
    function dd_feature_id(string $featureCode): ?int
    {
        $row = db_connect()
            ->table('features')
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

        $rows = db_connect()
            ->table('status s')
            ->select('s.id, s.name')
            ->join('status_features sf', 'sf.status_id = s.id', 'inner')
            ->where('sf.feature_id', $featureId)
            ->where('s.date_deleted', null)
            ->orderBy('s.name', 'ASC')
            ->get()
            ->getResultArray();

        $options = [];
        foreach ($rows as $row) {
            $options[(int) $row['id']] = $row['name'];
        }

        return $options;
    }
}

if (! function_exists('dd_status_id')) {
    function dd_status_id(string $statusName, string $featureCode): ?int
    {
        $featureId = dd_feature_id($featureCode);

        if (! $featureId) {
            return null;
        }

        $row = db_connect()
            ->table('status s')
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

if (! function_exists('dd_next_statuses')) {
    function dd_next_statuses(string $featureCode, int $fromStatusId): array
    {
        $featureId = dd_feature_id($featureCode);

        if (! $featureId || $fromStatusId <= 0) {
            return [];
        }

        $rows = db_connect()
            ->table('workflow_transitions wt')
            ->select('
                s.id,
                s.name,
                wt.grace_period,
                wt.require_remarks,
                wt.sort_order,
                wt.email_template_key,
                wt.send_email
            ')
            ->join('status s', 's.id = wt.status_id_to', 'inner')
            ->where('wt.feature_id', $featureId)
            ->where('wt.status_id_from', $fromStatusId)
            ->where('wt.status_id', 1)
            ->where('wt.date_deleted', null)
            ->where('s.date_deleted', null)
            ->orderBy('wt.sort_order', 'ASC')
            ->orderBy('s.name', 'ASC')
            ->get()
            ->getResultArray();

        $options = [];

        foreach ($rows as $row) {
            $statusId = (int) $row['id'];

            $options[$statusId] = [
                'id' => $statusId,
                'name' => $row['name'],
                'grace_period' => $row['grace_period'] !== null ? (int) $row['grace_period'] : null,
                'require_remarks' => (int) $row['require_remarks'],
                'sort_order' => (int) $row['sort_order'],
                'email_template_key' => $row['email_template_key'] ?? null,
                'send_email' => (int) ($row['send_email'] ?? 0),
            ];
        }

        return $options;
    }
}

if (! function_exists('dd_workflow_transition')) {
    function dd_workflow_transition(string $featureCode, ?int $fromStatusId, int $toStatusId): ?array
    {
        $featureId = dd_feature_id($featureCode);

        if (! $featureId || $toStatusId <= 0) {
            return null;
        }

        $builder = db_connect()
            ->table('workflow_transitions')
            ->select('
                id,
                feature_id,
                status_id_from,
                status_id_to,
                grace_period,
                require_remarks,
                sort_order,
                email_template_key,
                send_email
            ')
            ->where('feature_id', $featureId)
            ->where('status_id_to', $toStatusId)
            ->where('status_id', 1)
            ->where('date_deleted', null);

        if ($fromStatusId === null || $fromStatusId <= 0) {
            $builder->where('status_id_from IS NULL', null, false);
        } else {
            $builder->where('status_id_from', $fromStatusId);
        }

        $row = $builder->get()->getRowArray();

        return $row ?: null;
    }
}

if (! function_exists('dd_workflow_due_at')) {
    function dd_workflow_due_at(?int $days): ?string
    {
        if ($days === null || $days <= 0) {
            return null;
        }

        return date('Y-m-d H:i:s', strtotime('+' . $days . ' days'));
    }
}

if (! function_exists('dd_common_defaults_adv')) {
    function dd_common_defaults_adv(
        array $where = [],
        string $idField = 'value',
        string $labelField = 'value',
        array $orderBy = ['value' => 'ASC']
    ): array {
        $db = db_connect();

        $builder = $db->table('common_defaults')
            ->select("{$idField}, {$labelField}")
            ->where('date_deleted', null);

        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        $rows = $builder->get()->getResultArray();

        $options = [];
        foreach ($rows as $row) {
            $options[$row[$idField]] = $row[$labelField];
        }

        return $options;
    }
}

if (! function_exists('dd_common_default_groups')) {
    function dd_common_default_groups(
        array $where = [],
        array $orderBy = ['key1' => 'ASC']
    ): array {
        $db = db_connect();

        $builder = $db->table('common_defaults')
            ->select('key1')
            ->where('date_deleted', null)
            ->where('key1 IS NOT NULL', null, false)
            ->where('key1 !=', '')
            ->groupBy('key1');

        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        $rows = $builder->get()->getResultArray();

        $options = [];
        foreach ($rows as $row) {
            $options[$row['key1']] = $row['key1'];
        }

        return $options;
    }
}

if (! function_exists('dd_common_defaults')) {
    function dd_common_defaults(
        string $group,
        string $valueField = 'value',
        string $labelField = 'value',
        array $where = [],
        array $orderBy = ['value' => 'ASC']
    ): array {
        $db = db_connect();

        $builder = $db->table('common_defaults')
            ->select("{$valueField}, {$labelField}")
            ->where('date_deleted', null)
            ->where('key1', $group);

        foreach ($where as $field => $value) {
            $builder->where($field, $value);
        }

        foreach ($orderBy as $field => $direction) {
            $builder->orderBy($field, $direction);
        }

        $rows = $builder->get()->getResultArray();

        $options = [];
        foreach ($rows as $row) {
            $options[$row[$valueField]] = $row[$labelField];
        }

        return $options;
    }
}

if (! function_exists('dd_common_default_value_exists')) {
    function dd_common_default_value_exists(string $group, string $value): bool
    {
        return db_connect()
            ->table('common_defaults')
            ->where('date_deleted', null)
            ->where('key1', $group)
            ->where('value', $value)
            ->countAllResults() > 0;
    }
}