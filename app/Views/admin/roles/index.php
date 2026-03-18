<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Roles',
    'subtitle' => 'Manage roles and permissions',
    'featureCode' => 'roles',
    'createUrl' => 'admin/roles/create',
    'createLabel' => '+ Create Role',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search roles...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $roles ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'style' => 'width:90px;'],
        ['key' => 'name', 'label' => 'Role', 'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>'],
        ['key' => 'status_name', 'label' => 'Status'],
        ['key' => 'features_list', 'label' => 'Features'],
    ],
    'actions' => [
        ['label' => 'Features', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-secondary', 'method' => 'get', 'url_builder' => fn($row) => 'admin/roles/' . $row['id'] . '/features'],
        ['label' => 'Users', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-info', 'method' => 'get', 'url_builder' => fn($row) => 'admin/roles/' . $row['id'] . '/users'],
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/roles/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/roles/delete/', 'confirm' => 'Delete this role?'],
    ],
]) ?>

<?= $this->endSection() ?>