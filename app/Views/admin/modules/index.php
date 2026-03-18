<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Modules',
    'subtitle' => 'Manage top-level modules',
    'featureCode' => 'modules',
    'createUrl' => 'admin/modules/create',
    'createLabel' => '+ Create Module',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search modules...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $modules ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'key1', 'label' => 'Key 1'],
        ['key' => 'key2', 'label' => 'Key 2'],
        ['key' => 'key3', 'label' => 'Key 3'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/modules/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/modules/delete/', 'confirm' => 'Delete this module?'],
    ],
]) ?>

<?= $this->endSection() ?>