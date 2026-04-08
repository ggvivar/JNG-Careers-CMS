<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Job' : 'Create Job',
    'subtitle' => 'Job template information',
    'backUrl' => 'admin/jobs',
    'fields' => [
        [
            'type' => 'text',
            'name' => 'name',
            'label' => 'Job Name',
            'required' => true,
            'col' => 'col-12 col-lg-6',
            'value' => $job['name'] ?? '',
        ],
        [
            'type' => 'text',
            'name' => 'job_code',
            'label' => 'Job Code',
            'required' => true,
            'col' => 'col-12 col-lg-3',
            'value' => $job['job_code'] ?? '',
        ],
        [
            'type' => 'select',
            'name' => 'company_id',
            'label' => 'Company',
            'required' => true,
            'col' => 'col-12 col-lg-3',
            'options' => $companyOptions ?? ['' => 'Select Company'],
            'value' => $job['company_id'] ?? '',
        ],
        [
            'type' => 'select',
            'name' => 'unit_id',
            'label' => 'Unit',
            'required' => true,
            'col' => 'col-12 col-lg-4',
            'options' => $unitOptions ?? ['' => 'Select Unit'],
            'value' => $job['unit_id'] ?? '',
        ],
        [
            'type' => 'select',
            'name' => 'group_id',
            'label' => 'Group',
            'required' => true,
            'col' => 'col-12 col-lg-4',
            'options' => $groupOptions ?? ['' => 'Select Group'],
            'value' => $job['group_id'] ?? '',
        ],
        [
            'type' => 'select',
            'name' => 'status_id',
            'label' => 'Status',
            'col' => 'col-12 col-lg-4',
            'options' => $statusOptions ?? ['' => 'Select Status'],
            'value' => $job['status_id'] ?? '',
        ],
        [
            'type' => 'textarea',
            'name' => 'description',
            'label' => 'Description',
            'col' => 'col-12',
            'value' => $job['description'] ?? '',
            'rows' => 4,
        ],
        [
            'type' => 'textarea',
            'name' => 'requirement',
            'label' => 'Requirement',
            'col' => 'col-12',
            'value' => $job['requirement'] ?? '',
            'rows' => 4,
        ],
    ],
]) ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const companySelect = document.querySelector('select[name="company_id"]');
    const unitSelect = document.querySelector('select[name="unit_id"]');
    const groupSelect = document.querySelector('select[name="group_id"]');

    const selectedUnitId = '<?= esc((string) ($job['unit_id'] ?? '')) ?>';
    const selectedGroupId = '<?= esc((string) ($job['group_id'] ?? '')) ?>';

    if (!companySelect || !unitSelect || !groupSelect) return;

    function setOptions(selectEl, items, placeholder, selectedValue = '') {
        let html = `<option value="">${placeholder}</option>`;

        items.forEach(item => {
            const selected = String(item.value) === String(selectedValue) ? 'selected' : '';
            html += `<option value="${item.value}" ${selected}>${item.label}</option>`;
        });

        selectEl.innerHTML = html;
    }

    function resetUnits() {
        unitSelect.innerHTML = '<option value="">Select Unit</option>';
    }

    function resetGroups() {
        groupSelect.innerHTML = '<option value="">Select Group</option>';
    }

    function loadUnits(companyId, selectedUnit = '', selectedGroup = '') {
        resetUnits();
        resetGroups();

        if (!companyId) return;

        unitSelect.innerHTML = '<option value="">Loading units...</option>';

        fetch('<?= base_url('admin/jobs/units-by-company') ?>?company_id=' + encodeURIComponent(companyId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const units = Array.isArray(data.units) ? data.units : [];
            setOptions(unitSelect, units, 'Select Unit', selectedUnit);

            if (selectedUnit) {
                loadGroups(selectedUnit, selectedGroup);
            }
        })
        .catch(() => {
            resetUnits();
            resetGroups();
        });
    }

    function loadGroups(unitId, selectedGroup = '') {
        resetGroups();

        if (!unitId) return;

        groupSelect.innerHTML = '<option value="">Loading groups...</option>';

        fetch('<?= base_url('admin/jobs/groups-by-unit') ?>?unit_id=' + encodeURIComponent(unitId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.json())
        .then(data => {
            const groups = Array.isArray(data.groups) ? data.groups : [];
            setOptions(groupSelect, groups, 'Select Group', selectedGroup);
        })
        .catch(() => {
            resetGroups();
        });
    }

    companySelect.addEventListener('change', function () {
        loadUnits(this.value);
    });

    unitSelect.addEventListener('change', function () {
        loadGroups(this.value);
    });

    if (companySelect.value) {
        loadUnits(companySelect.value, selectedUnitId, selectedGroupId);
    }
});
</script>

<?= $this->endSection() ?>