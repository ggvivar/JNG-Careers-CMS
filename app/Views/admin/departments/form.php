<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Department' : 'Create Department',
    'subtitle' => 'Department information',
    'backUrl' => 'admin/departments',
    'fields' => [
        ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'required' => true, 'col' => 'col-12 col-lg-6', 'options' => $companyOptions ?? [], 'value' => $department['company_id'] ?? ''],
        ['type' => 'text', 'name' => 'name', 'label' => 'Department Name', 'required' => true, 'col' => 'col-12 col-lg-6', 'value' => $department['name'] ?? ''],
        ['type' => 'text', 'name' => 'code', 'label' => 'Code', 'col' => 'col-12 col-lg-4', 'value' => $department['code'] ?? ''],
        ['type' => 'select', 'name' => 'manager_user_id', 'label' => 'Manager', 'col' => 'col-12 col-lg-4', 'options' => $managerOptions ?? [], 'value' => $department['manager_user_id'] ?? ''],
        ['type' => 'select', 'name' => 'status_id', 'label' => 'Status', 'col' => 'col-12 col-lg-4', 'options' => $statusOptions ?? [], 'value' => $department['status_id'] ?? ''],
    ],
]) ?>

<?= $this->endSection() ?>