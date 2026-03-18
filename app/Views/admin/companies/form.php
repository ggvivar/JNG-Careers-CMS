<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Company' : 'Create Company',
    'subtitle' => 'Company information',
    'backUrl' => 'admin/companies',
    'fields' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Company Name', 'required' => true, 'col' => 'col-12 col-lg-6', 'value' => $company['name'] ?? ''],
        ['type' => 'text', 'name' => 'code', 'label' => 'Code', 'col' => 'col-12 col-lg-3', 'value' => $company['code'] ?? ''],
        ['type' => 'select', 'name' => 'status_id', 'label' => 'Status', 'col' => 'col-12 col-lg-3', 'options' => $statusOptions ?? [], 'value' => $company['status_id'] ?? ''],
        ['type' => 'text', 'name' => 'contact_no', 'label' => 'Contact No', 'col' => 'col-12 col-lg-4', 'value' => $company['contact_no'] ?? ''],
        ['type' => 'email', 'name' => 'email', 'label' => 'Email', 'col' => 'col-12 col-lg-4', 'value' => $company['email'] ?? ''],
        ['type' => 'text', 'name' => 'address', 'label' => 'Address', 'col' => 'col-12 col-lg-4', 'value' => $company['address'] ?? ''],
    ],
]) ?>

<?= $this->endSection() ?>