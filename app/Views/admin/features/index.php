<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Features',
    'subtitle' => 'Manage system features per module',
    'featureCode' => 'modules',
    'createUrl' => 'admin/features/create',
    'createLabel' => 'Create Feature',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search features...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $features ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'style' => 'width:90px;'],
        [
            'key' => 'name',
            'label' => 'Feature',
            'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>',
        ],
        ['key' => 'code', 'label' => 'Code'],
        ['key' => 'module_name', 'label' => 'Module'],
        ['key' => 'sort_order', 'label' => 'Sort'],
        ['key' => 'description', 'label' => 'Description'],
    ],
    'actions' => [
        [
            'label' => 'Edit',
            'permission' => 'can_edit',
            'class' => 'btn btn-sm btn-outline-primary',
            'method' => 'get',
            'url' => 'admin/features/edit/',
        ],
        [
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-sm btn-outline-danger',
            'method' => 'post',
            'url' => 'admin/features/delete/',
            'confirm' => 'Delete this feature?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>