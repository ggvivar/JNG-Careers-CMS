<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Jobs',
    'subtitle' => 'Manage job templates',
    'featureCode' => 'jobs',
    'createUrl' => 'admin/jobs/create',
    'createLabel' => '+ Create Job',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search jobs...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $jobs ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Name', 'formatter' => fn($row, $value) => '<span class="fw-semibold">' . esc($value) . '</span>'],
        ['key' => 'description', 'label' => 'Description'],
        ['key' => 'requirement', 'label' => 'Requirement'],
        ['key' => 'status_name', 'label' => 'Status'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/jobs/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/jobs/delete/', 'confirm' => 'Delete this job?'],
    ],
]) ?>

<?= $this->endSection() ?>