<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Applicants',
    'subtitle' => 'Manage applicants',
    'featureCode' => 'applicants',
    'createUrl' => 'admin/applicants/create',
    'createLabel' => 'Create Applicants',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search applicants...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $applicants ?? [],
    'columns' => [
        [
            'key' => 'firstname',
            'label' => 'Applicant',
            'formatter' => function ($row, $value) {
                $fullName = trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''));
                $email = $row['email'] ?? '';

                return '
                    <div class="d-flex flex-column">
                        <span class="fw-semibold text-dark">' . esc($fullName !== '' ? $fullName : '-') . '</span>
                        ' . (!empty($email) ? '<small class="text-muted">' . esc($email) . '</small>' : '') . '
                    </div>
                ';
            },
        ],
        [
            'key' => 'phone',
            'label' => 'Phone',
            'formatter' => function ($row, $value) {
                return !empty($value)
                    ? '<span class="text-dark">' . esc($value) . '</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'city',
            'label' => 'Location',
            'formatter' => function ($row, $value) {
                $city = $row['city'] ?? '';
                $province = $row['province'] ?? '';

                if ($city && $province) {
                    return esc($city . ', ' . $province);
                }

                if ($city) {
                    return esc($city);
                }

                if ($province) {
                    return esc($province);
                }

                return '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'resume',
            'label' => 'Resume',
            'formatter' => function ($row, $value) {
                return !empty($value)
                    ? '<span class="badge rounded-pill text-bg-light border">Available</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
    ],
    'actions' => [
        [
            'label' => 'View',
            'permission' => 'can_view',
            'class' => 'btn btn-sm btn-outline-primary',
            'method' => 'get',
            'url' => 'admin/applicants/',
            'icon' => 'bi-eye',
        ],
    ],
]) ?>

<?= $this->endSection() ?>