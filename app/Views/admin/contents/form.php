<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$tagsValue = '';
if (!empty($content['tags'])) {
    $decoded = json_decode($content['tags'], true);
    $tagsValue = is_array($decoded) ? implode(', ', $decoded) : $content['tags'];
}

$validityStart = !empty($content['validity_date_start']) ? date('Y-m-d\TH:i', strtotime($content['validity_date_start'])) : '';
$validityEnd   = !empty($content['validity_date_end']) ? date('Y-m-d\TH:i', strtotime($content['validity_date_end'])) : '';
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

    textarea.form-control {
        min-height: 110px;
    }

    .preview-box {
        min-height: 220px;
        border: 1px dashed #d0d5dd;
        border-radius: 16px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        padding: 1rem;
    }

    .preview-box img {
        max-width: 100%;
        max-height: 260px;
        border-radius: 14px;
        object-fit: cover;
    }

    .preview-placeholder {
        text-align: center;
        color: #98a2b3;
        font-size: .95rem;
    }

    .subsection-item {
        border: 1px solid #e9ecef;
        border-radius: 18px;
        background: #fff;
        box-shadow: 0 6px 18px rgba(16, 24, 40, 0.04);
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .subsection-item:hover {
        box-shadow: 0 10px 24px rgba(16, 24, 40, 0.08);
    }

    .subsection-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
        padding-bottom: .75rem;
        border-bottom: 1px solid #f1f3f5;
    }

    .subsection-title-wrap {
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .drag-handle {
        width: 34px;
        height: 34px;
        border-radius: 10px;
        border: 1px solid #dee2e6;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: move;
        color: #667085;
        font-size: 1rem;
    }

    .subsection-label {
        font-weight: 700;
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

    .subsection-preview-box {
        min-height: 170px;
        border: 1px dashed #d0d5dd;
        border-radius: 14px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: .75rem;
        overflow: hidden;
    }

    .subsection-preview-box img {
        max-width: 100%;
        max-height: 180px;
        border-radius: 12px;
        object-fit: cover;
    }
</style>

<div class="content-page">
    <div class="page-hero">
        <div>
            <div class="badge-soft mb-2">
                <span>CMS</span>
                <span><?= esc($mode === 'edit' ? 'Edit Mode' : 'Create Mode') ?></span>
            </div>
            <h2 class="page-title"><?= esc($mode === 'edit' ? 'Edit Content' : 'Create Content') ?></h2>
            <p class="page-subtitle">Manage content details, media, and subsections in one place.</p>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/contents') ?>">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Content Details</h3>
                <div class="section-subtitle">Core information for this content item</div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">Parent Content</label>
                        <select class="form-select" name="main_content_id">
                            <option value="">Top Level</option>
                            <?php foreach (($parentContentOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string) $optValue) ?>" <?= (string) old('main_content_id', $content['main_content_id'] ?? ($defaultParentId ?? '')) === (string) $optValue ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Module <span class="text-danger">*</span></label>
                        <select class="form-select" name="module_id" required>
                            <option value="">Select...</option>
                            <?php foreach (($moduleOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string) $optValue) ?>" <?= (string) old('module_id', $content['module_id'] ?? '') === (string) $optValue ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id">
                            <option value="">Select...</option>
                            <?php foreach (($categoryOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string) $optValue) ?>" <?= (string) old('category_id', $content['category_id'] ?? '') === (string) $optValue ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-7">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" value="<?= esc(old('name', $content['name'] ?? '')) ?>" required>
                    </div>

                    <div class="col-12 col-lg-5">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug" value="<?= esc(old('slug', $content['slug'] ?? '')) ?>" required>
                        <div class="meta-note mt-1">Auto-filled from title, but still editable.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3"><?= esc(old('description', $content['description'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Body</label>
                        <textarea class="form-control" name="body" rows="8"><?= esc(old('body', $content['body'] ?? '')) ?></textarea>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">External Link</label>
                        <input type="text" class="form-control" name="external_link" value="<?= esc(old('external_link', $content['external_link'] ?? '')) ?>" placeholder="https://example.com/page">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Rank</label>
                        <input type="number" class="form-control" name="rank" value="<?= esc(old('rank', $content['rank'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status_id">
                            <option value="">Select...</option>
                            <?php foreach (($statusOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string) $optValue) ?>" <?= (string) old('status_id', $content['status_id'] ?? '') === (string) $optValue ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Tags</label>
                        <input type="text" class="form-control" name="tags" value="<?= esc(old('tags', $tagsValue)) ?>" placeholder="news, promo, homepage">
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Approver Matrix</label>
                        <select class="form-select" name="approvermatrix_id">
                            <option value="">Select...</option>
                            <?php foreach (($matrixOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string) $optValue) ?>" <?= (string) old('approvermatrix_id', $content['approvermatrix_id'] ?? '') === (string) $optValue ? 'selected' : '' ?>>
                                    <?= esc((string) $optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Validity Start</label>
                        <input type="datetime-local" class="form-control" name="validity_date_start" value="<?= esc(old('validity_date_start', $validityStart)) ?>">
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Validity End</label>
                        <input type="datetime-local" class="form-control" name="validity_date_end" value="<?= esc(old('validity_date_end', $validityEnd)) ?>">
                    </div>
                </div>
            </div>
        </div>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Media</h3>
                <div class="section-subtitle">Upload an image or use a hosted URL</div>
            </div>

            <div class="card-body">
                <div class="row g-4 align-items-start">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Upload Image</label>
                        <input
                            type="file"
                            class="form-control"
                            name="image_file"
                            accept="image/*"
                            onchange="previewMainUpload(this)"
                        >
                        <div class="meta-note mt-2">Accepted: JPG, PNG, WEBP, GIF</div>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Image URL</label>
                        <input
                            type="text"
                            class="form-control"
                            name="image_url"
                            value="<?= esc(old('image_url', $content['image_url'] ?? '')) ?>"
                            placeholder="https://example.com/image.jpg"
                            oninput="previewMainUrl(this)"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">Preview</label>
                        <div class="preview-box">
                            <img
                                id="main-image-preview"
                                src="<?= !empty($content['image_path']) ? site_url($content['image_path']) : (!empty($content['image_url']) ? esc($content['image_url']) : '') ?>"
                                alt="Main Image Preview"
                                class="<?= empty($content['image_path']) && empty($content['image_url']) ? 'd-none' : '' ?>"
                            >
                            <div id="main-image-placeholder" class="preview-placeholder <?= !empty($content['image_path']) || !empty($content['image_url']) ? 'd-none' : '' ?>">
                                <i class="bi bi-image fs-2 d-block mb-2"></i>
                                No image selected yet
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($mode === 'create' || empty($content['main_content_id'])): ?>
            <div class="modern-card">
                <div class="section-header d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="section-title">Subsections</h3>
                        <div class="section-subtitle">Add, reorder, and manage child content blocks</div>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="addSubsection()">
                        <i class="bi bi-plus-lg me-1"></i> Add Subsection
                    </button>
                </div>

                <div class="card-body">
                    <div id="subsection-container">
                        <?php if (!empty($subsections ?? [])): ?>
                            <?php foreach ($subsections as $i => $sub): ?>
                                <div class="subsection-item p-3 mb-3" draggable="true">
                                    <input type="hidden" name="subsections[<?= $i ?>][id]" value="<?= esc($sub['id']) ?>">
                                    <input type="hidden" name="subsections[<?= $i ?>][existing_image_path]" value="<?= esc($sub['image_path'] ?? '') ?>">

                                    <div class="subsection-toolbar">
                                        <div class="subsection-title-wrap">
                                            <span class="drag-handle" title="Drag to reorder">⋮⋮</span>
                                            <div class="subsection-label">Subsection</div>
                                        </div>

                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSubsection(this)">
                                            <i class="bi bi-trash3 me-1"></i> Remove
                                        </button>
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label">Title</label>
                                            <input
                                                type="text"
                                                name="subsections[<?= $i ?>][name]"
                                                class="form-control"
                                                value="<?= esc($sub['name'] ?? '') ?>"
                                                placeholder="Title"
                                                oninput="autoSlugSubsection(this)"
                                                required
                                            >
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label class="form-label">Slug</label>
                                            <input
                                                type="text"
                                                name="subsections[<?= $i ?>][slug]"
                                                class="form-control"
                                                value="<?= esc($sub['slug'] ?? '') ?>"
                                                placeholder="Slug"
                                            >
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea
                                                name="subsections[<?= $i ?>][description]"
                                                class="form-control"
                                                placeholder="Description"
                                            ><?= esc($sub['description'] ?? '') ?></textarea>
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Body</label>
                                            <textarea
                                                name="subsections[<?= $i ?>][body]"
                                                class="form-control"
                                                placeholder="Body"
                                            ><?= esc($sub['body'] ?? '') ?></textarea>
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label class="form-label">Upload Image</label>
                                            <input
                                                type="file"
                                                name="subsection_image_file_<?= $i ?>"
                                                class="form-control"
                                                accept="image/*"
                                                onchange="previewSubsectionUpload(this)"
                                            >
                                        </div>

                                        <div class="col-12 col-lg-6">
                                            <label class="form-label">Image URL</label>
                                            <input
                                                type="text"
                                                name="subsections[<?= $i ?>][image_url]"
                                                class="form-control"
                                                value="<?= esc($sub['image_url'] ?? '') ?>"
                                                placeholder="https://example.com/image.jpg"
                                                oninput="previewSubsectionUrl(this)"
                                            >
                                        </div>

                                        <div class="col-12 col-lg-4">
                                            <label class="form-label">Rank</label>
                                            <input
                                                type="hidden"
                                                name="subsections[<?= $i ?>][rank]"
                                                class="subsection-rank"
                                                value="<?= esc($sub['rank'] ?? ($i + 1)) ?>"
                                            >
                                            <div class="form-control bg-light">Auto-managed by drag and drop</div>
                                        </div>

                                        <div class="col-12 col-lg-8">
                                            <label class="form-label">Preview</label>
                                            <div class="subsection-preview-box">
                                                <img
                                                    src="<?= !empty($sub['image_path']) ? site_url($sub['image_path']) : (!empty($sub['image_url']) ? esc($sub['image_url']) : '') ?>"
                                                    class="subsection-preview <?= empty($sub['image_path']) && empty($sub['image_url']) ? 'd-none' : '' ?>"
                                                    alt="Subsection Image Preview"
                                                >
                                                <div class="preview-placeholder <?= !empty($sub['image_path']) || !empty($sub['image_url']) ? 'd-none' : '' ?>">
                                                    No preview yet
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div id="empty-subsection-state" class="preview-box">
                                <div class="preview-placeholder">
                                    <i class="bi bi-layout-text-window-reverse fs-2 d-block mb-2"></i>
                                    No subsections yet. Add one to get started.
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="sticky-actions">
            <div class="sticky-actions-inner">
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/contents') ?>">Cancel</a>
                <button class="btn btn-primary px-4" type="submit">
                    <i class="bi bi-check-lg me-1"></i> Save Content
                </button>
            </div>
        </div>
    </form>
</div>

<script>
let subsectionIndex = <?= !empty($subsections) ? count($subsections) : 0 ?>;
let slugEdited = false;
let draggedItem = null;

function generateSlug(text) {
    return text
        .toLowerCase()
        .trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
}

document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.querySelector('input[name="name"]');
    const slugInput = document.querySelector('input[name="slug"]');

    if (slugInput) {
        slugInput.addEventListener('input', function () {
            slugEdited = this.value.trim() !== '';
        });
    }

    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function () {
            if (!slugEdited) {
                slugInput.value = generateSlug(this.value);
            }
        });
    }

    initDragAndDrop();
    refreshSubsectionRanks();
});

function autoSlugSubsection(input) {
    const wrapper = input.closest('.subsection-item');
    const slugInput = wrapper.querySelector('input[name*="[slug]"]');
    if (!slugInput) return;

    if (!slugInput.dataset.edited || slugInput.value.trim() === '') {
        slugInput.value = generateSlug(input.value);
    }
}

document.addEventListener('input', function (e) {
    if (e.target.name && e.target.name.includes('[slug]')) {
        e.target.dataset.edited = 'true';
    }
});

function toggleMainPlaceholder(showImage) {
    const preview = document.getElementById('main-image-preview');
    const placeholder = document.getElementById('main-image-placeholder');
    if (!preview || !placeholder) return;

    preview.classList.toggle('d-none', !showImage);
    placeholder.classList.toggle('d-none', showImage);
}

function addSubsection() {
    const container = document.getElementById('subsection-container');
    const emptyState = document.getElementById('empty-subsection-state');
    if (emptyState) emptyState.remove();

    const html = `
        <div class="subsection-item p-3 mb-3" draggable="true">
            <div class="subsection-toolbar">
                <div class="subsection-title-wrap">
                    <span class="drag-handle" title="Drag to reorder">⋮⋮</span>
                    <div class="subsection-label">Subsection</div>
                </div>

                <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSubsection(this)">
                    <i class="bi bi-trash3 me-1"></i> Remove
                </button>
            </div>

            <div class="row g-3">
                <div class="col-12 col-lg-6">
                    <label class="form-label">Title</label>
                    <input type="text"
                           name="subsections[${subsectionIndex}][name]"
                           class="form-control"
                           placeholder="Title"
                           oninput="autoSlugSubsection(this)"
                           required>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Slug</label>
                    <input type="text"
                           name="subsections[${subsectionIndex}][slug]"
                           class="form-control"
                           placeholder="Slug">
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="subsections[${subsectionIndex}][description]"
                              class="form-control"
                              placeholder="Description"></textarea>
                </div>

                <div class="col-12">
                    <label class="form-label">Body</label>
                    <textarea name="subsections[${subsectionIndex}][body]"
                              class="form-control"
                              placeholder="Body"></textarea>
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Upload Image</label>
                    <input type="file"
                           name="subsection_image_file_${subsectionIndex}"
                           class="form-control"
                           accept="image/*"
                           onchange="previewSubsectionUpload(this)">
                </div>

                <div class="col-12 col-lg-6">
                    <label class="form-label">Image URL</label>
                    <input type="text"
                           name="subsections[${subsectionIndex}][image_url]"
                           class="form-control"
                           placeholder="https://example.com/image.jpg"
                           oninput="previewSubsectionUrl(this)">
                </div>

                <div class="col-12 col-lg-4">
                    <label class="form-label">Rank</label>
                    <input type="hidden"
                           name="subsections[${subsectionIndex}][rank]"
                           class="subsection-rank"
                           value="${subsectionIndex + 1}">
                    <div class="form-control bg-light">Auto-managed by drag and drop</div>
                </div>

                <div class="col-12 col-lg-8">
                    <label class="form-label">Preview</label>
                    <div class="subsection-preview-box">
                        <img class="subsection-preview d-none" alt="Subsection Image Preview">
                        <div class="preview-placeholder">No preview yet</div>
                    </div>
                </div>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', html);
    subsectionIndex++;
    initDragAndDrop();
    refreshSubsectionRanks();
}

function removeSubsection(button) {
    const item = button.closest('.subsection-item');
    if (item) item.remove();

    refreshSubsectionRanks();

    const container = document.getElementById('subsection-container');
    const items = container.querySelectorAll('.subsection-item');
    if (items.length === 0) {
        container.innerHTML = `
            <div id="empty-subsection-state" class="preview-box">
                <div class="preview-placeholder">
                    <i class="bi bi-layout-text-window-reverse fs-2 d-block mb-2"></i>
                    No subsections yet. Add one to get started.
                </div>
            </div>
        `;
    }
}

function previewMainUpload(input) {
    const preview = document.getElementById('main-image-preview');
    const file = input.files[0];
    if (!file || !preview) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        toggleMainPlaceholder(true);
    };
    reader.readAsDataURL(file);
}

function previewMainUrl(input) {
    const preview = document.getElementById('main-image-preview');
    const url = input.value.trim();
    if (!preview) return;

    if (url !== '') {
        preview.src = url;
        toggleMainPlaceholder(true);
    } else {
        preview.src = '';
        toggleMainPlaceholder(false);
    }
}

function previewSubsectionUpload(input) {
    const wrapper = input.closest('.subsection-item');
    const preview = wrapper.querySelector('.subsection-preview');
    const placeholder = wrapper.querySelector('.preview-placeholder');
    const file = input.files[0];
    if (!file || !preview) return;

    const reader = new FileReader();
    reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('d-none');
        if (placeholder) placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
}

function previewSubsectionUrl(input) {
    const wrapper = input.closest('.subsection-item');
    const preview = wrapper.querySelector('.subsection-preview');
    const placeholder = wrapper.querySelector('.preview-placeholder');
    const url = input.value.trim();

    if (url !== '') {
        preview.src = url;
        preview.classList.remove('d-none');
        if (placeholder) placeholder.classList.add('d-none');
    } else {
        preview.src = '';
        preview.classList.add('d-none');
        if (placeholder) placeholder.classList.remove('d-none');
    }
}

function refreshSubsectionRanks() {
    const items = document.querySelectorAll('#subsection-container .subsection-item');
    items.forEach((item, index) => {
        const rankInput = item.querySelector('.subsection-rank');
        if (rankInput) {
            rankInput.value = index + 1;
        }
    });
}

function initDragAndDrop() {
    const items = document.querySelectorAll('#subsection-container .subsection-item');

    items.forEach(item => {
        item.removeEventListener('dragstart', handleDragStart);
        item.removeEventListener('dragover', handleDragOver);
        item.removeEventListener('drop', handleDrop);
        item.removeEventListener('dragend', handleDragEnd);

        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragover', handleDragOver);
        item.addEventListener('drop', handleDrop);
        item.addEventListener('dragend', handleDragEnd);
    });
}

function handleDragStart(e) {
    draggedItem = e.currentTarget;
    e.currentTarget.classList.add('opacity-50');
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDrop(e) {
    e.preventDefault();
    const targetItem = e.currentTarget;

    if (!draggedItem || draggedItem === targetItem) return;

    const container = document.getElementById('subsection-container');
    const items = Array.from(container.querySelectorAll('.subsection-item'));
    const draggedIndex = items.indexOf(draggedItem);
    const targetIndex = items.indexOf(targetItem);

    if (draggedIndex < targetIndex) {
        container.insertBefore(draggedItem, targetItem.nextSibling);
    } else {
        container.insertBefore(draggedItem, targetItem);
    }

    refreshSubsectionRanks();
}

function handleDragEnd(e) {
    e.currentTarget.classList.remove('opacity-50');
    draggedItem = null;
}
</script>

<?= $this->endSection() ?>