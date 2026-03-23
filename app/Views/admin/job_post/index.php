<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Job Posts',
    'subtitle' => 'Manage published job listings',
    'featureCode' => 'job-posts',
    'createUrl' => 'admin/job-posts/create',
    'createLabel' => 'Create Job Post',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search job posts...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $posts ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID'],
        ['key' => 'job_name', 'label' => 'Job', 'formatter' => fn($r, $v) => '<span class="fw-semibold">' . esc($v) . '</span>'],
        ['key' => 'company_name', 'label' => 'Company'],
        ['key' => 'department_name', 'label' => 'Department'],
        ['key' => 'location', 'label' => 'Location'],
        ['key' => 'salary_range', 'label' => 'Salary'],
        ['key' => 'experience_range', 'label' => 'Experience'],
        ['key' => 'rank_hiring', 'label' => 'Rank'],
        ['key' => 'status_name', 'label' => 'Status'],
    ],
    'actions' => [
        ['label' => 'Edit', 'permission' => 'can_edit', 'class' => 'btn btn-sm btn-outline-primary', 'method' => 'get', 'url' => 'admin/job-posts/edit/'],
        ['label' => 'Delete', 'permission' => 'can_delete', 'class' => 'btn btn-sm btn-outline-danger', 'method' => 'post', 'url' => 'admin/job-posts/delete/', 'confirm' => 'Delete this job post?'],
    ],
]) ?>

<?= $this->endSection() ?>