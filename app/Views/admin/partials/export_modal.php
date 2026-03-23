<?php
$modalId = $modalId ?? 'exportModal';
$exportUrl = $exportUrl ?? '';
$entity = $entity ?? '';
?>

<div class="modal fade" id="<?= esc($modalId) ?>" tabindex="-1">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">

      <form method="get" action="<?= site_url($exportUrl) ?>" id="exportForm">
        
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-download me-2"></i> Export Data
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">

          <!-- FORMAT -->
          <div class="mb-3">
            <label class="form-label">Format</label>
            <select name="format" class="form-select">
              <option value="csv">CSV</option>
              <option value="xlsx">Excel (XLSX)</option>
            </select>
          </div>

          <!-- COLUMNS -->
          <div class="mb-3">
            <label class="form-label">Columns</label>
            <div id="exportColumnsWrap" class="border rounded p-2" style="max-height:200px; overflow:auto;">
              <div class="text-muted small">Loading columns...</div>
            </div>
          </div>

          <!-- PREVIEW -->
          <div class="mb-2 d-flex justify-content-between align-items-center">
            <label class="form-label mb-0">Preview</label>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="loadExportPreview()">
              <i class="bi bi-eye"></i> Preview
            </button>
          </div>

          <div class="table-responsive border rounded">
            <table class="table table-sm mb-0">
              <thead class="table-light" id="exportPreviewHead"></thead>
              <tbody id="exportPreviewBody">
                <tr><td class="text-muted text-center p-3">No preview yet</td></tr>
              </tbody>
            </table>
          </div>

        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-dark">
            <i class="bi bi-download"></i> Export
          </button>
        </div>

      </form>

    </div>
  </div>
</div>

<script>
const exportEntity = "<?= esc($entity) ?>";

function loadExportColumns() {
  fetch("<?= site_url('admin/export-columns/') ?>" + exportEntity)
    .then(res => res.json())
    .then(data => {
      if (!data.status) return;

      const wrap = document.getElementById('exportColumnsWrap');
      wrap.innerHTML = '';

      data.columns.forEach(col => {
        wrap.innerHTML += `
          <div class="form-check">
            <input class="form-check-input"
                   type="checkbox"
                   name="columns[]"
                   value="${col}"
                   checked>
            <label class="form-check-label">${col}</label>
          </div>
        `;
      });
    });
}

function loadExportPreview() {
  const form = document.getElementById('exportForm');
  const params = new URLSearchParams(new FormData(form));

  fetch("<?= site_url('admin/export-preview/') ?>" + exportEntity + "?" + params.toString())
    .then(res => res.json())
    .then(data => {
      if (!data.status) return;

      // header
      let head = '<tr>';
      data.columns.forEach(c => head += `<th>${c}</th>`);
      head += '</tr>';
      document.getElementById('exportPreviewHead').innerHTML = head;

      // body
      let body = '';
      data.rows.forEach(row => {
        body += '<tr>';
        data.columns.forEach(c => {
          body += `<td>${row[c] ?? ''}</td>`;
        });
        body += '</tr>';
      });

      document.getElementById('exportPreviewBody').innerHTML = body;
    });
}

document.addEventListener('DOMContentLoaded', loadExportColumns);
</script>