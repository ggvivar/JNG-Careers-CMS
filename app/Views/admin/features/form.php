<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Feature' : 'Create Feature',
    'subtitle' => 'Feature master data',
    'backUrl' => 'admin/features',
    'fields' => [
        [
            'type' => 'select',
            'name' => 'module_id',
            'label' => 'Module',
            'required' => true,
            'col' => 'col-12 col-lg-4',
            'options' => $moduleOptions ?? [],
            'value' => $feature['module_id'] ?? '',
        ],
        [
            'type' => 'text',
            'name' => 'name',
            'label' => 'Feature Name',
            'required' => true,
            'col' => 'col-12 col-lg-4',
            'value' => $feature['name'] ?? '',
        ],
        [
            'type' => 'text',
            'name' => 'code',
            'label' => 'Code',
            'required' => true,
            'col' => 'col-12 col-lg-4',
            'value' => $feature['code'] ?? '',
            'help' => 'Used in routes, e.g. job-posts, site-settings',
        ],
        [
            'type' => 'number',
            'name' => 'sort_order',
            'label' => 'Sort Order',
            'col' => 'col-12 col-lg-3',
            'value' => $feature['sort_order'] ?? 0,
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'label' => 'Description',
            'col' => 'col-12 col-lg-9',
            'value' => $feature['description'] ?? '',
            'rows' => 3,
        ],
    ],
]) ?>

<?= $this->endSection() ?>