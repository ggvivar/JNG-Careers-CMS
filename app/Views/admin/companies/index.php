<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Companies',
    'subtitle' => 'Manage companies',
    'featureCode' => 'companies',
    'createUrl' => 'admin/companies/create',
    'createLabel' => 'Create Company',
    'exportUrl' => 'admin/export/companies',
    'importUrl' => 'admin/import/companies',
    'importModalId' => 'importCompaniesModal',
    'importHelp' => 'Columns: name,code,address,contact_no,email,status_name',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search companies...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $companies ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Name', 'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>'],
        ['key' => 'code', 'label' => 'Code'],
        ['key' => 'contact_no', 'label' => 'Contact'],
        ['key' => 'email', 'label' => 'Email'],
        ['key' => 'status_name', 'label' => 'Status'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/companies/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/companies/delete/', 'confirm' => 'Delete this company?'],
    ],
]) ?>

<?= $this->endSection() ?>