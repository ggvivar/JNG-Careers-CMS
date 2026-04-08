<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
.template-preview-box{
    min-height:120px;
    border:1px dashed #d0d5dd;
    border-radius:14px;
    background:#f8fafc;
    padding:1rem;
}
</style>

<?= view('admin/partials/table_template', [
    'title' => 'Message Templates',
    'subtitle' => 'Manage reusable email and sms templates',
    'featureCode' => 'message-templates',
    'createUrl' => 'admin/message-templates/create',
    'createLabel' => '+ Create Message Template',
    'searchQuery' => $searchQuery ?? '',
    'searchPlaceholder' => 'Search message templates...',
    'paginationLinks' => $paginationLinks ?? '',
    'rows' => $templates ?? [],
    'columns' => [
        ['key' => 'id', 'label' => 'ID', 'style' => 'width:80px;'],

        [
            'key' => 'name',
            'label' => 'Name',
            'formatter' => fn($row,$value) =>
                '<span class="fw-semibold">'.esc($value).'</span>'
        ],

        ['key' => 'template_key', 'label' => 'Key'],

        [
            'key' => 'channel',
            'label' => 'Channel',
            'formatter' => function ($row,$value) {
                $value = strtolower((string)$value);
                $badge = $value === 'sms' ? 'text-bg-warning' : 'text-bg-primary';
                return '<span class="badge '.$badge.'">'.esc(strtoupper($value)).'</span>';
            }
        ],

        [
            'key' => 'source_table',
            'label' => 'Source',
            'formatter' => fn($row,$value) =>
                $value ? esc(ucwords(str_replace('_',' ',$value))) : '<span class="text-muted">—</span>'
        ],

        [
            'key' => 'subject',
            'label' => 'Subject',
            'formatter' => fn($row,$value) =>
                trim($value) ? esc($value) : '<span class="text-muted">—</span>'
        ],

        [
            'key' => 'body_template',
            'label' => 'Preview',
            'formatter' => function ($row,$value){
                $text = trim(preg_replace('/\s+/',' ',strip_tags($value)));
                return $text
                    ? esc(mb_strlen($text)>90 ? mb_substr($text,0,90).'...' : $text)
                    : '<span class="text-muted">—</span>';
            }
        ],

        [
            'key' => 'status_name',
            'label' => 'Status',
            'formatter' => fn($row,$value) =>
                trim($value) ? esc($value) : '<span class="text-muted">—</span>'
        ],
    ],

    'actions' => [
        [
            'label' => 'Preview',
            'class' => 'btn btn-outline-secondary js-preview-template',
            'icon' => 'bi-eye',
            'custom' => true,
            'data' => fn($row) => [
                'subject' => $row['subject'] ?? '',
                'body' => $row['body_template'] ?? '',
                'channel' => $row['channel'] ?? 'email',
            ],
        ],

        [
            'label' => 'Edit',
            'permission' => 'can_edit',
            'class' => 'btn btn-outline-primary',
            'method' => 'get',
            'icon' => 'bi-pencil',
            'url' => 'admin/message-templates/edit/',
        ],

        [
            'label' => 'Delete',
            'permission' => 'can_delete',
            'class' => 'btn btn-outline-danger',
            'method' => 'post',
            'icon' => 'bi-trash',
            'url' => 'admin/message-templates/delete/',
            'confirm' => 'Delete this template?',
        ],
    ],
]) ?>

<!-- ✅ Preview Modal -->
<div class="modal fade" id="previewModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header">
                <h5 class="modal-title">Template Preview</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <div class="small text-muted">Subject</div>
                    <div class="template-preview-box" id="previewSubject"></div>
                </div>

                <div>
                    <div class="small text-muted">Body</div>
                    <div class="template-preview-box" id="previewBody"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function escapeHtml(text){
    return String(text||'')
        .replace(/&/g,"&amp;")
        .replace(/</g,"&lt;")
        .replace(/>/g,"&gt;");
}

document.addEventListener('click', function(e){
    const btn = e.target.closest('.js-preview-template');
    if(!btn) return;

    const subject = btn.dataset.subject || '';
    const body = btn.dataset.body || '';
    const channel = (btn.dataset.channel || 'email').toLowerCase();

    const subjectBox = document.getElementById('previewSubject');
    const bodyBox = document.getElementById('previewBody');

    if(channel === 'sms'){
        subjectBox.innerHTML = '<span class="text-muted">No subject</span>';
        bodyBox.innerHTML = '<div style="white-space:pre-wrap;">'+escapeHtml(body)+'</div>';
    }else{
        subjectBox.textContent = subject || '—';
        bodyBox.innerHTML = body || '<span class="text-muted">—</span>';
    }

    new bootstrap.Modal(document.getElementById('previewModal')).show();
});
</script>

<?= $this->endSection() ?>