<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Default Value' : 'Create Default Value',
    'subtitle' => 'Common default information',
    'backUrl' => 'admin/common-defaults',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'key1',
            'label' => 'Group / Category',
            'required' => true,
            'col' => 'col-12 col-lg-6',
            'value' => $default['key1'] ?? '',
            'placeholder' => 'e.g. Gender, Civil Status'
        ],
        [
            'type' => 'text',
            'name' => 'value',
            'label' => 'Value',
            'required' => true,
            'col' => 'col-12 col-lg-6',
            'value' => $default['value'] ?? '',
            'placeholder' => 'e.g. Male, Female, Single'
        ],
        [
            'type' => 'text',
            'name' => 'key2',
            'label' => 'Key 2',
            'col' => 'col-12 col-lg-3',
            'value' => $default['key2'] ?? '',
        ],
        [
            'type' => 'text',
            'name' => 'key3',
            'label' => 'Key 3',
            'col' => 'col-12 col-lg-3',
            'value' => $default['key3'] ?? '',
        ],
        [
            'type' => 'text',
            'name' => 'key4',
            'label' => 'Key 4',
            'col' => 'col-12 col-lg-3',
            'value' => $default['key4'] ?? '',
        ],
        [
            'type' => 'text',
            'name' => 'key5',
            'label' => 'Key 5',
            'col' => 'col-12 col-lg-3',
            'value' => $default['key5'] ?? '',
        ],
        [
            'type' => 'textarea',
            'name' => 'definition',
            'label' => 'Definition',
            'col' => 'col-12',
            'value' => $default['definition'] ?? '',
            'rows' => 3,
            'placeholder' => 'Optional description or notes'
        ],
    ],
]) ?>

<?= $this->endSection() ?>