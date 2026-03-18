<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="mx-auto" style="max-width: 1100px;">
    <?= view('admin/partials/form_template', [
        'title' => $mode === 'edit' ? 'Edit Role' : 'Create Role',
        'subtitle' => 'Role master data',
        'backUrl' => 'admin/roles',
        'submitLabel' => $mode === 'edit' ? 'Update Role' : 'Save Role',
        'fields' => [
            [
                'type' => 'text',
                'name' => 'name',
                'label' => 'Role Name',
                'required' => true,
                'col' => 'col-12 col-xl-8',
                'value' => $role['name'] ?? '',
                'placeholder' => 'Enter role name',
                'help' => 'Use a clear and unique role name.',
            ],
            [
                'type' => 'select',
                'name' => 'status_id',
                'label' => 'Status',
                'col' => 'col-12 col-xl-4',
                'options' => $statusOptions ?? [],
                'value' => $role['status_id'] ?? '',
                'help' => 'Set whether this role is active or inactive.',
            ],
        ],
    ]) ?>
</div>

<?= $this->endSection() ?>