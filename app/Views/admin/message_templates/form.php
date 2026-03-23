<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

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

    .ck-editor__editable_inline {
        min-height: 260px;
    }

    .editor-shell {
        border: 1px solid #dbe1e7;
        border-radius: 16px;
        overflow: hidden;
        background: #fff;
    }

    .editor-note {
        padding: .75rem 1rem;
        border-bottom: 1px solid #edf0f2;
        background: #f8fafc;
        color: #667085;
        font-size: .85rem;
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
        min-height: 220px;
        border: 1px dashed #d0d5dd;
        border-radius: 16px;
        background: #f8fafc;
        padding: 1rem;
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
            <h2 class="page-title"><?= esc($mode === 'edit' ? 'Edit Message Template' : 'Create Message Template') ?></h2>
            <p class="page-subtitle">Manage reusable email and SMS templates with drag-and-drop variables.</p>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/message-templates') ?>">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form method="post" id="messageTemplateForm">
        <?= csrf_field() ?>
        <input type="hidden" name="available_vars" id="available_vars" value="<?= esc(old('available_vars', $template['available_vars'] ?? '')) ?>">

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Template Details</h3>
                <div class="section-subtitle">Basic information and delivery settings</div>
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
                        <div class="meta-note mt-1">Use a unique system key like <code>application_update</code>.</div>
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
                        <input type="text" name="subject" id="subject" class="form-control drop-target" value="<?= esc(old('subject', $template['subject'] ?? '')) ?>">
                        <div class="meta-note mt-1">Available for email templates only. You can drag variables into this field.</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Variables</h3>
                <div class="section-subtitle">Click or drag placeholders from the selected source</div>
            </div>

            <div class="card-body">
                <div id="variableTrayWrap"></div>
            </div>
        </div>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Template Body</h3>
                <div class="section-subtitle">Compose the actual email or SMS content</div>
            </div>

            <div class="card-body">
                <div id="emailEditorWrap">
                    <div class="editor-shell">
                        <div class="editor-note">Click a variable to insert it into the email editor. Drag-and-drop is enabled for standard input fields only.</div>
                        <textarea name="body_template" id="body_template" class="form-control"><?= esc(old('body_template', $template['body_template'] ?? '')) ?></textarea>
                    </div>
                </div>

                <div id="smsEditorWrap" style="display:none;">
                    <label class="form-label mt-1">SMS Body</label>
                    <textarea name="body_template_sms" id="body_template_sms" class="form-control drop-target" rows="10"><?= esc(old('body_template_sms', $template['body_template'] ?? '')) ?></textarea>
                    <div class="meta-note mt-1">Drag variables here for SMS content.</div>
                </div>
            </div>
        </div>

        <div class="sticky-actions">
            <div class="sticky-actions-inner">
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/message-templates') ?>">Cancel</a>
                <button class="btn btn-outline-primary" type="button" id="previewMessageTemplateBtn">
                    <i class="bi bi-eye me-1"></i> Preview
                </button>
                <button class="btn btn-primary px-4" type="submit">
                    <i class="bi bi-check-lg me-1"></i> Save Template
                </button>
            </div>
        </div>
    </form>
</div>

<div class="modal fade" id="messageTemplatePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content" style="border-radius:18px;">
            <div class="modal-header">
                <h5 class="modal-title">Template Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3" id="previewSubjectWrap">
                    <div class="small text-muted mb-1">Subject</div>
                    <div class="preview-box bg-light" id="previewSubject"></div>
                </div>

                <div>
                    <div class="small text-muted mb-1">Body</div>
                    <div class="preview-box bg-light" id="previewBody"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
window.TemplateEditors = window.TemplateEditors || {};
const variableMap = <?= json_encode($variableMap ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?>;
let messageEditor = null;

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
    html += '</div><div class="meta-note mt-3">Drag variables into input fields or click them to insert into the body editor.</div></div>';

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
    const previewSubjectWrap = document.getElementById('previewSubjectWrap');
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
        previewSubjectWrap.style.display = 'none';
    } else {
        syncSmsToMain();
        emailWrap.style.display = '';
        smsWrap.style.display = 'none';
        subjectWrap.style.display = '';
        previewSubjectWrap.style.display = '';
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

function buildMessagePreview() {
    const channel = document.getElementById('channel').value;
    const subject = document.getElementById('subject').value || '';
    const smsBody = document.getElementById('body_template_sms').value || '';
    const emailBody = messageEditor ? messageEditor.getData() : (document.getElementById('body_template').value || '');

    if (channel === 'sms') {
        document.getElementById('previewSubject').innerHTML = '<span class="text-muted">No subject for SMS</span>';
        document.getElementById('previewBody').innerHTML = '<div style="white-space: pre-wrap;">' + escapeHtml(smsBody) + '</div>';
    } else {
        document.getElementById('previewSubject').textContent = subject || '—';
        document.getElementById('previewBody').innerHTML = emailBody || '<span class="text-muted">No body content</span>';
    }

    const modal = new bootstrap.Modal(document.getElementById('messageTemplatePreviewModal'));
    modal.show();
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
        const editor = window.TemplateEditors['body_template'];
        if (!editor) return;

        editor.model.change(writer => {
            editor.model.insertContent(
                writer.createText(variable),
                editor.model.document.selection
            );
        });

        editor.editing.view.focus();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    enableDropTarget(document.getElementById('subject'));
    enableDropTarget(document.getElementById('body_template_sms'));

    document.getElementById('source_table').addEventListener('change', updateAvailableVars);
    document.getElementById('channel').addEventListener('change', toggleTemplateMode);
    document.getElementById('previewMessageTemplateBtn').addEventListener('click', buildMessagePreview);

    ClassicEditor
        .create(document.querySelector('#body_template'))
        .then(editor => {
            messageEditor = editor;
            window.TemplateEditors['body_template'] = editor;

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