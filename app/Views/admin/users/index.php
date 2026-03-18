<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Users',
    'subtitle' => 'Manage admin users',
    'featureCode' => 'users',
    'createUrl' => 'admin/users/create',
    'createLabel' => '+ Create User',
    'exportUrl' => 'admin/export/users',
    'importUrl' => 'admin/import/users',
    'importModalId' => 'importUsersModal',
    'importHelp' => 'Columns: username,email,name,company_names,department_name,role_name,password',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search users...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $users ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'style' => 'width:90px;'],
        ['key' => 'username', 'label' => 'Username', 'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>'],
        ['key' => 'name', 'label' => 'Name'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'company_names', 'label' => 'Companies'],
        ['key' => 'department_name', 'label' => 'Department'],
        ['key' => 'role_name', 'label' => 'Role'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/users/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/users/delete/', 'confirm' => 'Delete this user?'],
    ],
]) ?>

<?= $this->endSection() ?>