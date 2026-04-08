<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Jobs',
    'subtitle' => 'Manage job templates',
    'featureCode' => 'job',
    'createUrl' => 'admin/jobs/create',
    'createLabel' => 'Create Job',
    'importUrl' => 'admin/import/job',
    'exportUrl' => 'admin/export/job',
    'importModalId' => 'importJobModal',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search jobs...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $jobs ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'name', 'label' => 'Name', 'formatter' => fn($row, $value) => '<a href=`/edit/id`><span class="fw-semibold">' . esc($value) . '</span></a>'],
        ['key' => 'job_code', 'label' => 'Code'],
        ['key' => 'requirement', 'label' => 'Requirement'],
        ['key' => 'status_name', 'label' => 'Status'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/jobs/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/jobs/delete/', 'confirm' => 'Delete this job?'],
    ],
]) ?>

<?= $this->endSection() ?>