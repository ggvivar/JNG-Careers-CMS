<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0"><?= $mode === 'edit' ? 'Edit Message Template' : 'Create Message Template' ?></h3>
    <div class="text-muted">Reusable email or sms template</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/message-templates') ?>">Back</a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="post" id="messageTemplateForm">
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
          <label class="form-label">Channel</label>
          <select name="channel" id="channel" class="form-select" required>
            <?php foreach (($channelOptions ?? []) as $value => $label): ?>
              <option value="<?= esc($value) ?>" <?= (string) old('channel', $template['channel'] ?? 'email') === (string) $value ? 'selected' : '' ?>>
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

        <div class="col-12" id="subjectWrap">
          <label class="form-label">Subject</label>
          <input type="text" name="subject" id="subject" class="form-control drop-target" data-drop-target="subject" value="<?= esc(old('subject', $template['subject'] ?? '')) ?>">
          <div class="form-text">Drop variables here for email subject.</div>
        </div>
      </div>

      <div id="variableTrayWrap" class="mt-3"></div>

      <div class="mt-3">
        <label class="form-label">Body Template</label>

        <div id="emailEditorWrap">
          <div class="border rounded p-2 mb-2 small text-muted">Drag variables into the editor or click them.</div>
          <textarea name="body_template" id="body_template" class="form-control"><?= esc(old('body_template', $template['body_template'] ?? '')) ?></textarea>
        </div>

        <div id="smsEditorWrap" style="display:none;">
          <textarea name="body_template_sms" id="body_template_sms" class="form-control drop-target" data-drop-target="body_template_sms" rows="8"><?= esc(old('body_template', $template['body_template'] ?? '')) ?></textarea>
          <div class="form-text">Drop variables here for SMS body.</div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-dark" type="submit">Save</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/message-templates') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.2.0/classic/ckeditor.js"></script>
<script>
  window.TemplateEditors = window.TemplateEditors || {};
  const variableMap = <?= json_encode($variableMap ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
  let messageEditor = null;

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
          <div class="form-text mt-2">Drag a variable into a field, or click a variable to insert it.</div>
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

  function syncSmsToMain() {
    const sms = document.getElementById('body_template_sms');
    const main = document.getElementById('body_template');
    if (sms && main) {
      main.value = sms.value;
    }
  }

  function toggleTemplateMode() {
    const channel = document.getElementById('channel').value;
    const emailWrap = document.getElementById('emailEditorWrap');
    const smsWrap = document.getElementById('smsEditorWrap');
    const subjectWrap = document.getElementById('subjectWrap');
    const main = document.getElementById('body_template');
    const sms = document.getElementById('body_template_sms');

    if (channel === 'sms') {
      if (messageEditor) {
        main.value = messageEditor.getData();
      }
      sms.value = main.value;
      emailWrap.style.display = 'none';
      smsWrap.style.display = '';
      subjectWrap.style.display = 'none';
    } else {
      syncSmsToMain();
      emailWrap.style.display = '';
      smsWrap.style.display = 'none';
      subjectWrap.style.display = '';
      if (messageEditor) {
        messageEditor.setData(main.value || '');
      }
    }
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

    const variable = pill.getAttribute('data-variable') || '';
    const channel = document.getElementById('channel').value;

    if (channel === 'sms') {
      insertAtCursor(document.getElementById('body_template_sms'), variable);
    } else {
      if (window.TemplateEditors['body_template']) {
        const editor = window.TemplateEditors['body_template'];
        editor.model.change(writer => {
          const insertPosition = editor.model.document.selection.getFirstPosition();
          writer.insertText(variable, insertPosition);
        });
        editor.editing.view.focus();
      }
    }
  });

  document.addEventListener('DOMContentLoaded', function () {
    enableDropTarget(document.getElementById('subject'));
    enableDropTarget(document.getElementById('body_template_sms'));

    ClassicEditor
      .create(document.querySelector('#body_template'))
      .then(editor => {
        messageEditor = editor;
        window.TemplateEditors['body_template'] = editor;

        const editable = editor.ui.view.editable.element;
        editable.addEventListener('dragover', function (e) {
          e.preventDefault();
        });

        editable.addEventListener('drop', function (e) {
          e.preventDefault();
          const variable = e.dataTransfer.getData('text/plain');
          editor.model.change(writer => {
            const insertPosition = editor.model.document.selection.getFirstPosition();
            writer.insertText(variable, insertPosition);
          });
          editor.editing.view.focus();
        });

        document.getElementById('source_table').addEventListener('change', updateAvailableVars);
        document.getElementById('channel').addEventListener('change', toggleTemplateMode);

        document.getElementById('messageTemplateForm').addEventListener('submit', function () {
          if (document.getElementById('channel').value === 'sms') {
            syncSmsToMain();
          } else {
            document.getElementById('body_template').value = editor.getData();
          }
        });

        updateAvailableVars();
        toggleTemplateMode();
      })
      .catch(error => console.error(error));
  });

  
</script>

<?= $this->endSection() ?>