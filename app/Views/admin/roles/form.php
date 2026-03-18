<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Role' : 'Create Role',
    'subtitle' => 'Role master data',
    'backUrl' => 'admin/roles',
    'fields' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Role Name', 'required' => true, 'col' => 'col-12 col-lg-6', 'value' => $role['name'] ?? ''],
        ['type' => 'select', 'name' => 'status_id', 'label' => 'Status', 'col' => 'col-12 col-lg-6', 'options' => $statusOptions ?? [], 'value' => $role['status_id'] ?? ''],
    ],
]) ?>

<?= $this->endSection() ?>