<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0"><?= $mode === 'edit' ? 'Edit Document Template' : 'Create Document Template' ?></h3>
    <div class="text-muted">Document rendering comes from the attached template file</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/document-templates') ?>">Back</a>
</div>

<?php if (!empty($template['source_file_path'])): ?>
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="fw-semibold mb-2">Current Source File</div>
      <a href="<?= site_url($template['source_file_path']) ?>" target="_blank"><?= esc(basename($template['source_file_path'])) ?></a>
    </div>
  </div>
<?php endif; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" enctype="multipart/form-data" id="documentTemplateForm">
      <?= csrf_field() ?>

      <input type="hidden" name="available_vars" id="available_vars" value="<?= esc(old('available_vars', $template['available_vars'] ?? '')) ?>">

      <div class="row g-3">
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
          <input type="text" name="file_name_pattern" id="file_name_pattern" class="form-control drop-target" data-drop-target="file_name_pattern" value="<?= esc(old('file_name_pattern', $template['file_name_pattern'] ?? '')) ?>" placeholder="Applicant-{{name}}-Offer-Letter">
          <div class="form-text">Drag variables here.</div>
        </div>

        <div class="col-12 col-lg-4">
          <label class="form-label">Source Template File</label>
          <input type="file" name="source_file" class="form-control">
        </div>
        <div class="col-12">
          <div id="variableTrayWrap"></div>
        </div>

        <div class="col-12">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="3"><?= esc(old('description', $template['description'] ?? '')) ?></textarea>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-dark" type="submit">Save</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/document-templates') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script>
  const variableMap = <?= json_encode($variableMap ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;

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
      wrap.innerHTML = '';
      return;
    }

    let html = `
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="fw-semibold mb-2">Variables</div>
          <div class="d-flex flex-wrap gap-2">
    `;

    vars.forEach(variable => {
      html += `
        <span
          class="badge rounded-pill text-bg-secondary variable-pill"
          draggable="true"
          data-variable="${variable.replace(/"/g, '&quot;')}"
          style="cursor:grab;"
        >
          ${variable}
        </span>
      `;
    });

    html += `
          </div>
          <div class="form-text mt-2">Drag a variable into File Name Pattern, or click to insert.</div>
        </div>
      </div>
    `;

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
    updateAvailableVars();
  });
</script>

<?= $this->endSection() ?>