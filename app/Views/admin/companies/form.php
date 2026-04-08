<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <form method="post" action="">
        <?= csrf_field() ?>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pb-0">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1"><?= $mode === 'edit' ? 'Edit Company' : 'Create Company' ?></h4>
                        <p class="text-muted mb-0">Company information</p>
                    </div>
                    <div>
                        <a href="<?= base_url('admin/companies') ?>" class="btn btn-outline-secondary btn-sm">Back</a>
                    </div>
                </div>
            </div>

            <div class="card-body pt-4">
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Company Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= esc(old('name', $company['name'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Code</label>
                        <input type="text" name="code" class="form-control" value="<?= esc(old('code', $company['code'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Status</label>
                        <select name="status_id" class="form-select">
                            <option value="">Select Status</option>
                            <?php foreach (($statusOptions ?? []) as $value => $label): ?>
                                <option value="<?= esc($value) ?>" <?= (string) old('status_id', $company['status_id'] ?? '') === (string) $value ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Contact No</label>
                        <input type="text" name="contact_no" class="form-control" value="<?= esc(old('contact_no', $company['contact_no'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?= esc(old('email', $company['email'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control" value="<?= esc(old('address', $company['address'] ?? '')) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-1">Unit and Group Maintenance</h5>
                    <p class="text-muted mb-0 small">Manage units and their groups under this company</p>
                </div>
                <button type="button" class="btn btn-primary btn-sm" id="addUnitBtn">+ Add Unit</button>
            </div>

            <div class="card-body">
                <div id="unitsContainer"></div>

                <div id="noUnitsMessage" class="text-muted text-center py-4" style="display:none;">
                    No units added yet.
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="<?= base_url('admin/companies') ?>" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <?= $mode === 'edit' ? 'Update Company' : 'Create Company' ?>
            </button>
        </div>
    </form>
</div>

<script>
const existingUnits = <?= json_encode($unitsWithGroups ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
let unitIndex = 0;

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function updateNoUnitsMessage() {
    const unitsContainer = document.getElementById('unitsContainer');
    const noUnitsMessage = document.getElementById('noUnitsMessage');
    noUnitsMessage.style.display = unitsContainer.children.length ? 'none' : 'block';
}

function refreshUnitTitles() {
    document.querySelectorAll('.unit-card').forEach((card, idx) => {
        const titleEl = card.querySelector('.unit-title');
        const nameInput = card.querySelector('.unit-name-input');
        const codeInput = card.querySelector('.unit-code-input');

        const name = nameInput?.value?.trim() || '';
        const code = codeInput?.value?.trim() || '';

        let title = `Unit ${idx + 1}`;
        if (name && code) title = `${name} (${code})`;
        else if (name) title = name;
        else if (code) title = code;

        if (titleEl) titleEl.textContent = title;
    });
}

function refreshGroupCounts() {
    document.querySelectorAll('.unit-card').forEach((card) => {
        const countEl = card.querySelector('.group-count');
        const count = card.querySelectorAll('.group-row').length;
        if (countEl) {
            countEl.textContent = `${count} Group${count !== 1 ? 's' : ''}`;
        }
    });
}

function bindCollapseChevron(card, collapseId) {
    const collapseEl = document.getElementById(collapseId);
    const chevronEl = card.querySelector('.toggle-chevron');

    if (!collapseEl || !chevronEl) return;

    collapseEl.addEventListener('show.bs.collapse', function () {
        chevronEl.classList.remove('bi-chevron-down');
        chevronEl.classList.add('bi-chevron-up');
    });

    collapseEl.addEventListener('hide.bs.collapse', function () {
        chevronEl.classList.remove('bi-chevron-up');
        chevronEl.classList.add('bi-chevron-down');
    });
}

function addGroupRow(groupsContainer, unitIdx, group = {}) {
    const groupIndex = groupsContainer.querySelectorAll('.group-row').length;

    const row = document.createElement('div');
    row.className = 'group-row border rounded p-3 mb-2 bg-light';

    row.innerHTML = `
        <div class="row g-2 align-items-end">
            <input type="hidden" name="units[${unitIdx}][groups][${groupIndex}][id]" value="${escapeHtml(group.id ?? '')}">

            <div class="col-12 col-lg-4">
                <label class="form-label mb-1">Group Name</label>
                <input type="text" name="units[${unitIdx}][groups][${groupIndex}][name]" class="form-control" value="${escapeHtml(group.name ?? '')}" placeholder="Enter group name">
            </div>

            <div class="col-12 col-lg-3">
                <label class="form-label mb-1">Group Code</label>
                <input type="text" name="units[${unitIdx}][groups][${groupIndex}][code]" class="form-control" value="${escapeHtml(group.code ?? '')}" placeholder="Enter group code">
            </div>

            <div class="col-12 col-lg-4">
                <label class="form-label mb-1">Status</label>
                <select name="units[${unitIdx}][groups][${groupIndex}][status_id]" class="form-select">
                    <option value="">Select Status</option>
                    <?php foreach (($statusOptions ?? []) as $value => $label): ?>
                        <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-lg-1">
                <button type="button" class="btn btn-outline-danger w-100 remove-group-btn">×</button>
            </div>
        </div>
    `;

    groupsContainer.appendChild(row);

    const statusSelect = row.querySelector(`select[name="units[${unitIdx}][groups][${groupIndex}][status_id]"]`);
    if (statusSelect && group.status_id) {
        statusSelect.value = String(group.status_id);
    }

    row.querySelector('.remove-group-btn').addEventListener('click', function () {
        row.remove();
        refreshGroupCounts();
    });

    refreshGroupCounts();
}

function addUnitCard(unit = {}) {
    const unitsContainer = document.getElementById('unitsContainer');
    const currentUnitIndex = unitIndex++;
    const collapseId = `unitCollapse_${currentUnitIndex}`;

    const unitCard = document.createElement('div');
    unitCard.className = 'card border mb-3 unit-card';

    unitCard.innerHTML = `
        <div class="card-header bg-white py-3">
            <div class="d-flex justify-content-between align-items-center gap-2 flex-wrap">
                <button
                    type="button"
                    class="btn btn-link text-decoration-none text-start p-0 flex-grow-1 d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse"
                    data-bs-target="#${collapseId}"
                    aria-expanded="true"
                >
                    <div class="d-flex flex-column">
                        <span class="fw-semibold unit-title">Unit</span>
                        <small class="text-muted group-count">0 Groups</small>
                    </div>

                    <i class="bi bi-chevron-up fs-6 toggle-chevron"></i>
                </button>

                <button type="button" class="btn btn-outline-danger btn-sm remove-unit-btn"><i class="bi bi-trash"></i></button>
            </div>
        </div>

        <div id="${collapseId}" class="collapse show">
            <div class="card-body">
                <input type="hidden" name="units[${currentUnitIndex}][id]" value="${escapeHtml(unit.id ?? '')}">

                <div class="row g-3 mb-3">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">Unit Name</label>
                        <input type="text" name="units[${currentUnitIndex}][name]" class="form-control unit-name-input" value="${escapeHtml(unit.name ?? '')}" placeholder="Enter unit name">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Unit Code</label>
                        <input type="text" name="units[${currentUnitIndex}][code]" class="form-control unit-code-input" value="${escapeHtml(unit.code ?? '')}" placeholder="Enter unit code">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Status</label>
                        <select name="units[${currentUnitIndex}][status_id]" class="form-select">
                            <option value="">Select Status</option>
                            <?php foreach (($statusOptions ?? []) as $value => $label): ?>
                                <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-2 d-flex align-items-end">
                        <button type="button" class="btn btn-outline-primary w-100 add-group-btn">+ Add Group</button>
                    </div>
                </div>

                <div class="groups-container"></div>
            </div>
        </div>
    `;

    unitsContainer.appendChild(unitCard);

    const unitStatusSelect = unitCard.querySelector(`select[name="units[${currentUnitIndex}][status_id]"]`);
    if (unitStatusSelect && unit.status_id) {
        unitStatusSelect.value = String(unit.status_id);
    }

    const nameInput = unitCard.querySelector('.unit-name-input');
    const codeInput = unitCard.querySelector('.unit-code-input');

    nameInput.addEventListener('input', refreshUnitTitles);
    codeInput.addEventListener('input', refreshUnitTitles);

    const groupsContainer = unitCard.querySelector('.groups-container');

    if (Array.isArray(unit.groups) && unit.groups.length) {
        unit.groups.forEach(group => addGroupRow(groupsContainer, currentUnitIndex, group));
    }

    unitCard.querySelector('.add-group-btn').addEventListener('click', function () {
        addGroupRow(groupsContainer, currentUnitIndex, {});
    });

    unitCard.querySelector('.remove-unit-btn').addEventListener('click', function () {
        unitCard.remove();
        updateNoUnitsMessage();
        refreshUnitTitles();
        refreshGroupCounts();
    });

    bindCollapseChevron(unitCard, collapseId);

    updateNoUnitsMessage();
    refreshUnitTitles();
    refreshGroupCounts();
}

document.getElementById('addUnitBtn').addEventListener('click', function () {
    addUnitCard({});
});

document.addEventListener('DOMContentLoaded', function () {
    if (Array.isArray(existingUnits) && existingUnits.length) {
        existingUnits.forEach(unit => addUnitCard(unit));
    }
    updateNoUnitsMessage();
    refreshUnitTitles();
    refreshGroupCounts();
});
</script>

<?= $this->endSection() ?>