<?php
$modalId   = $modalId ?? 'importModal';
$importUrl = $importUrl ?? '#';
$entity    = $entity ?? '';
?>

<style>
    #<?= esc($modalId) ?> .modal-content {
        border-radius: 16px;
        border: 1px solid #e9ecef;
        overflow: hidden;
    }

    #<?= esc($modalId) ?> .modal-body {
        max-height: 65vh;
        overflow-y: auto;
    }

    #<?= esc($modalId) ?> .mapping-wrap {
        max-height: 260px;
        overflow-y: auto;
        border: 1px solid #dee2e6;
        border-radius: 12px;
        background: #fff;
        padding: .75rem;
    }

    #<?= esc($modalId) ?> .mapping-row {
        border-bottom: 1px solid #f1f3f5;
        padding: .6rem 0;
    }

    #<?= esc($modalId) ?> .mapping-row:last-child {
        border-bottom: 0;
    }

    #<?= esc($modalId) ?> .preview-wrap {
        max-height: 220px;
        overflow: auto;
        border: 1px solid #dee2e6;
        border-radius: 12px;
    }

    #<?= esc($modalId) ?> .modal-footer {
        position: sticky;
        bottom: 0;
        background: #fff;
        border-top: 1px solid #dee2e6;
        z-index: 2;
    }
</style>

<div class="modal fade" id="<?= esc($modalId) ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form method="post" action="<?= site_url($importUrl) ?>" enctype="multipart/form-data" id="<?= esc($modalId) ?>_form">
                <?= csrf_field() ?>

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-upload me-2"></i>Import Data
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Format</label>
                            <select name="format" class="form-select" required>
                                <option value="csv">CSV</option>
                                <option value="xlsx">XLSX</option>
                            </select>
                        </div>

                        <div class="col-md-8">
                            <label class="form-label">File</label>
                            <input type="file" name="import_file" class="form-control" accept=".csv,.xlsx" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="previewImport_<?= esc($modalId) ?>()">
                            <i class="bi bi-eye me-1"></i> Preview Import
                        </button>
                    </div>

                    <div id="<?= esc($modalId) ?>_validation" class="mb-3"></div>

                    <div class="mb-3">
                        <label class="form-label">Column Mapping</label>
                        <div id="<?= esc($modalId) ?>_mapping" class="mapping-wrap">
                            <div class="text-muted small">Upload a file and click Preview Import to load mapping.</div>
                        </div>
                    </div>

                    <div>
                        <label class="form-label">Preview Rows</label>
                        <div class="preview-wrap">
                            <table class="table table-sm mb-0">
                                <thead class="table-light" id="<?= esc($modalId) ?>_preview_head"></thead>
                                <tbody id="<?= esc($modalId) ?>_preview_body">
                                    <tr>
                                        <td class="text-muted text-center p-3">No preview yet</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-upload me-1"></i> Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImport_<?= esc($modalId) ?>() {
    const form = document.getElementById('<?= esc($modalId) ?>_form');
    const formData = new FormData(form);

    fetch("<?= site_url('admin/import-preview') ?>/<?= esc($entity) ?>", {
        method: "POST",
        body: formData,
        headers: { "X-Requested-With": "XMLHttpRequest" }
    })
    .then(res => res.json())
    .then(data => {
        const validationWrap = document.getElementById('<?= esc($modalId) ?>_validation');
        const mappingWrap = document.getElementById('<?= esc($modalId) ?>_mapping');
        const head = document.getElementById('<?= esc($modalId) ?>_preview_head');
        const body = document.getElementById('<?= esc($modalId) ?>_preview_body');

        validationWrap.innerHTML = '';

        if (!data.status) {
            validationWrap.innerHTML = `<div class="alert alert-danger mb-0">${data.message || 'Preview failed.'}</div>`;
            return;
        }

        let validationHtml = '';
        if ((data.invalid_headers || []).length) {
            validationHtml += `<div class="alert alert-danger py-2 mb-2">Invalid columns: ${data.invalid_headers.join(', ')}</div>`;
        }
        if ((data.missing_required_headers || []).length) {
            validationHtml += `<div class="alert alert-warning py-2 mb-2">Missing required columns: ${data.missing_required_headers.join(', ')}</div>`;
        }
        if ((data.row_errors || []).length) {
            validationHtml += `<div class="alert alert-warning py-2 mb-0">${data.row_errors.length} row validation error(s) found.</div>`;
        }
        if (!validationHtml) {
            validationHtml = `<div class="alert alert-success py-2 mb-0">File looks valid and ready to import.</div>`;
        }
        validationWrap.innerHTML = validationHtml;

        const validHeaders = data.valid_headers || [];
        const allHeaders = data.headers || [];

        let mappingHtml = '';
        if (!allHeaders.length) {
            mappingHtml = '<div class="text-muted small">No columns detected.</div>';
        } else {
            allHeaders.forEach(function(header) {
                const safeHeader = String(header).replace(/"/g, '&quot;');
                const options = ['<option value="">-- Ignore --</option>']
                    .concat(validHeaders.map(function(field) {
                        const selected = field === header ? 'selected' : '';
                        return `<option value="${field}" ${selected}>${field}</option>`;
                    }))
                    .join('');

                mappingHtml += `
                    <div class="mapping-row row align-items-center">
                        <div class="col-md-5">
                            <div class="fw-semibold">${safeHeader}</div>
                        </div>
                        <div class="col-md-7">
                            <select name="mapping[${safeHeader}]" class="form-select form-select-sm">
                                ${options}
                            </select>
                        </div>
                    </div>
                `;
            });
        }

        mappingWrap.innerHTML = mappingHtml;

        let headHtml = '';
        if (allHeaders.length) {
            headHtml = '<tr>' + allHeaders.map(function(h) {
                return `<th>${h}</th>`;
            }).join('') + '</tr>';
        }
        head.innerHTML = headHtml;

        let bodyHtml = '';
        const previewRows = data.preview_rows || [];
        if (!previewRows.length) {
            bodyHtml = '<tr><td class="text-muted text-center p-3" colspan="' + Math.max(allHeaders.length, 1) + '">No preview rows</td></tr>';
        } else {
            previewRows.forEach(function(row) {
                bodyHtml += '<tr>';
                allHeaders.forEach(function(h) {
                    bodyHtml += `<td>${row[h] ?? ''}</td>`;
                });
                bodyHtml += '</tr>';
            });
        }
        body.innerHTML = bodyHtml;
    })
    .catch(() => {
        document.getElementById('<?= esc($modalId) ?>_validation').innerHTML =
            '<div class="alert alert-danger mb-0">Unable to preview import.</div>';
    });
}
</script>