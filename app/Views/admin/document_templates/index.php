<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Document Templates',
    'subtitle' => 'Manage attached template files with reusable placeholders',
    'featureCode' => 'document-templates',
    'createUrl' => 'admin/document-templates/create',
    'createLabel' => '+ Create Document Template',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search document templates...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $templates ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'style' => 'width:80px;'],
        [
            'key' => 'name',
            'label' => 'Name',
            'formatter' => function ($row, $value) {
                return '<span class="fw-semibold">' . esc($value) . '</span>';
            },
        ],
        ['key' => 'template_key', 'label' => 'Key'],
        [
            'key' => 'template_type',
            'label' => 'Type',
            'formatter' => function ($row, $value) {
                $value = strtolower((string) $value);
                $badge = $value === 'pdf' ? 'text-bg-danger' : 'text-bg-success';

                return '<span class="badge ' . $badge . '">' . esc(strtoupper($value)) . '</span>';
            },
        ],
        [
            'key' => 'source_table',
            'label' => 'Source',
            'formatter' => function ($row, $value) {
                if ((string) $value === '') {
                    return '<span class="text-muted">—</span>';
                }

                return esc(ucwords(str_replace('_', ' ', (string) $value)));
            },
        ],
        [
            'key' => 'file_name_pattern',
            'label' => 'File Name Pattern',
            'formatter' => function ($row, $value) {
                $value = trim((string) $value);
                return $value !== '' ? esc($value) : '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'available_vars',
            'label' => 'Variables',
            'formatter' => function ($row, $value) {
                $vars = array_filter(array_map('trim', explode(',', (string) $value)));

                if (empty($vars)) {
                    return '<span class="text-muted">—</span>';
                }

                $preview = array_slice($vars, 0, 3);
                $html = '';

                foreach ($preview as $var) {
                    $html .= '<span class="badge text-bg-light border me-1 mb-1">' . esc($var) . '</span>';
                }

                if (count($vars) > 3) {
                    $html .= '<span class="text-muted small">+' . (count($vars) - 3) . ' more</span>';
                }

                return $html;
            },
        ],
        [
            'key' => 'source_file_path',
            'label' => 'Template File',
            'formatter' => function ($row, $value) {
                if (empty($value)) {
                    return '<span class="text-muted">—</span>';
                }

                return '<a href="' . site_url($value) . '" target="_blank">Open File</a>';
            },
        ],
        [
            'key' => 'description',
            'label' => 'Description',
            'formatter' => function ($row, $value) {
                $text = trim(strip_tags((string) $value));

                if ($text === '') {
                    return '<span class="text-muted">—</span>';
                }

                if (mb_strlen($text) > 70) {
                    $text = mb_substr($text, 0, 70) . '...';
                }

                return esc($text);
            },
        ],
        [
            'key' => 'status_name',
            'label' => 'Status',
            'formatter' => function ($row, $value) {
                $value = trim((string) $value);
                return $value !== '' ? esc($value) : '<span class="text-muted">—</span>';
            },
        ],
    ],
    'actions' => [
        [
            'label' => 'Edit',
            'permission' => 'can_edit',
            'class' => 'btn btn-outline-primary',
            'method' => 'get',
            'icon' => 'bi-pencil',
            'url' => 'admin/document-templates/edit/',
        ],
        [
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-outline-danger',
            'method' => 'post',
            'icon' => 'bi-trash',
            'url' => 'admin/document-templates/delete/',
            'confirm' => 'Delete this template?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>