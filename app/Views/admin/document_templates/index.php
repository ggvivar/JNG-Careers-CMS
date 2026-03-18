<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Document Templates',
    'subtitle' => 'Manage attached template files with applicant placeholders',
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
            'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>',
        ],
        ['key' => 'template_key', 'label' => 'Key'],
        ['key' => 'template_type', 'label' => 'Type'],
        ['key' => 'file_name_pattern', 'label' => 'File Name Pattern'],
        [
            'key' => 'source_file_path',
            'label' => 'Template File',
            'formatter' => function ($row, $value) {
                if (empty($value)) {
                    return '<span class="text-muted">-</span>';
                }
                return '<a href="' . site_url($value) . '" target="_blank">Open File</a>';
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
            'url' => 'admin/document-templates/edit/',
        ],
        [
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-sm btn-outline-danger',
            'method' => 'post',
            'url' => 'admin/document-templates/delete/',
            'confirm' => 'Delete this template?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>