<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'My Processing',
    'subtitle' => 'Applications assigned to me',
    'featureCode' => 'applications',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search my applications...',
    'paginationLinks' => $paginationLinks ?? '',
    'sort' => $currentSort ?? '',
    'dir' => $currentDir ?? 'desc',
    'currentFilters' => [
        'status_id' => $currentStatusId ?? '',
        'overdue' => !empty($currentOverdue) ? '1' : '',
    ],
    'filters' => [
        [
            'name' => 'status_id',
            'label' => 'Status',
            'type' => 'select',
            'options' => $statusOptions ?? [],
            'placeholder' => 'All Statuses',
            'class' => 'col-12 col-md-3 col-lg-2',
        ],
        [
            'name' => 'overdue',
            'label' => 'Overdue',
            'type' => 'checkbox',
            'checkbox_label' => 'Overdue Only',
            'class' => 'col-12 col-md-2 col-lg-2',
        ],
    ],
    'rows' => $applications ?? [],
    'columns' => [
        [
            'key' => 'id',
            'label' => 'ID',
            'sortable' => true,
            'sort_key' => 'id',
        ],
        [
            'key' => 'firstname',
            'label' => 'Applicant',
            'sortable' => true,
            'sort_key' => 'applicant',
            'formatter' => function ($row, $value) {
                $name = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                return '<a href="' . site_url('admin/applications/' . (int) $row['id']) . '" class="fw-semibold text-decoration-none">' . esc($name) . '</a>';
            },
        ],
        [
            'key' => 'email',
            'label' => 'Email',
            'sortable' => true,
            'sort_key' => 'email',
        ],
        [
            'key' => 'job_name',
            'label' => 'Job',
            'sortable' => true,
            'sort_key' => 'job',
        ],
        [
            'key' => 'status_name',
            'label' => 'Status',
            'sortable' => true,
            'sort_key' => 'status',
        ],
        [
            'key' => 'applied_at',
            'label' => 'Applied',
            'sortable' => true,
            'sort_key' => 'applied_at',
        ],
        [
            'key' => 'due_at',
            'label' => 'Due',
            'sortable' => true,
            'sort_key' => 'due_at',
            'formatter' => static function ($row, $value) {
                if (empty($value)) {
                    return '<span class="text-muted">-</span>';
                }

                $isOverdue = strtotime($value) < time();

                if ($isOverdue) {
                    return '<span class="badge bg-danger">Overdue</span><div class="small text-danger mt-1">' . esc($value) . '</div>';
                }

                // return '<span class="badge bg-success">On Time</span><div class="small text-muted mt-1">' . esc($value) . '</div>';
                return '<div class="small text-muted mt-1">' . esc($value) . '</div>';
            },
        ],
        ['key' => 'source', 'label' => 'Source'],
    ],
    'actions' => [
        [
            'label' => 'View',
            'permission' => 'can_view',
            'class' => 'btn btn-sm btn-outline-primary',
            'icon' => 'bi-eye',
            'method' => 'get',
            'url' => 'admin/applications/',
        ],
    ],
]) ?>

<?= $this->endSection() ?>