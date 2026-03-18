<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Applications',
    'subtitle' => 'Manage job applications',
    'featureCode' => 'applications',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search applications...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $applications ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        [
            'key' => 'firstname',
            'label' => 'Applicant',
            'formatter' => function ($row, $value) {
                $name = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                $applicantId = (int) ($row['applicant_id'] ?? 0);
                if ($applicantId > 0) {
                    return '<a href="' . site_url('admin/applicants/' . $applicantId) . '" class="fw-semibold text-decoration-none">' . esc($name ?: 'View Applicant') . '</a>';
                }
                return '<span class="fw-semibold">' . esc($name) . '</span>';
            },
        ],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'job_name', 'label' => 'Job'],
        ['key' => 'status_name', 'label' => 'Status'],
        ['key' => 'source', 'label' => 'Source'],
    ],
    'actions' => [
        ['label' => 'View', 'permission' => 'can_view', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/applications/'],
    ],
]) ?>

<?= $this->endSection() ?>