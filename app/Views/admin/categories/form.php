<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Category' : 'Create Category',
    'subtitle' => 'Category data',
    'backUrl' => 'admin/categories',
    'fields' => [
        ['type' => 'select', 'name' => 'module_id', 'label' => 'Module', 'col' => 'col-12 col-lg-4', 'options' => $moduleOptions ?? [], 'value' => $category['module_id'] ?? ''],
        ['type' => 'text', 'name' => 'name', 'label' => 'Name', 'required' => true, 'col' => 'col-12 col-lg-4', 'value' => $category['name'] ?? ''],
        ['type' => 'text', 'name' => 'key1', 'label' => 'Key 1', 'col' => 'col-12 col-lg-4', 'value' => $category['key1'] ?? ''],
        ['type' => 'text', 'name' => 'key2', 'label' => 'Key 2', 'col' => 'col-12 col-lg-6', 'value' => $category['key2'] ?? ''],
        ['type' => 'text', 'name' => 'key3', 'label' => 'Key 3', 'col' => 'col-12 col-lg-6', 'value' => $category['key3'] ?? ''],
    ],
]) ?>

<?= $this->endSection() ?>