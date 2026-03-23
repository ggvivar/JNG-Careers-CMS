<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Message Templates',
    'subtitle' => 'Manage reusable email and sms templates',
    'featureCode' => 'message-templates',
    'createUrl' => 'admin/message-templates/create',
    'createLabel' => '+ Create Message Template',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search message templates...',
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
            'key' => 'channel',
            'label' => 'Channel',
            'formatter' => function ($row, $value) {
                $value = strtolower((string) $value);
                $badge = $value === 'sms' ? 'text-bg-warning' : 'text-bg-primary';

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
            'key' => 'subject',
            'label' => 'Subject',
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
            'key' => 'body_template',
            'label' => 'Preview',
            'formatter' => function ($row, $value) {
                $text = strip_tags((string) $value);
                $text = preg_replace('/\s+/', ' ', $text);
                $text = trim((string) $text);

                if ($text === '') {
                    return '<span class="text-muted">—</span>';
                }

                if (mb_strlen($text) > 90) {
                    $text = mb_substr($text, 0, 90) . '...';
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
            'url' => 'admin/message-templates/edit/',
        ],
        [
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-outline-danger',
            'method' => 'post',
            'icon' => 'bi-trash',
            'url' => 'admin/message-templates/delete/',
            'confirm' => 'Delete this template?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>