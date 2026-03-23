<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/table_template', [
    'title' => 'Contents',
    'subtitle' => 'Manage CMS contents',
    'featureCode' => 'contents',
    'createUrl' => 'admin/contents/create',
    'createLabel' => 'Create Content',
    'exportUrl' => 'admin/export/contents',
    'importUrl' => 'admin/import/contents',
    'importModalId' => 'importContentsModal',
    'importHelp' => 'Columns: module_name,category_name,name,description,body,tags,rank,status_name,validity_date_start,validity_date_end,parent_title',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search contents...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $contents ?? [],
    'columns' => [
        [
            'key' => 'name',
            'label' => 'Title',
            'formatter' => function ($row, $value) {
                return '
                    <div class="d-flex flex-column">
                        <span class="fw-semibold text-dark">' . esc($value) . '</span>
                        ' . (!empty($row['description']) ? '<small class="text-muted">' . esc(mb_strimwidth(strip_tags($row['description']), 0, 80, '...')) . '</small>' : '') . '
                    </div>
                ';
            },
        ],
        [
            'key' => 'module_name',
            'label' => 'Module',
            'formatter' => function ($row, $value) {
                return $value
                    ? '<span class="badge rounded-pill text-bg-light border">' . esc($value) . '</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'category_name',
            'label' => 'Category',
            'formatter' => function ($row, $value) {
                return $value
                    ? '<span class="badge rounded-pill text-bg-light border">' . esc($value) . '</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'status_name',
            'label' => 'Status',
            'formatter' => function ($row, $value) {
                $status = strtolower((string) $value);

                $class = 'text-bg-light border text-dark';

                if ($status === 'approved') {
                    $class = 'text-bg-success';
                } elseif ($status === 'submitted') {
                    $class = 'text-bg-warning';
                } elseif ($status === 'rejected') {
                    $class = 'text-bg-danger';
                } elseif ($status === 'draft') {
                    $class = 'text-bg-secondary';
                }

                return $value
                    ? '<span class="badge rounded-pill ' . $class . '">' . esc($value) . '</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
        [
            'key' => 'rank',
            'label' => 'Rank',
            'formatter' => function ($row, $value) {
                return $value !== null && $value !== ''
                    ? '<span class="fw-semibold">' . esc($value) . '</span>'
                    : '<span class="text-muted">—</span>';
            },
        ],
    ],
    'actions' => [
        [
            'icon' => 'bi-eye',
            'label' => 'View',
            'permission' => 'can_view',
            'class' => 'btn btn-sm btn-outline-success',
            'method' => 'get',
            'url' => 'admin/contents/',
        ],
        [
            'icon' => 'bi-pencil-square',
            'label' => 'Edit',
            'permission' => 'can_edit',
            'class' => 'btn btn-sm btn-outline-primary',
            'method' => 'get',
            'url' => 'admin/contents/edit/',
        ],
        [
            'icon' => 'bi-trash',
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-sm btn-outline-danger',
            'method' => 'post',
            'url' => 'admin/contents/delete/',
            'confirm' => 'Delete this content?',
        ],
    ],
]) ?>

<?= $this->endSection() ?>