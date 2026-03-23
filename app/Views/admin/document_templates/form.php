<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$currentSourceFileUrl = !empty($template['source_file_path'])
    ? site_url($template['source_file_path'])
    : '';
?>

<style>
    .content-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .modern-card {
        border: 1px solid #e9ecef;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06);
        overflow: hidden;
        background: #fff;
    }

    .modern-card + .modern-card {
        margin-top: 1.25rem;
    }

    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .section-subtitle {
        font-size: .85rem;
        color: #6c757d;
        margin-top: .2rem;
    }

    .page-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .page-title {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -.02em;
        margin-bottom: .15rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .75rem;
        font-weight: 600;
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.08);
        border: 1px solid rgba(13, 110, 253, 0.12);
        padding: .4rem .7rem;
        border-radius: 999px;
    }

    .modern-card .card-body {
        padding: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        color: #344054;
        margin-bottom: .45rem;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe1e7;
        padding-top: .6rem;
        padding-bottom: .6rem;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12) !important;
    }

    .variable-panel {
        border: 1px solid #e9ecef;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(16, 24, 40, 0.04);
    }

    .variable-pill {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: .5rem .8rem;
        font-size: .82rem;
        font-weight: 600;
        background: #eef4ff;
        color: #155eef;
        border: 1px solid #d9e6ff;
        cursor: grab;
        user-select: none;
    }

    .variable-pill:hover {
        background: #dfeaff;
    }

    .preview-box {
        min-height: 240px;
        border: 1px dashed #d0d5dd;
        border-radius: 16px;
        background: #f8fafc;
        padding: 1rem;
    }

    .file-chip {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .6rem .8rem;
        border: 1px solid #e4e7ec;
        border-radius: 12px;
        background: #f9fafb;
        font-weight: 600;
        color: #344054;
    }

    .sticky-actions {
        position: sticky;
        bottom: 16px;
        z-index: 10;
        margin-top: 1.25rem;
    }

    .sticky-actions-inner {
        display: flex;
        gap: .75rem;
        justify-content: flex-end;
        align-items: center;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(222, 226, 230, 0.8);
        box-shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
        border-radius: 18px;
        padding: .9rem 1rem;
    }

    .btn {
        border-radius: 12px;
    }

    .btn-primary {
        box-shadow: 0 8px 18px rgba(13, 110, 253, 0.18);
    }

    .meta-note {
        font-size: .8rem;
        color: #98a2b3;
    }

    .drop-target.border-primary {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .12) !important;
    }
</style>

<div class="content-page">
    <div class="page-hero">
        <div>
            <div class="badge-soft mb-2">
                <span>Templates</span>
                <span><?= esc($mode === 'edit' ? 'Edit Mode' : 'Create Mode') ?></span>
            </div>
            <h2 class="page-title"><?= esc($mode === 'edit' ? 'Edit Document Template' : 'Create Document Template') ?></h2>
            <p class="page-subtitle">Manage attached template files and reusable placeholders for generated documents.</p>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/document-templates') ?>">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data" id="documentTemplateForm">
        <?= csrf_field() ?>
        <input type="hidden" name="available_vars" id="available_vars" value="<?= esc(old('available_vars', $template['available_vars'] ?? '')) ?>">

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Template Details</h3>
                <div class="section-subtitle">Basic settings and source mapping for document generation</div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" required value="<?= esc(old('name', $template['name'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Template Key</label>
                        <input type="text" name="template_key" class="form-control" required value="<?= esc(old('template_key', $template['template_key'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Template Type</label>
                        <select name="template_type" class="form-select" required>
                            <?php foreach (($templateTypeOptions ?? []) as $value => $label): ?>
                                <option value="<?= esc($value) ?>" <?= (string) old('template_type', $template['template_type'] ?? 'docx') === (string) $value ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Source Table</label>
                        <select name="source_table" id="source_table" class="form-select">
                            <option value="">-- Select --</option>
                            <?php foreach (($sourceTableOptions ?? []) as $value => $label): ?>
                                <option value="<?= esc($value) ?>" <?= (string) old('source_table', $template['source_table'] ?? '') === (string) $value ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Status</label>
                        <select name="status_id" class="form-select">
                            <option value="">-- Select --</option>
                            <?php foreach (($statusOptions ?? []) as $id => $label): ?>
                                <option value="<?= esc($id) ?>" <?= (string) old('status_id', $template['status_id'] ?? '') === (string) $id ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-8">
                        <label class="form-label">File Name Pattern</label>
                        <input
                            type="text"
                            name="file_name_pattern"
                            id="file_name_pattern"
                            class="form-control drop-target"
                            value="<?= esc(old('file_name_pattern', $template['file_name_pattern'] ?? '')) ?>"
                            placeholder="Offer-Letter-{{applicant.name}}-{{job.name}}"
                        >
                        <div class="meta-note mt-1">Drag or click variables to build the generated file name pattern.</div>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Source Template File</label>
                        <input type="file" name="source_file" class="form-control">
                        <div class="meta-note mt-1">Allowed file types: DOCX and PDF.</div>
                    </div>

                    <?php if (!empty($template['source_file_path'])): ?>
                        <div class="col-12">
                            <label class="form-label">Current File</label>
                            <div class="file-chip">
                                <i class="bi bi-file-earmark-text"></i>
                                <a href="<?= site_url($template['source_file_path']) ?>" target="_blank" class="text-decoration-none">
                                    <?= esc(basename($template['source_file_path'])) ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4"><?= esc(old('description', $template['description'] ?? '')) ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Variables</h3>
                <div class="section-subtitle">Placeholders available from the selected source table</div>
            </div>

            <div class="card-body">
                <div id="variableTrayWrap"></div>
            </div>
        </div>

        <div class="sticky-actions">
            <div class="sticky-actions-inner">
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/document-templates') ?>">Cancel</a>
                <button class="btn btn-outline-primary" type="button" id="previewDocumentTemplateBtn">
                    <i class="bi bi-eye me-1"></i> Preview
                </button>
                <button class="btn btn-primary px-4" type="submit">
                    <i class="bi bi-check-lg me-1"></i> Save Template
                </button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="documentTemplatePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:18px;">
            <div class="modal-header">
                <h5 class="modal-title">Document Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-12 col-lg-4">
                        <div class="mb-3">
                            <div class="small text-muted mb-1">File Name Pattern</div>
                            <div class="preview-box bg-light" id="previewFileNamePattern"></div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Template Type</div>
                            <div class="preview-box bg-light" id="previewTemplateType"></div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Source Table</div>
                            <div class="preview-box bg-light" id="previewSourceTable"></div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Description</div>
                            <div class="preview-box bg-light" id="previewDescription" style="white-space: pre-wrap;"></div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted mb-1">Template File</div>
                            <div class="preview-box bg-light" id="previewFileMeta"></div>
                        </div>
                    </div>

                    <div class="col-12 col-lg-8">
                        <div class="small text-muted mb-1">Attached Document Preview</div>
                        <div id="documentPreviewContainer" class="preview-box" style="min-height: 520px; overflow:auto;">
                            <div class="text-muted">No preview available.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/mammoth@1.8.0/mammoth.browser.min.js"></script>
<script>
const variableMap = <?= json_encode($variableMap ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
const currentSourceFileUrl = <?= json_encode($currentSourceFileUrl ?? '', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
let selectedTemplateFileObjectUrl = null;

function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function insertAtCursor(el, text) {
    if (!el) return;
    el.focus();

    if (typeof el.selectionStart === 'number' && typeof el.selectionEnd === 'number') {
        const start = el.selectionStart;
        const end = el.selectionEnd;
        const value = el.value || '';
        el.value = value.substring(0, start) + text + value.substring(end);
        el.selectionStart = el.selectionEnd = start + text.length;
    } else {
        el.value = (el.value || '') + text;
    }

    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
}

function setAvailableVars(vars) {
    document.getElementById('available_vars').value = vars.join(', ');
}

function buildVariableTray(vars) {
    const wrap = document.getElementById('variableTrayWrap');

    if (!vars.length) {
        wrap.innerHTML = '<div class="meta-note">Select a source table to load available variables.</div>';
        return;
    }

    let html = '<div class="variable-panel p-3"><div class="d-flex flex-wrap gap-2">';
    vars.forEach(variable => {
        html += `
            <span
                class="variable-pill"
                draggable="true"
                data-variable="${variable.replace(/"/g, '&quot;')}"
            >
                ${variable}
            </span>
        `;
    });
    html += '</div><div class="meta-note mt-3">Drag or click variables to insert them into the file name pattern.</div></div>';

    wrap.innerHTML = html;
}

function updateAvailableVars() {
    const sourceTable = document.getElementById('source_table').value || '';
    const vars = variableMap[sourceTable] || [];
    setAvailableVars(vars);
    buildVariableTray(vars);
}

function enableDropTarget(el) {
    if (!el) return;

    el.addEventListener('dragover', function (e) {
        e.preventDefault();
        el.classList.add('border-primary');
    });

    el.addEventListener('dragleave', function () {
        el.classList.remove('border-primary');
    });

    el.addEventListener('drop', function (e) {
        e.preventDefault();
        el.classList.remove('border-primary');
        const variable = e.dataTransfer.getData('text/plain');
        insertAtCursor(el, variable);
    });
}

function getSelectedTemplateTypeText() {
    const select = document.querySelector('select[name="template_type"]');
    return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '—';
}

function getSelectedSourceTableText() {
    const select = document.getElementById('source_table');
    return select.options[select.selectedIndex] ? select.options[select.selectedIndex].text : '—';
}

function setPreviewMeta(fileName, sourceLabel) {
    document.getElementById('previewFileMeta').innerHTML = `
        <div><strong>File:</strong> ${escapeHtml(fileName || '—')}</div>
        <div><strong>Source:</strong> ${escapeHtml(sourceLabel || '—')}</div>
    `;
}

function renderPdfPreview(previewUrl) {
    document.getElementById('documentPreviewContainer').innerHTML = `
        <iframe
            src="${previewUrl}"
            style="width:100%; height:520px; border:0; background:#fff; border-radius:12px;"
            title="PDF Preview"
        ></iframe>
    `;
}

async function renderDocxPreviewFromFile(file) {
    const container = document.getElementById('documentPreviewContainer');
    container.innerHTML = '<div class="text-muted">Rendering DOCX preview...</div>';

    try {
        const arrayBuffer = await file.arrayBuffer();
        const result = await mammoth.convertToHtml({ arrayBuffer });

        container.innerHTML = `
            <div class="bg-white p-4 rounded border" style="min-height:500px;">
                ${result.value || '<div class="text-muted">No preview content.</div>'}
            </div>
        `;
    } catch (error) {
        container.innerHTML = '<div class="alert alert-warning mb-0">Unable to render DOCX preview.</div>';
        console.error(error);
    }
}

async function renderDocxPreviewFromUrl(url) {
    const container = document.getElementById('documentPreviewContainer');
    container.innerHTML = '<div class="text-muted">Rendering DOCX preview...</div>';

    try {
        const response = await fetch(url);
        const arrayBuffer = await response.arrayBuffer();
        const result = await mammoth.convertToHtml({ arrayBuffer });

        container.innerHTML = `
            <div class="bg-white p-4 rounded border" style="min-height:500px;">
                ${result.value || '<div class="text-muted">No preview content.</div>'}
            </div>
        `;
    } catch (error) {
        container.innerHTML = `
            <div class="alert alert-warning">Unable to render the saved DOCX preview in-browser.</div>
            <a href="${url}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">Open DOCX</a>
        `;
        console.error(error);
    }
}

function renderNoPreview(message) {
    document.getElementById('documentPreviewContainer').innerHTML =
        `<div class="text-muted">${escapeHtml(message || 'No preview available.')}</div>`;
}

async function buildDocumentPreview() {
    const fileNamePattern = document.getElementById('file_name_pattern').value || '';
    const description = document.getElementById('description').value || '';
    const fileInput = document.querySelector('input[name="source_file"]');
    const selectedFile = fileInput && fileInput.files && fileInput.files.length ? fileInput.files[0] : null;

    document.getElementById('previewFileNamePattern').textContent = fileNamePattern || '—';
    document.getElementById('previewTemplateType').textContent = getSelectedTemplateTypeText() || '—';
    document.getElementById('previewSourceTable').textContent = getSelectedSourceTableText() || '—';
    document.getElementById('previewDescription').textContent = description || '—';

    const modal = new bootstrap.Modal(document.getElementById('documentTemplatePreviewModal'));
    modal.show();

    if (selectedTemplateFileObjectUrl) {
        URL.revokeObjectURL(selectedTemplateFileObjectUrl);
        selectedTemplateFileObjectUrl = null;
    }

    if (selectedFile) {
        const name = selectedFile.name || '';
        const ext = name.includes('.') ? name.split('.').pop().toLowerCase() : '';
        setPreviewMeta(name, 'New upload');

        if (ext === 'pdf') {
            selectedTemplateFileObjectUrl = URL.createObjectURL(selectedFile);
            renderPdfPreview(selectedTemplateFileObjectUrl);
            return;
        }

        if (ext === 'docx') {
            await renderDocxPreviewFromFile(selectedFile);
            return;
        }

        renderNoPreview('Preview not available for this file type.');
        return;
    }

    if (currentSourceFileUrl) {
        const parts = currentSourceFileUrl.split('?')[0].split('/');
        const fileName = parts.length ? parts[parts.length - 1] : 'Current file';
        const ext = fileName.includes('.') ? fileName.split('.').pop().toLowerCase() : '';

        setPreviewMeta(fileName, 'Saved template file');

        if (ext === 'pdf') {
            renderPdfPreview(currentSourceFileUrl);
            return;
        }

        if (ext === 'docx') {
            await renderDocxPreviewFromUrl(currentSourceFileUrl);
            return;
        }

        renderNoPreview('Preview not available for this file type.');
        return;
    }

    setPreviewMeta('', '');
    renderNoPreview('No file selected or attached.');
}

document.addEventListener('dragstart', function (e) {
    const pill = e.target.closest('.variable-pill');
    if (!pill) return;
    e.dataTransfer.setData('text/plain', pill.getAttribute('data-variable') || '');
});

document.addEventListener('click', function (e) {
    const pill = e.target.closest('.variable-pill');
    if (!pill) return;
    insertAtCursor(document.getElementById('file_name_pattern'), pill.getAttribute('data-variable') || '');
});

document.addEventListener('DOMContentLoaded', function () {
    enableDropTarget(document.getElementById('file_name_pattern'));
    document.getElementById('source_table').addEventListener('change', updateAvailableVars);
    document.getElementById('previewDocumentTemplateBtn').addEventListener('click', function () {
        buildDocumentPreview();
    });
    updateAvailableVars();
});

window.addEventListener('beforeunload', function () {
    if (selectedTemplateFileObjectUrl) {
        URL.revokeObjectURL(selectedTemplateFileObjectUrl);
    }
});
</script>

<?= $this->endSection() ?>