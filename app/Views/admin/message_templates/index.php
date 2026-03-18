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
            'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>',
        ],
        ['key' => 'template_key', 'label' => 'Key'],
        [
            'key' => 'channel',
            'label' => 'Channel',
            'formatter' => function ($row, $value) {
                $badge = strtolower((string) $value) === 'sms'
                    ? 'text-bg-warning'
                    : 'text-bg-primary';

                return '<span class="badge ' . $badge . '">' . esc(strtoupper((string) $value)) . '</span>';
            },
        ],
        ['key' => 'subject', 'label' => 'Subject'],
        [
            'key' => 'body_template',
            'label' => 'Preview',
            'formatter' => function ($row, $value) {
                $text = strip_tags((string) $value);
                if (mb_strlen($text) > 90) {
                    $text = mb_substr($text, 0, 90) . '...';
                }
                return esc($text);
            },
        ],
        ['key' => 'status_name', 'label' => 'Status'],
    ],
    'actions' => [
        [
            'label' => 'Edit',
            'permission' => 'can_edit',
            'class' => 'btn btn-sm btn-outline-primary',
            'method' => 'get',
            'url' => 'admin/message-templates/edit/',
        ],
        [
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-sm btn-outline-danger',
            'method' => 'post',
            'url' => 'admin/message-templates/delete/',
            'confirm' => 'Delete this template?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>