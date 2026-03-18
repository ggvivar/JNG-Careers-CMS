<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Statuses',
    'subtitle' => 'Manage statuses and feature assignments',
    'featureCode' => 'status',
    'createUrl' => 'admin/status/create',
    'createLabel' => '+ Create Status',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search statuses...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $statuses ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'style' => 'width:90px;'],
        ['key' => 'name', 'label' => 'Status', 'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>'],
        ['key' => 'feature_names', 'label' => 'Features'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/status/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/status/delete/', 'confirm' => 'Delete this status?'],
    ],
]) ?>

<?= $this->endSection() ?>