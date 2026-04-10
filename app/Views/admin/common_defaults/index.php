<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$groupMap = [];
foreach (($defaults ?? []) as $row) {
    $g = trim((string) ($row['key1'] ?? ''));
    if ($g === '') continue;
    $groupMap[$g] = $g;
}
ksort($groupMap);
?>

<style>
.common-defaults-wrap .toolbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:12px;
    flex-wrap:wrap;
    margin-bottom:16px;
}
.group-tabs{
    display:flex;
    gap:.5rem;
    flex-wrap:wrap;
    margin-bottom:1rem;
}
.group-tab{
    display:inline-flex;
    align-items:center;
    gap:.5rem;
    padding:.55rem 1rem;
    border-radius:999px;
    border:1px solid #dee2e6;
    background:#fff;
    cursor:pointer;
    font-weight:600;
}
.group-tab.active{
    background:#0d6efd;
    border-color:#0d6efd;
    color:#fff;
}
.inline-input{
    border:1px solid transparent;
    width:100%;
    background:transparent;
    border-radius:8px;
    padding:.45rem .55rem;
}
.inline-input:focus{
    outline:none;
    background:#fff;
    border-color:#86b7fe;
    box-shadow:0 0 0 .2rem rgba(13,110,253,.12);
}
.add-row-btn{
    border:1px dashed #0d6efd;
    background:#f8fbff;
    color:#0d6efd;
    border-radius:12px;
    padding:.8rem 1rem;
    width:100%;
    font-weight:600;
}
.group-actions{
    display:flex;
    gap:.5rem;
    flex-wrap:wrap;
}
.empty-state{
    padding:24px;
    text-align:center;
    color:#6c757d;
}
</style>

<div class="common-defaults-wrap">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="mb-1 fw-semibold">Common Defaults</h3>
            <div class="text-muted small">Manage groups and values inline</div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-primary btn-sm" id="btnNewGroup">
                <i class="bi bi-plus-lg me-1"></i>New Group
            </button>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="btnRenameGroup">
                <i class="bi bi-pencil-square me-1"></i>Rename Group
            </button>
            <button type="button" class="btn btn-outline-danger btn-sm" id="btnDeleteGroup">
                <i class="bi bi-trash me-1"></i>Delete Group
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-3 p-md-4">
            <div class="group-tabs" id="groupTabs">
                <?php foreach ($groupMap as $group): ?>
                    <button type="button" class="group-tab" data-group="<?= esc($group) ?>">
                        <?= esc($group) ?>
                    </button>
                <?php endforeach; ?>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="defaultTable">
                    <thead class="table-light">
                        <tr>
                            <th style="min-width:180px;">Value</th>
                            <th style="min-width:120px;">Key 2</th>
                            <th style="min-width:120px;">Key 3</th>
                            <th style="min-width:120px;">Key 4</th>
                            <th style="min-width:120px;">Key 5</th>
                            <th style="min-width:220px;">Definition</th>
                            <th class="text-end" style="width:1%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="mt-3">
                <button type="button" class="add-row-btn" id="btnAddRow">
                    <i class="bi bi-plus-lg me-1"></i>Add Value
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const allData = <?= json_encode(array_values($defaults ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>.map(row => ({
    ...row,
    key1: String(row.key1 ?? '').trim(),
    key2: String(row.key2 ?? ''),
    key3: String(row.key3 ?? ''),
    key4: String(row.key4 ?? ''),
    key5: String(row.key5 ?? ''),
    value: String(row.value ?? ''),
    definition: String(row.definition ?? '')
}));

let currentGroup = <?= json_encode($currentGroup ?? '') ?> || null;

function escapeHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function rowHtml(row = {}) {
    return `
        <tr data-id="${row.id ?? ''}">
            <td><input type="text" class="inline-input" data-field="value" value="${escapeHtml(row.value ?? '')}"></td>
            <td><input type="text" class="inline-input" data-field="key2" value="${escapeHtml(row.key2 ?? '')}"></td>
            <td><input type="text" class="inline-input" data-field="key3" value="${escapeHtml(row.key3 ?? '')}"></td>
            <td><input type="text" class="inline-input" data-field="key4" value="${escapeHtml(row.key4 ?? '')}"></td>
            <td><input type="text" class="inline-input" data-field="key5" value="${escapeHtml(row.key5 ?? '')}"></td>
            <td><input type="text" class="inline-input" data-field="definition" value="${escapeHtml(row.definition ?? '')}"></td>
            <td class="text-end">
                <button type="button" class="btn btn-sm btn-outline-danger btn-delete-row">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>
    `;
}

function getGroups() {
    return [...new Set(
        allData
            .filter(r => !r.date_deleted && String(r.key1 ?? '').trim() !== '')
            .map(r => String(r.key1 ?? '').trim())
    )].sort((a, b) => a.localeCompare(b));
}

function filteredRows() {
    return allData.filter(row =>
        String(row.key1 ?? '').trim() === String(currentGroup ?? '').trim() &&
        !row.date_deleted
    );
}

function renderTabs(activeGroup = null) {
    const tabs = document.getElementById('groupTabs');
    const groups = getGroups();

    tabs.innerHTML = '';

    groups.forEach(group => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'group-tab' + (group === activeGroup ? ' active' : '');
        btn.dataset.group = group;
        btn.textContent = group;
        btn.addEventListener('click', () => {
            currentGroup = group;
            renderTabs(currentGroup);
            renderTable();
        });
        tabs.appendChild(btn);
    });

    if ((!currentGroup || !groups.includes(currentGroup)) && groups.length) {
        currentGroup = groups[0];
    }
}

function renderTable() {
    const tbody = document.querySelector('#defaultTable tbody');

    if (!currentGroup) {
        tbody.innerHTML = `<tr><td colspan="7" class="empty-state">No group selected.</td></tr>`;
        return;
    }

    const rows = filteredRows();

    if (!rows.length) {
        tbody.innerHTML = `<tr><td colspan="7" class="empty-state">No values yet for <strong>${escapeHtml(currentGroup)}</strong>.</td></tr>`;
        return;
    }

    tbody.innerHTML = rows.map(row => rowHtml(row)).join('');
}

function postForm(url, data) {
    const fd = new FormData();
    Object.keys(data).forEach(key => fd.append(key, data[key] ?? ''));
    return fetch(url, {
        method: 'POST',
        body: fd,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    }).then(async res => {
        const json = await res.json();
        if (!res.ok) throw json;
        return json;
    });
}

document.getElementById('btnNewGroup').addEventListener('click', async () => {
    const group = prompt('Enter new group name');
    if (!group) return;

    try {
        const res = await postForm("<?= site_url('admin/common-defaults/create-group') ?>", {
            group: group.trim()
        });

        currentGroup = res.group.trim();
        renderTabs(currentGroup);
        renderTable();
    } catch (err) {
        alert(err.message || 'Unable to create group.');
    }
});

document.getElementById('btnRenameGroup').addEventListener('click', async () => {
    if (!currentGroup) {
        alert('Select a group first.');
        return;
    }

    const newGroup = prompt('Rename group', currentGroup);
    if (!newGroup) return;

    try {
        const res = await postForm("<?= site_url('admin/common-defaults/rename-group') ?>", {
            old_group: currentGroup.trim(),
            new_group: newGroup.trim()
        });

        allData.forEach(row => {
            if (String(row.key1 ?? '').trim() === String(currentGroup ?? '').trim()) {
                row.key1 = res.group.trim();
            }
        });

        currentGroup = res.group.trim();
        renderTabs(currentGroup);
        renderTable();
    } catch (err) {
        alert(err.message || 'Unable to rename group.');
    }
});

document.getElementById('btnDeleteGroup').addEventListener('click', async () => {
    if (!currentGroup) {
        alert('Select a group first.');
        return;
    }

    if (!confirm(`Delete group "${currentGroup}" and all its values?`)) return;

    try {
        await postForm("<?= site_url('admin/common-defaults/delete-group') ?>", {
            group: currentGroup.trim()
        });

        for (let i = allData.length - 1; i >= 0; i--) {
            if (String(allData[i].key1 ?? '').trim() === String(currentGroup ?? '').trim()) {
                allData.splice(i, 1);
            }
        }

        currentGroup = null;
        renderTabs();
        renderTable();
    } catch (err) {
        alert(err.message || 'Unable to delete group.');
    }
});

document.getElementById('btnAddRow').addEventListener('click', () => {
    if (!currentGroup) {
        alert('Create or select a group first.');
        return;
    }

    const tbody = document.querySelector('#defaultTable tbody');
    const empty = tbody.querySelector('.empty-state');
    if (empty) tbody.innerHTML = '';
    tbody.insertAdjacentHTML('beforeend', rowHtml({}));
});

document.addEventListener('change', async function(e) {
    if (!e.target.classList.contains('inline-input')) return;

    const tr = e.target.closest('tr');
    if (!tr) return;

    const payload = {
        id: tr.dataset.id || '',
        key1: String(currentGroup ?? '').trim(),
        value: '',
        key2: '',
        key3: '',
        key4: '',
        key5: '',
        definition: ''
    };

    tr.querySelectorAll('.inline-input').forEach(input => {
        payload[input.dataset.field] = input.value;
    });

    if (!payload.value.trim()) return;

    try {
        const res = await postForm("<?= site_url('admin/common-defaults/save-inline') ?>", payload);
        tr.dataset.id = res.id;

        const existingIndex = allData.findIndex(r => String(r.id) === String(res.id));
        const rowData = {
            id: res.id,
            key1: payload.key1,
            value: payload.value,
            key2: payload.key2,
            key3: payload.key3,
            key4: payload.key4,
            key5: payload.key5,
            definition: payload.definition
        };

        if (existingIndex >= 0) {
            allData[existingIndex] = rowData;
        } else {
            allData.push(rowData);
        }

        renderTabs(currentGroup);
        renderTable();
    } catch (err) {
        alert(err.message || 'Unable to save row.');
    }
});

document.addEventListener('click', async function(e) {
    const btn = e.target.closest('.btn-delete-row');
    if (!btn) return;

    const tr = btn.closest('tr');
    const id = tr?.dataset.id || '';

    if (!id) {
        tr.remove();
        if (!document.querySelector('#defaultTable tbody tr')) {
            renderTable();
        }
        return;
    }

    if (!confirm('Delete this value?')) return;

    try {
        await postForm("<?= site_url('admin/common-defaults/delete-inline/') ?>" + id, {});
        const index = allData.findIndex(r => String(r.id) === String(id));
        if (index >= 0) allData.splice(index, 1);
        tr.remove();

        if (!document.querySelector('#defaultTable tbody tr')) {
            renderTable();
        }

        renderTabs(currentGroup);
    } catch (err) {
        alert(err.message || 'Unable to delete row.');
    }
});

renderTabs(currentGroup);
renderTable();
</script>

<?= $this->endSection() ?>