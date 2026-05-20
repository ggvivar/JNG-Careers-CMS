<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$tagsValue = '';
if (!empty($content['tags'])) {
    $decoded   = json_decode($content['tags'], true);
    $tagsValue = is_array($decoded) ? implode(', ', $decoded) : $content['tags'];
}

$validityStart = !empty($content['validity_date_start']) ? date('Y-m-d', strtotime($content['validity_date_start'])) : '';
$validityEnd   = !empty($content['validity_date_end'])   ? date('Y-m-d', strtotime($content['validity_date_end']))   : '';

$existingImages = [];
if (!empty($content['images'])) {
    $dec = json_decode($content['images'], true);
    if (is_array($dec)) $existingImages = $dec;
}
if (empty($existingImages)) {
    $existingImages = [[
        'path'    => $content['image_path']        ?? '',
        'url'     => $content['image_url']          ?? '',
        'caption' => $content['image_description']  ?? '',
        'order'   => 1,
    ]];
}
// dd($content);
?>

<style>
    .content-page { max-width: 1200px; margin: 0 auto; }

    .modern-card {
        border: 1px solid #e9ecef; border-radius: 18px;
        box-shadow: 0 8px 24px rgba(16,24,40,.06);
        overflow: hidden; background: #fff;
    }
    .modern-card + .modern-card { margin-top: 1.25rem; }

    .section-header {
        padding: 1rem 1.25rem; border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(180deg,#fff 0%,#fafbfc 100%);
    }
    .section-title    { font-size: 1rem; font-weight: 700; color: #212529; margin: 0; }
    .section-subtitle { font-size: .85rem; color: #6c757d; margin-top: .2rem; }

    .page-hero { display:flex; justify-content:space-between; align-items:flex-start; gap:1rem; margin-bottom:1.25rem; }
    .page-title { font-size:1.6rem; font-weight:800; letter-spacing:-.02em; margin-bottom:.15rem; }
    .page-subtitle { color:#6c757d; margin:0; }

    .badge-soft {
        display:inline-flex; align-items:center; gap:.35rem; font-size:.75rem; font-weight:600;
        color:#0d6efd; background:rgba(13,110,253,.08); border:1px solid rgba(13,110,253,.12);
        padding:.4rem .7rem; border-radius:999px;
    }

    .modern-card .card-body { padding: 1.25rem; }
    .form-label { font-weight:600; color:#344054; margin-bottom:.45rem; }
    .form-control, .form-select {
        border-radius:12px; border-color:#dbe1e7;
        padding-top:.6rem; padding-bottom:.6rem; box-shadow:none !important;
    }
    .form-control:focus, .form-select:focus {
        border-color:#86b7fe;
        box-shadow:0 0 0 .2rem rgba(13,110,253,.12) !important;
    }
    textarea.form-control { min-height:110px; }
    .ck-editor__editable_inline { min-height:180px; }

    .preview-box {
        min-height:220px; border:1px dashed #d0d5dd; border-radius:16px;
        background:#f8fafc; display:flex; align-items:center; justify-content:center;
        overflow:hidden; padding:1rem;
    }
    .preview-box img { max-width:100%; max-height:260px; border-radius:14px; object-fit:cover; }
    .preview-placeholder { text-align:center; color:#98a2b3; font-size:.95rem; }


    .collapsible-item {
        border:1px solid #e9ecef; border-radius:18px; background:#fff;
        box-shadow:0 6px 18px rgba(16,24,40,.04);
        transition:box-shadow .2s ease; overflow:hidden;
    }
    .collapsible-item:hover { box-shadow:0 10px 24px rgba(16,24,40,.08); }
    .collapsible-item + .collapsible-item { margin-top:.75rem; }

    .item-header {
        display:flex; justify-content:space-between; align-items:center;
        padding:.85rem 1rem; background:linear-gradient(180deg,#fff 0%,#fafbfc 100%);
        border-bottom:1px solid #f1f3f5; cursor:pointer; user-select:none;
    }
    .item-header-left   { display:flex; align-items:center; gap:.65rem; }
    .item-header-actions{ display:flex; align-items:center; gap:.5rem; }
    .item-header .item-label        { font-weight:700; color:#344054; font-size:.9rem; }
    .item-header .item-preview-text { font-size:.8rem; color:#98a2b3; margin-left:.25rem; }

    .collapse-toggle {
        width:28px; height:28px; border-radius:8px; border:1px solid #dee2e6;
        background:#fff; display:inline-flex; align-items:center; justify-content:center;
        cursor:pointer; color:#667085; transition:transform .2s ease,background .15s; flex-shrink:0;
    }
    .collapse-toggle.open  { transform:rotate(180deg); }
    .collapse-toggle:hover { background:#f1f3f5; }

    .collapsible-body          { padding:1rem; display:block; }
    .collapsible-body.collapsed{ display:none; }

    .sub-image-item { border:1px solid #e9ecef; border-radius:14px; background:#fff; box-shadow:0 2px 8px rgba(16,24,40,.04); overflow:hidden; }
    .sub-image-item + .sub-image-item { margin-top:.5rem; }

    .drag-handle {
        width:34px; height:34px; border-radius:10px; border:1px solid #dee2e6;
        background:#fff; display:inline-flex; align-items:center; justify-content:center;
        cursor:move; color:#667085; font-size:1rem; flex-shrink:0;
    }

    .subsection-preview-box {
        min-height:150px; border:1px dashed #d0d5dd; border-radius:14px;
        background:#f8fafc; display:flex; align-items:center; justify-content:center;
        padding:.75rem; overflow:hidden;
    }
    .subsection-preview-box img { max-width:100%; max-height:170px; border-radius:12px; object-fit:cover; }

    .sticky-actions { position:sticky; bottom:16px; z-index:10; margin-top:1.25rem; }
    .sticky-actions-inner {
        display:flex; gap:.75rem; justify-content:flex-end; align-items:center;
        background:rgba(255,255,255,.92); backdrop-filter:blur(10px);
        border:1px solid rgba(222,226,230,.8); box-shadow:0 10px 30px rgba(16,24,40,.08);
        border-radius:18px; padding:.9rem 1rem;
    }

    .btn { border-radius:12px; }
    .btn-primary { box-shadow:0 8px 18px rgba(13,110,253,.18); }
    .meta-note { font-size:.8rem; color:#98a2b3; }
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
                                <option value="<?= esc((string)$optValue) ?>"
                                    <?= (string)old('main_content_id', $content['main_content_id'] ?? ($defaultParentId ?? '')) === (string)$optValue ? 'selected' : '' ?>>
                                    <?= esc((string)$optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Module <span class="text-danger">*</span></label>
                        <select class="form-select" name="module_id" required>
                            <option value="">Select...</option>
                            <?php foreach (($moduleOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string)$optValue) ?>"
                                    <?= (string)old('module_id', $content['module_id'] ?? '') === (string)$optValue ? 'selected' : '' ?>>
                                    <?= esc((string)$optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-4">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="category_id">
                            <option value="">Select...</option>
                            <?php foreach (($categoryOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string)$optValue) ?>"
                                    <?= (string)old('category_id', $content['category_id'] ?? '') === (string)$optValue ? 'selected' : '' ?>>
                                    <?= esc((string)$optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-7">
                        <label class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name"
                               value="<?= esc(old('name', $content['name'] ?? '')) ?>" required>
                    </div>

                    <div class="col-12 col-lg-5">
                        <label class="form-label">Slug <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="slug"
                               value="<?= esc(old('slug', $content['slug'] ?? '')) ?>" required>
                        <div class="meta-note mt-1">Auto-filled from title, but still editable.</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Lead <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="lead"
                               value="<?= esc(old('lead', $content['lead'] ?? '')) ?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Excerpt <span class="text-muted small">(Optional)</span></label>
                        <input type="text" class="form-control" name="excerpt"
                               value="<?= esc(old('excerpt', $content['excerpt'] ?? '')) ?>">
                        <div class="meta-note mt-1">Short summarized preview (for article/related cards)</div>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Content</label>
                        <textarea class="form-control richtext" name="description" rows="3"><?= old('description', $content['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Body</label>
                        <textarea class="form-control richtext" name="body" rows="8"><?= old('body', $content['body'] ?? '') ?></textarea>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">External Link</label>
                        <input type="text" class="form-control" name="external_link"
                               value="<?= esc(old('external_link', $content['external_link'] ?? '')) ?>"
                               placeholder="https://example.com/page">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Rank</label>
                        <input type="number" class="form-control" name="rank"
                               value="<?= esc(old('rank', $content['rank'] ?? '')) ?>">
                    </div>

                    <div class="col-12 col-lg-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status_id">
                            <option value="">Select...</option>
                            <?php foreach (($statusOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string)$optValue) ?>"
                                    <?= (string)old('status_id', $content['status_id'] ?? '') === (string)$optValue ? 'selected' : '' ?>>
                                    <?= esc((string)$optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Tags</label>
                        <input type="text" class="form-control" name="tags"
                               value="<?= esc(old('tags', $tagsValue)) ?>"
                               placeholder="news, promo, homepage">
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Approver Matrix</label>
                        <select class="form-select" name="approvermatrix_id">
                            <option value="">Select...</option>
                            <?php foreach (($matrixOptions ?? []) as $optValue => $optLabel): ?>
                                <option value="<?= esc((string)$optValue) ?>"
                                    <?= (string)old('approvermatrix_id', $content['approvermatrix_id'] ?? '') === (string)$optValue ? 'selected' : '' ?>>
                                    <?= esc((string)$optLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Validity Start</label>
                        <input type="date" class="form-control" name="validity_date_start"
                               value="<?= esc(old('validity_date_start', $validityStart)) ?>">
                    </div>

                    <div class="col-12 col-lg-6">
                        <label class="form-label">Validity End</label>
                        <input type="date" class="form-control" name="validity_date_end"
                               value="<?= esc(old('validity_date_end', $validityEnd)) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ── Media ── -->
        <div class="modern-card">
            <div class="section-header d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="section-title">Media</h3>
                    <div class="section-subtitle">Upload images or use hosted URLs — supports multiple</div>
                </div>
                <button type="button" class="btn btn-primary btn-sm" onclick="addImageItem()">
                    <i class="bi bi-plus-lg me-1"></i> Add Image
                </button>
            </div>

            <div class="card-body">
                <div id="images-container">
                    <?php foreach ($existingImages as $ii => $img): ?>
                        <?php
                        $imgPath = $img['path'] ?? ($img['_path'] ?? '');
                        $imgSrc  = !empty($imgPath) ? site_url($imgPath) : ($img['url'] ?? '');
                        ?>
                        <div class="collapsible-item image-item" draggable="true">
                            <div class="item-header" onclick="toggleCollapse(this)">
                                <div class="item-header-left">
                                    <span class="drag-handle" onclick="event.stopPropagation()" title="Drag to reorder">⋮⋮</span>
                                    <!-- <span class="item-label">Image #<span class="image-order-label"><?= $ii + 1 ?></span></span>
                                    <span class="item-preview-text"><?= !empty($img['caption']) ? esc(mb_strimwidth($img['caption'], 0, 40, '…')) : '' ?></span> -->
                                <span class="item-label">
                                    Image - <span class="image-order-label"><?= $ii + 1 ?></span>
                                    <?php if (!empty($img['caption'])): ?>
                                        - <?= esc(mb_strimwidth($img['caption'], 0, 40, '…')) ?>
                                    <?php endif; ?>
                                </span>
                                </div>
                                <div class="item-header-actions">
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="event.stopPropagation(); removeImageItem(this)">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                    <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                                </div>
                            </div>

                            <div class="collapsible-body">
                                <!-- hidden fields that persist saved data -->
                                <input type="hidden" name="images[<?= $ii ?>][order]"         class="image-order-input" value="<?= $ii + 1 ?>">
                                <input type="hidden" name="images[<?= $ii ?>][existing_path]" value="<?= esc($imgPath) ?>">

                                <div class="row g-3 align-items-start">
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label">Upload Image</label>
                                        <input type="file" class="form-control"
                                               name="image_file_<?= $ii ?>" accept="image/*"
                                               onchange="previewImageUpload(this)">
                                        <div class="meta-note mt-1">Accepted: JPG, PNG, WEBP, GIF</div>
                                        <?php if (!empty($imgPath)): ?>
                                            <div class="meta-note mt-1 text-success">
                                                <i class="bi bi-check-circle me-1"></i>Current: <?= esc(basename($imgPath)) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="col-12 col-lg-6">
                                        <label class="form-label">Image URL</label>
                                        <input type="text" class="form-control"
                                               name="images[<?= $ii ?>][url]"
                                               value="<?= esc(old("images.{$ii}.url", $img['url'] ?? '')) ?>"
                                               placeholder="https://example.com/image.jpg"
                                               oninput="previewImageUrl(this)">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Caption</label>
                                        <textarea class="form-control" name="images[<?= $ii ?>][caption]" rows="2"
                                                  placeholder="Optional caption or alt text"><?= esc(old("images.{$ii}.caption", $img['caption'] ?? '')) ?></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label">Preview</label>
                                        <div class="preview-box">
                                            <img class="image-preview <?= empty($imgSrc) ? 'd-none' : '' ?>"
                                                 src="<?= esc($imgSrc) ?>" alt="Image Preview">
                                            <div class="preview-placeholder <?= !empty($imgSrc) ? 'd-none' : '' ?>">
                                                <i class="bi bi-image fs-2 d-block mb-2"></i>No image selected yet
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- ── Subsections ── -->
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
                                <?php
                                /* normalise subsection images */
                                $subImages = [];
                                if (!empty($sub['images'])) {
                                    $dec = json_decode($sub['images'], true);
                                    if (is_array($dec)) $subImages = $dec;
                                }
                                if (empty($subImages)) {
                                    $subImages = [[
                                        'path'    => $sub['image_path']        ?? '',
                                        'url'     => $sub['image_url']          ?? '',
                                        'caption' => $sub['image_description']  ?? '',
                                        'order'   => 1,
                                    ]];
                                }
                                ?>
                                <div class="collapsible-item subsection-item mb-3" draggable="true">
                                    <div class="item-header" onclick="toggleCollapse(this)">
                                        <div class="item-header-left">
                                            <span class="drag-handle" onclick="event.stopPropagation()" title="Drag to reorder">⋮⋮</span>
                                            <span class="item-label">Subsection -<span class="subsection-order-label"><?= $i + 1 ?></span></span>
                                            <span class="item-preview-text subsection-preview-title"><?= !empty($sub['name']) ? esc(mb_strimwidth($sub['name'], 0, 50, '…')) : '' ?></span>
                                        </div>dasd
                                        <div class="item-header-actions">
                                            <button type="button" class="btn btn-outline-danger btn-sm"
                                                    onclick="event.stopPropagation(); removeSubsection(this)">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                            <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                                        </div>
                                    </div>

                                    <div class="collapsible-body">
                                        <input type="hidden" name="subsections[<?= $i ?>][id]"   value="<?= esc($sub['id']) ?>">
                                        <input type="hidden" name="subsections[<?= $i ?>][rank]" class="subsection-rank" value="<?= esc($sub['rank'] ?? ($i + 1)) ?>">

                                        <div class="row g-3">
                                            <div class="col-12 col-lg-6">
                                                <label class="form-label">Title</label>
                                                <input type="text" name="subsections[<?= $i ?>][name]"
                                                       class="form-control subsection-name-input"
                                                       value="<?= esc($sub['name'] ?? '') ?>"
                                                       placeholder="Title"
                                                       oninput="autoSlugSubsection(this)" required>
                                            </div>
                                            <div class="col-12 col-lg-6">
                                                <label class="form-label">Slug</label>
                                                <input type="text" name="subsections[<?= $i ?>][slug]"
                                                       class="form-control"
                                                       value="<?= esc($sub['slug'] ?? '') ?>"
                                                       placeholder="Slug">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Description</label>
                                                <textarea name="subsections[<?= $i ?>][description]"
                                                          class="form-control richtext"
                                                          placeholder="Description"><?= $sub['description'] ?? '' ?></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label">Body</label>
                                                <textarea name="subsections[<?= $i ?>][body]"
                                                          class="form-control richtext"
                                                          placeholder="Body"><?= $sub['body'] ?? '' ?></textarea>
                                            </div>

                                            <!-- Subsection Images -->
                                            <div class="col-12">
                                                <div class="d-flex justify-content-between align-items-center mb-2">
                                                    <label class="form-label mb-0">Images</label>
                                                    <button type="button" class="btn btn-outline-primary btn-sm"
                                                            onclick="addSubsectionImage(this)">
                                                        <i class="bi bi-plus-lg me-1"></i> Add Image
                                                    </button>
                                                </div>
                                                <div class="subsection-images-container">
                                                    <?php foreach ($subImages as $si => $simg): ?>
                                                        <?php
                                                        $sPath   = $simg['path'] ?? ($simg['_path'] ?? '');
                                                        $sImgSrc = !empty($sPath) ? site_url($sPath) : ($simg['url'] ?? '');
                                                        ?>
                                                        <div class="collapsible-item sub-image-item" draggable="true">
                                                            <div class="item-header" onclick="toggleCollapse(this)">
                                                                <div class="item-header-left">
                                                                    <span class="drag-handle" onclick="event.stopPropagation()">⋮⋮</span>
                                                                    <span class="item-label">Image - <span class="sub-image-order-label"><?= $si + 1 ?></span></span>
                                                                    <span class="item-preview-text"><?= !empty($simg['caption']) ? esc(mb_strimwidth($simg['caption'], 0, 40, '…')) : '' ?></span>
                                                                </div>
                                                                <div class="item-header-actions">
                                                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                                                            onclick="event.stopPropagation(); removeSubsectionImage(this)">
                                                                        <i class="bi bi-trash3"></i>
                                                                    </button>
                                                                    <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                                                                </div>
                                                            </div>
                                                            <div class="collapsible-body">
                                                                <!-- persist existing path & order -->
                                                                <input type="hidden" name="subsections[<?= $i ?>][images][<?= $si ?>][order]"         class="sub-image-order-input" value="<?= $si + 1 ?>">
                                                                <input type="hidden" name="subsections[<?= $i ?>][images][<?= $si ?>][existing_path]"  value="<?= esc($sPath) ?>">

                                                                <div class="row g-3">
                                                                    <div class="col-12 col-lg-6">
                                                                        <label class="form-label">Upload Image</label>
                                                                        <input type="file" class="form-control"
                                                                               name="subsection_image_file_<?= $i ?>_<?= $si ?>"
                                                                               accept="image/*"
                                                                               onchange="previewSubsectionImageUpload(this)">
                                                                        <?php if (!empty($sPath)): ?>
                                                                            <div class="meta-note mt-1 text-success">
                                                                                <i class="bi bi-check-circle me-1"></i>Current: <?= esc(basename($sPath)) ?>
                                                                            </div>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="col-12 col-lg-6">
                                                                        <label class="form-label">Image URL</label>
                                                                        <input type="text" class="form-control"
                                                                               name="subsections[<?= $i ?>][images][<?= $si ?>][url]"
                                                                               value="<?= esc($simg['url'] ?? '') ?>"
                                                                               placeholder="https://example.com/image.jpg"
                                                                               oninput="previewSubsectionImageUrl(this)">
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label">Caption</label>
                                                                        <textarea class="form-control"
                                                                                  name="subsections[<?= $i ?>][images][<?= $si ?>][caption]"
                                                                                  rows="2" placeholder="Optional caption or alt text"><?= esc($simg['caption'] ?? '') ?></textarea>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <label class="form-label">Preview</label>
                                                                        <div class="subsection-preview-box">
                                                                            <img class="subsection-preview <?= empty($sImgSrc) ? 'd-none' : '' ?>"
                                                                                 src="<?= esc($sImgSrc) ?>" alt="Preview">
                                                                            <div class="preview-placeholder <?= !empty($sImgSrc) ? 'd-none' : '' ?>">No preview yet</div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div><!-- /subsection-images-container -->
                                            </div>
                                            <!-- /Subsection Images -->

                                            <div class="col-12">
                                                <label class="form-label">Rank</label>
                                                <div class="form-control bg-light">Auto-managed by drag and drop</div>
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

<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>
<script>
/* ─────────────────────────────────────────
   State
───────────────────────────────────────── */
let subsectionIndex = <?= !empty($subsections) ? count($subsections) : 0 ?>;
let imageIndex      = <?= count($existingImages) ?>;
let slugEdited      = false;
let draggedItem     = null;
let draggedImage    = null;
let editors         = [];

/* ─────────────────────────────────────────
   Helpers
───────────────────────────────────────── */
function generateSlug(text) {
    return text.toLowerCase().trim()
        .replace(/[^\w\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');
}

function toggleCollapse(header) {
    const body   = header.nextElementSibling;
    const toggle = header.querySelector('.collapse-toggle');
    const isOpen = !body.classList.contains('collapsed');
    body.classList.toggle('collapsed', isOpen);
    toggle.classList.toggle('open', !isOpen);
}

function initRichTextEditors(scope = document) {
    scope.querySelectorAll('textarea.richtext').forEach(el => {
        if (el.dataset.editorInitialized === 'true') return;
        ClassicEditor.create(el)
            .then(editor => { editors.push(editor); el.dataset.editorInitialized = 'true'; })
            .catch(console.error);
    });
}

/* ─────────────────────────────────────────
   Boot
───────────────────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.querySelector('input[name="name"]');
    const slugInput  = document.querySelector('input[name="slug"]');

    if (slugInput) slugInput.addEventListener('input', () => { slugEdited = slugInput.value.trim() !== ''; });
    if (titleInput && slugInput) {
        titleInput.addEventListener('input', function () {
            if (!slugEdited) slugInput.value = generateSlug(this.value);
        });
    }

    initDragAndDrop();
    initImageDragAndDrop();
    document.querySelectorAll('.subsection-images-container').forEach(c => initSubsectionImageDragAndDrop(c));
    refreshSubsectionRanks();
    refreshImageOrders();
    initRichTextEditors();
});

// document.addEventListener('input', function (e) {
//     if (e.target.name && e.target.name.includes('[slug]')) e.target.dataset.edited = 'true';
//     if (e.target.classList.contains('subsection-name-input')) {
//         const header = e.target.closest('.subsection-item').querySelector('.subsection-preview-title');
//         if (header) header.textContent = e.target.value ? e.target.value.substring(0, 50) : '';
//     }
// });

document.addEventListener('input', function (e) {
    if (e.target.name && e.target.name.includes('[slug]')) {
        e.target.dataset.edited = 'true';
    }

    if (e.target.classList.contains('subsection-name-input')) {
        const header = e.target.closest('.subsection-item').querySelector('.subsection-preview-title');
        if (header) header.textContent = e.target.value ? e.target.value.substring(0, 50) : '';
    }

    if (e.target.name && e.target.name.includes('[caption]')) {
        const imageItem = e.target.closest('.image-item');

        if (imageItem) {
            const previewText = imageItem.querySelector('.item-preview-text');

            if (previewText) {
                previewText.textContent = e.target.value.trim()
                    ? ' - ' + e.target.value.substring(0, 40)
                    : '';
            }
        }
    }
});

function autoSlugSubsection(input) {
    const wrapper   = input.closest('.subsection-item');
    const slugInput = wrapper.querySelector('input[name*="[slug]"]');
    if (!slugInput) return;
    if (!slugInput.dataset.edited || slugInput.value.trim() === '') {
        slugInput.value = generateSlug(input.value);
    }
}


function refreshImageOrders() {
    document.querySelectorAll('#images-container .image-item').forEach((item, i) => {
        const o = item.querySelector('.image-order-input');
        const l = item.querySelector('.image-order-label');
        if (o) o.value       = i + 1;
        if (l) l.textContent = i + 1;

        // Renumber all named fields so indices stay sequential
        item.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/images\[\d+\]/, `images[${i}]`);
            // also fix file input names  image_file_N
            if (el.type === 'file') {
                el.name = el.name.replace(/image_file_\d+/, `image_file_${i}`);
            }
        });
    });
}

function refreshSubsectionRanks() {
    document.querySelectorAll('#subsection-container .subsection-item').forEach((item, i) => {
        const r = item.querySelector('.subsection-rank');
        const l = item.querySelector('.subsection-order-label');
        if (r) r.value       = i + 1;
        if (l) l.textContent = i + 1;

        // Renumber all named fields inside this subsection
        item.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/subsections\[\d+\]/, `subsections[${i}]`);
            // fix subsection file inputs:  subsection_image_file_N_M
            if (el.type === 'file') {
                el.name = el.name.replace(/subsection_image_file_\d+_(\d+)/, `subsection_image_file_${i}_$1`);
            }
        });
    });
}

function refreshSubsectionImageOrders(container) {
    container.querySelectorAll('.sub-image-item').forEach((item, i) => {
        const o = item.querySelector('.sub-image-order-input');
        const l = item.querySelector('.sub-image-order-label');
        if (o) o.value       = i + 1;
        if (l) l.textContent = i + 1;

        // Renumber image slot indices  [images][N]
        item.querySelectorAll('[name]').forEach(el => {
            el.name = el.name.replace(/\[images\]\[\d+\]/, `[images][${i}]`);
            // fix file inputs subsection_image_file_X_N
            if (el.type === 'file') {
                el.name = el.name.replace(/(subsection_image_file_\d+_)\d+/, `$1${i}`);
            }
        });
    });
}


function addImageItem() {
    const container = document.getElementById('images-container');
    const idx       = imageIndex;

    const html = `
        <div class="collapsible-item image-item" draggable="true">
            <div class="item-header" onclick="toggleCollapse(this)">
                <div class="item-header-left">
                    <span class="drag-handle" onclick="event.stopPropagation()" title="Drag to reorder">⋮⋮</span>
                    <span class="item-label">
                        Image - <span class="image-order-label"></span>
                        <span class="item-preview-text"></span>
                    </span>
                </div>
                <div class="item-header-actions">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="event.stopPropagation(); removeImageItem(this)">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                </div>
            </div>
            <div class="collapsible-body">
                <input type="hidden" name="images[${idx}][order]"         class="image-order-input" value="">
                <input type="hidden" name="images[${idx}][existing_path]" value="">
                <div class="row g-3 align-items-start">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Upload Image</label>
                        <input type="file" class="form-control" name="image_file_${idx}" accept="image/*"
                               onchange="previewImageUpload(this)">
                        <div class="meta-note mt-1">Accepted: JPG, PNG, WEBP, GIF</div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Image URL</label>
                        <input type="text" class="form-control" name="images[${idx}][url]"
                               placeholder="https://example.com/image.jpg" oninput="previewImageUrl(this)">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Caption</label>
                        <textarea class="form-control" name="images[${idx}][caption]" rows="2"
                                  placeholder="Optional caption or alt text"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Preview</label>
                        <div class="preview-box">
                            <img class="image-preview d-none" alt="Image Preview">
                            <div class="preview-placeholder"><i class="bi bi-image fs-2 d-block mb-2"></i>No image selected yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    container.insertAdjacentHTML('beforeend', html);
    imageIndex++;
    refreshImageOrders();
    initImageDragAndDrop();
}

function removeImageItem(btn) {
    btn.closest('.image-item').remove();
    refreshImageOrders();
}

function previewImageUpload(input) {
    const wrapper     = input.closest('.image-item');
    const preview     = wrapper.querySelector('.image-preview');
    const placeholder = wrapper.querySelector('.preview-placeholder');
    const file        = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); placeholder.classList.add('d-none'); };
    reader.readAsDataURL(file);
}

function previewImageUrl(input) {
    const wrapper     = input.closest('.image-item');
    const preview     = wrapper.querySelector('.image-preview');
    const placeholder = wrapper.querySelector('.preview-placeholder');
    const url         = input.value.trim();
    if (url) { preview.src = url; preview.classList.remove('d-none'); placeholder.classList.add('d-none'); }
    else     { preview.src = ''; preview.classList.add('d-none');    placeholder.classList.remove('d-none'); }
}

function initImageDragAndDrop() {
    document.querySelectorAll('#images-container .image-item').forEach(item => {
        item.removeEventListener('dragstart', handleImageDragStart);
        item.removeEventListener('dragover',  handleDragOver);
        item.removeEventListener('drop',      handleImageDrop);
        item.removeEventListener('dragend',   handleDragEnd);
        item.addEventListener('dragstart', handleImageDragStart);
        item.addEventListener('dragover',  handleDragOver);
        item.addEventListener('drop',      handleImageDrop);
        item.addEventListener('dragend',   handleDragEnd);
    });
}

function handleImageDragStart(e) { draggedImage = e.currentTarget; e.currentTarget.classList.add('opacity-50'); }
function handleImageDrop(e) {
    e.preventDefault();
    const target = e.currentTarget;
    if (!draggedImage || draggedImage === target) return;
    const container = document.getElementById('images-container');
    const items     = [...container.querySelectorAll('.image-item')];
    const di = items.indexOf(draggedImage), ti = items.indexOf(target);
    container.insertBefore(draggedImage, di < ti ? target.nextSibling : target);
    refreshImageOrders();
}


function addSubsection() {
    const container  = document.getElementById('subsection-container');
    const emptyState = document.getElementById('empty-subsection-state');
    if (emptyState) emptyState.remove();

    const si = subsectionIndex;

    const html = `
        <div class="collapsible-item subsection-item mb-3" draggable="true">
            <div class="item-header" onclick="toggleCollapse(this)">
                <div class="item-header-left">
                    <span class="drag-handle" onclick="event.stopPropagation()" title="Drag to reorder">⋮⋮</span>
                    <span class="item-label">Subsection - <span class="subsection-order-label"></span></span>
                    <span class="item-preview-text subsection-preview-title"></span>
                </div>
                <div class="item-header-actions">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="event.stopPropagation(); removeSubsection(this)">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                </div>
            </div>
            <div class="collapsible-body">
                <input type="hidden" name="subsections[${si}][rank]" class="subsection-rank" value="">
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Title</label>
                        <input type="text" name="subsections[${si}][name]"
                               class="form-control subsection-name-input"
                               placeholder="Title" oninput="autoSlugSubsection(this)" required>
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Slug</label>
                        <input type="text" name="subsections[${si}][slug]" class="form-control" placeholder="Slug">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="subsections[${si}][description]" class="form-control richtext" placeholder="Description"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Body</label>
                        <textarea name="subsections[${si}][body]" class="form-control richtext" placeholder="Body"></textarea>
                    </div>
                    <div class="col-12">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Images</label>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSubsectionImage(this)">
                                <i class="bi bi-plus-lg me-1"></i> Add Image
                            </button>
                        </div>
                        <div class="subsection-images-container">
                            <div class="collapsible-item sub-image-item" draggable="true">
                                <div class="item-header" onclick="toggleCollapse(this)">
                                    <div class="item-header-left">
                                        <span class="drag-handle" onclick="event.stopPropagation()">⋮⋮</span>
                                        <span class="item-label">Image - <span class="sub-image-order-label">1</span></span>
                                        <span class="item-preview-text"></span>
                                    </div>
                                    <div class="item-header-actions">
                                        <button type="button" class="btn btn-outline-danger btn-sm"
                                                onclick="event.stopPropagation(); removeSubsectionImage(this)">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                        <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                                    </div>
                                </div>
                                <div class="collapsible-body">
                                    <input type="hidden" name="subsections[${si}][images][0][order]"         class="sub-image-order-input" value="1">
                                    <input type="hidden" name="subsections[${si}][images][0][existing_path]" value="">
                                    <div class="row g-3">
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label">Upload Image</label>
                                            <input type="file" class="form-control"
                                                   name="subsection_image_file_${si}_0" accept="image/*"
                                                   onchange="previewSubsectionImageUpload(this)">
                                        </div>
                                        <div class="col-12 col-lg-6">
                                            <label class="form-label">Image URL</label>
                                            <input type="text" class="form-control"
                                                   name="subsections[${si}][images][0][url]"
                                                   placeholder="https://example.com/image.jpg"
                                                   oninput="previewSubsectionImageUrl(this)">
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Caption</label>
                                            <textarea class="form-control"
                                                      name="subsections[${si}][images][0][caption]"
                                                      rows="2" placeholder="Optional caption or alt text"></textarea>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">Preview</label>
                                            <div class="subsection-preview-box">
                                                <img class="subsection-preview d-none" alt="Preview">
                                                <div class="preview-placeholder">No preview yet</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Rank</label>
                        <div class="form-control bg-light">Auto-managed by drag and drop</div>
                    </div>
                </div>
            </div>
        </div>`;

    container.insertAdjacentHTML('beforeend', html);
    const newItem = container.lastElementChild;
    subsectionIndex++;
    initDragAndDrop();
    refreshSubsectionRanks();
    initSubsectionImageDragAndDrop(newItem.querySelector('.subsection-images-container'));
    initRichTextEditors(newItem);
}

function removeSubsection(button) {
    button.closest('.subsection-item').remove();
    refreshSubsectionRanks();
    const container = document.getElementById('subsection-container');
    if (!container.querySelector('.subsection-item')) {
        container.innerHTML = `
            <div id="empty-subsection-state" class="preview-box">
                <div class="preview-placeholder">
                    <i class="bi bi-layout-text-window-reverse fs-2 d-block mb-2"></i>
                    No subsections yet. Add one to get started.
                </div>
            </div>`;
    }
}

function addSubsectionImage(btn) {
    const subsectionItem = btn.closest('.subsection-item');
    const container      = subsectionItem.querySelector('.subsection-images-container');

    const anyInput    = subsectionItem.querySelector('input[name*="subsections["]');
    const subIdxMatch = anyInput ? anyInput.name.match(/subsections\[(\d+)\]/) : null;
    const subIdx      = subIdxMatch ? subIdxMatch[1] : Date.now();

    const si = container.querySelectorAll('.sub-image-item').length;

    const html = `
        <div class="collapsible-item sub-image-item" draggable="true">
            <div class="item-header" onclick="toggleCollapse(this)">
                <div class="item-header-left">
                    <span class="drag-handle" onclick="event.stopPropagation()">⋮⋮</span>
                    <span class="item-label">Image - <span class="sub-image-order-label"></span></span>
                    <span class="item-preview-text"></span>
                </div>
                <div class="item-header-actions">
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            onclick="event.stopPropagation(); removeSubsectionImage(this)">
                        <i class="bi bi-trash3"></i>
                    </button>
                    <span class="collapse-toggle open"><i class="bi bi-chevron-down"></i></span>
                </div>
            </div>
            <div class="collapsible-body">
                <input type="hidden" name="subsections[${subIdx}][images][${si}][order]"         class="sub-image-order-input" value="">
                <input type="hidden" name="subsections[${subIdx}][images][${si}][existing_path]" value="">
                <div class="row g-3">
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Upload Image</label>
                        <input type="file" class="form-control"
                               name="subsection_image_file_${subIdx}_${si}" accept="image/*"
                               onchange="previewSubsectionImageUpload(this)">
                    </div>
                    <div class="col-12 col-lg-6">
                        <label class="form-label">Image URL</label>
                        <input type="text" class="form-control"
                               name="subsections[${subIdx}][images][${si}][url]"
                               placeholder="https://example.com/image.jpg"
                               oninput="previewSubsectionImageUrl(this)">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Caption</label>
                        <textarea class="form-control"
                                  name="subsections[${subIdx}][images][${si}][caption]"
                                  rows="2" placeholder="Optional caption or alt text"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Preview</label>
                        <div class="subsection-preview-box">
                            <img class="subsection-preview d-none" alt="Preview">
                            <div class="preview-placeholder">No preview yet</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>`;

    container.insertAdjacentHTML('beforeend', html);
    refreshSubsectionImageOrders(container);
    initSubsectionImageDragAndDrop(container);
}

function removeSubsectionImage(btn) {
    const item      = btn.closest('.sub-image-item');
    const container = item.closest('.subsection-images-container');
    item.remove();
    refreshSubsectionImageOrders(container);
}

function previewSubsectionImageUpload(input) {
    const wrapper     = input.closest('.sub-image-item');
    const preview     = wrapper.querySelector('.subsection-preview');
    const placeholder = wrapper.querySelector('.preview-placeholder');
    const file        = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => { preview.src = e.target.result; preview.classList.remove('d-none'); if (placeholder) placeholder.classList.add('d-none'); };
    reader.readAsDataURL(file);
}

function previewSubsectionImageUrl(input) {
    const wrapper     = input.closest('.sub-image-item');
    const preview     = wrapper.querySelector('.subsection-preview');
    const placeholder = wrapper.querySelector('.preview-placeholder');
    const url         = input.value.trim();
    if (url) { preview.src = url; preview.classList.remove('d-none'); if (placeholder) placeholder.classList.add('d-none'); }
    else     { preview.src = ''; preview.classList.add('d-none');    if (placeholder) placeholder.classList.remove('d-none'); }
}

function initSubsectionImageDragAndDrop(container) {
    if (!container) return;
    let draggedSubImage = null;

    container.querySelectorAll('.sub-image-item').forEach(item => {
        item.removeEventListener('dragstart', onSubImgDragStart);
        item.removeEventListener('dragover',  handleDragOver);
        item.removeEventListener('drop',      onSubImgDrop);
        item.removeEventListener('dragend',   handleDragEnd);
        item.addEventListener('dragstart', onSubImgDragStart);
        item.addEventListener('dragover',  handleDragOver);
        item.addEventListener('drop',      onSubImgDrop);
        item.addEventListener('dragend',   handleDragEnd);
    });

    function onSubImgDragStart(e) { draggedSubImage = e.currentTarget; e.currentTarget.classList.add('opacity-50'); }
    function onSubImgDrop(e) {
        e.preventDefault();
        const target = e.currentTarget;
        if (!draggedSubImage || draggedSubImage === target) return;
        const items = [...container.querySelectorAll('.sub-image-item')];
        const di    = items.indexOf(draggedSubImage), ti = items.indexOf(target);
        container.insertBefore(draggedSubImage, di < ti ? target.nextSibling : target);
        refreshSubsectionImageOrders(container);
    }
}


function initDragAndDrop() {
    document.querySelectorAll('#subsection-container .subsection-item').forEach(item => {
        item.removeEventListener('dragstart', handleDragStart);
        item.removeEventListener('dragover',  handleDragOver);
        item.removeEventListener('drop',      handleDrop);
        item.removeEventListener('dragend',   handleDragEnd);
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragover',  handleDragOver);
        item.addEventListener('drop',      handleDrop);
        item.addEventListener('dragend',   handleDragEnd);
    });
}

function handleDragStart(e) { draggedItem = e.currentTarget; e.currentTarget.classList.add('opacity-50'); }
function handleDragOver(e)  { e.preventDefault(); }
function handleDrop(e) {
    e.preventDefault();
    const target = e.currentTarget;
    if (!draggedItem || draggedItem === target) return;
    const container = document.getElementById('subsection-container');
    const items     = [...container.querySelectorAll('.subsection-item')];
    const di = items.indexOf(draggedItem), ti = items.indexOf(target);
    container.insertBefore(draggedItem, di < ti ? target.nextSibling : target);
    refreshSubsectionRanks();
}
function handleDragEnd(e) {
    e.currentTarget.classList.remove('opacity-50');
    draggedItem = draggedImage = null;
}

</script>

<?= $this->endSection() ?>