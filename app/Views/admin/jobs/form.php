<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Job' : 'Create Job',
    'subtitle' => 'Job template information',
    'backUrl' => 'admin/jobs',
    'fields' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Job Name', 'required' => true, 'col' => 'col-12', 'value' => $job['name'] ?? ''],
        ['type' => 'textarea', 'name' => 'description', 'label' => 'Description', 'col' => 'col-12', 'value' => $job['description'] ?? '', 'rows' => 4],
        ['type' => 'textarea', 'name' => 'requirement', 'label' => 'Requirement', 'col' => 'col-12', 'value' => $job['requirement'] ?? '', 'rows' => 4],
        ['type' => 'select', 'name' => 'status_id', 'label' => 'Status', 'col' => 'col-12 col-lg-4', 'options' => $statusOptions ?? [], 'value' => $job['status_id'] ?? ''],
    ],
]) ?>

<?= $this->endSection() ?>