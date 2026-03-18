<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Module' : 'Create Module',
    'subtitle' => 'Top-level module data',
    'backUrl' => 'admin/modules',
    'fields' => [
        ['type' => 'text', 'name' => 'name', 'label' => 'Name', 'required' => true, 'col' => 'col-12 col-lg-6', 'value' => $module['name'] ?? ''],
        ['type' => 'text', 'name' => 'key1', 'label' => 'Key 1', 'col' => 'col-12 col-lg-2', 'value' => $module['key1'] ?? ''],
        ['type' => 'text', 'name' => 'key2', 'label' => 'Key 2', 'col' => 'col-12 col-lg-2', 'value' => $module['key2'] ?? ''],
        ['type' => 'text', 'name' => 'key3', 'label' => 'Key 3', 'col' => 'col-12 col-lg-2', 'value' => $module['key3'] ?? ''],
    ],
]) ?>

<?= $this->endSection() ?>