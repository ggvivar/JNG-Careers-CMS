<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Categories',
    'subtitle' => 'Manage content categories',
    'featureCode' => 'categories',
    'createUrl' => 'admin/categories/create',
    'createLabel' => 'Create Category',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search categories...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $categories ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'module_name', 'label' => 'Module'],
        ['key' => 'key1', 'label' => 'Key 1'],
        ['key' => 'key2', 'label' => 'Key 2'],
        ['key' => 'key3', 'label' => 'Key 3'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/categories/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/categories/delete/', 'confirm' => 'Delete this category?'],
    ],
]) ?>

<?= $this->endSection() ?>