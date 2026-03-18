<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Roles',
    'subtitle' => 'Manage roles and permissions',
    'featureCode' => 'roles',
    'createUrl' => 'admin/roles/create',
    'createLabel' => '+ Create Role',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search roles...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $roles ?? [],
    'columns' => [
        [
            'key' => 'name',
            'label' => 'Role',
            'formatter' => function ($row, $value) {
                return '
                    <div class="d-flex flex-column">
                        <span class="fw-semibold text-dark">' . esc($value) . '</span>
                        <small class="text-muted">ID: ' . esc($row['id'] ?? '-') . '</small>
                    </div>
                ';
            },
        ],
        [
            'key' => 'status_name',
            'label' => 'Status',
            'formatter' => function ($row, $value) {
                $status = strtolower((string) $value);
                $class = 'text-bg-light border text-dark';

                if ($status === 'active') {
                    $class = 'text-bg-success';
                } elseif ($status === 'inactive') {
                    $class = 'text-bg-secondary';
                }

                return $value
                    ? '<span class="badge rounded-pill ' . $class . '">' . esc($value) . '</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'features_list',
            'label' => 'Features',
            'formatter' => function ($row, $value) {
                if (empty($value)) {
                    return '<span class="text-muted">No features assigned</span>';
                }

                return '<span class="text-muted">' . esc($value) . '</span>';
            },
        ],
    ],
    'actions' => [
        [
            'label' => 'Features',
            'icon' => 'bi-shield-check',
            'permission' => 'can_edit',
            'class' => 'btn btn-sm btn-outline-secondary',
            'method' => 'get',
            'url_builder' => fn($row) => 'admin/roles/' . $row['id'] . '/features',
        ],
        [
            'label' => 'Users',
            'icon' => 'bi-people',
            'permission' => 'can_edit',
            'class' => 'btn btn-sm btn-outline-info',
            'method' => 'get',
            'url_builder' => fn($row) => 'admin/roles/' . $row['id'] . '/users',
        ],
        [
            'label' => 'Edit',
            'icon' => 'bi-pencil-square',
            'permission' => 'can_edit',
            'class' => 'btn btn-sm btn-outline-primary',
            'method' => 'get',
            'url' => 'admin/roles/edit/',
        ],
        [
            'label' => 'Delete',
            'icon' => 'bi-trash',
            'permission' => 'can_delete',
            'class' => 'btn btn-sm btn-outline-danger',
            'method' => 'post',
            'url' => 'admin/roles/delete/',
            'confirm' => 'Delete this role?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>