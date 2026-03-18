<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Departments',
    'subtitle' => 'Manage departments',
    'featureCode' => 'departments',
    'createUrl' => 'admin/departments/create',
    'createLabel' => '+ Create Department',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search departments...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $departments ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Department', 'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>'],
        ['key' => 'company_name', 'label' => 'Company'],
        ['key' => 'code', 'label' => 'Code'],
        ['key' => 'manager_name', 'label' => 'Manager'],
        ['key' => 'status_name', 'label' => 'Status'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/departments/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/departments/delete/', 'confirm' => 'Delete this department?'],
    ],
]) ?>

<?= $this->endSection() ?>