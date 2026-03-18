<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
  .content-view-wrap {
    max-width: 1200px;
    margin: 0 auto;
  }

  .content-view-hero {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
  }

  .content-view-title {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    color: #1f2937;
    margin-bottom: 0.35rem;
  }

  .content-view-submeta {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
  }

  .content-meta-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.75rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    color: #475467;
    font-size: 0.82rem;
    font-weight: 600;
  }

  .content-view-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 0.65rem;
  }

  .content-view-actions .btn {
    border-radius: 12px;
  }

  .content-modern-card {
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .content-modern-card + .content-modern-card {
    margin-top: 1.25rem;
  }

  .content-modern-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .content-modern-header-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
  }

  .content-modern-body {
    padding: 1.25rem;
  }

  .content-image-frame {
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    overflow: hidden;
    background: #f8fafc;
  }

  .content-image-frame img {
    width: 100%;
    max-height: 420px;
    object-fit: cover;
    display: block;
  }

  .content-description {
    font-size: 1.05rem;
    line-height: 1.7;
    color: #475467;
    margin-bottom: 1rem;
  }

  .content-body {
    color: #344054;
    line-height: 1.75;
    font-size: 0.98rem;
  }

  .content-body p:last-child {
    margin-bottom: 0;
  }

  .subsection-accordion .accordion-item {
    border: 1px solid #e5e7eb;
    border-radius: 16px !important;
    overflow: hidden;
    margin-bottom: 0.85rem;
    box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
  }

  .subsection-accordion .accordion-button {
    font-weight: 700;
    color: #1f2937;
    background: #fff;
    box-shadow: none !important;
    padding: 1rem 1.1rem;
  }

  .subsection-accordion .accordion-button:not(.collapsed) {
    background: #f8fbff;
    color: #0d6efd;
  }

  .subsection-accordion .accordion-body {
    padding: 1rem 1.1rem 1.15rem;
    background: #fff;
  }

  .subsection-image-frame {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    background: #f8fafc;
  }

  .subsection-image-frame img {
    width: 100%;
    max-height: 260px;
    object-fit: cover;
    display: block;
  }

  .subsection-description {
    font-weight: 600;
    color: #344054;
    margin-bottom: 0.75rem;
  }

  .subsection-body {
    color: #475467;
    line-height: 1.7;
  }

  .empty-subsection-state {
    text-align: center;
    color: #98a2b3;
    padding: 2.25rem 1rem;
    border: 1px dashed #d0d5dd;
    border-radius: 18px;
    background: #f8fafc;
  }

  .link-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
    border: 1px solid #dbe3ec;
    background: #fff;
    color: #344054;
    border-radius: 999px;
    padding: 0.55rem 0.9rem;
    font-weight: 600;
  }

  .link-chip:hover {
    background: #f8fafc;
    color: #0d6efd;
  }

  @media (max-width: 768px) {
    .content-modern-body {
      padding: 1rem;
    }

    .content-view-actions {
      width: 100%;
    }

    .content-view-actions .btn {
      flex: 1 1 auto;
    }
  }
</style>

<div class="content-view-wrap">
  <div class="content-view-hero">
    <div>
      <h1 class="content-view-title"><?= esc($content['name'] ?? '') ?></h1>

      <div class="content-view-submeta">
        <span class="content-meta-badge">
          <i class="bi bi-grid me-1"></i>
          <?= esc($content['module_name'] ?? '-') ?>
        </span>

        <?php if (!empty($content['category_name'])): ?>
          <span class="content-meta-badge">
            <i class="bi bi-tag me-1"></i>
            <?= esc($content['category_name']) ?>
          </span>
        <?php endif; ?>

        <?php if (!empty($content['status_name'])): ?>
          <span class="content-meta-badge">
            <i class="bi bi-check2-circle me-1"></i>
            <?= esc($content['status_name']) ?>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <div class="content-view-actions">
      <a class="btn btn-outline-secondary" href="<?= site_url('admin/contents') ?>">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
      <a class="btn btn-outline-primary" href="<?= site_url('admin/contents/edit/' . $content['id']) ?>">
        <i class="bi bi-pencil-square me-1"></i> Edit
      </a>
      <a class="btn btn-dark" href="<?= site_url('admin/contents/create?parent_id=' . $content['id']) ?>">
        <i class="bi bi-plus-lg me-1"></i> Add Subsection
      </a>
    </div>
  </div>

  <div class="content-modern-card">
    <div class="content-modern-header">
      <h3 class="content-modern-header-title">Content Overview</h3>
    </div>

    <div class="content-modern-body">
      <?php if (!empty($content['image_path']) || !empty($content['image_url'])): ?>
        <div class="content-image-frame mb-4">
          <img
            src="<?= !empty($content['image_path']) ? site_url($content['image_path']) : esc($content['image_url']) ?>"
            alt="<?= esc($content['name'] ?? 'Content Image') ?>"
          >
        </div>
      <?php endif; ?>

      <?php if (!empty($content['description'])): ?>
        <div class="content-description">
          <?= esc($content['description']) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($content['external_link'])): ?>
        <div class="mb-4">
          <a href="<?= esc($content['external_link']) ?>" target="_blank" rel="noopener" class="link-chip">
            <i class="bi bi-box-arrow-up-right"></i>
            Open External Link
          </a>
        </div>
      <?php endif; ?>

      <div class="content-body">
        <?= $content['body'] ?? '' ?>
      </div>
    </div>
  </div>

  <div class="content-modern-card">
    <div class="content-modern-header">
      <h3 class="content-modern-header-title">Subsections</h3>
    </div>

    <div class="content-modern-body">
      <?php if (!empty($subsections)): ?>
        <div class="accordion subsection-accordion" id="subsectionsAccordion">
          <?php foreach ($subsections as $index => $sub): ?>
            <?php $collapseId = 'subsectionCollapse' . (int) $sub['id']; ?>
            <div class="accordion-item">
              <h2 class="accordion-header" id="heading<?= (int) $sub['id'] ?>">
                <button
                  class="accordion-button <?= $index > 0 ? 'collapsed' : '' ?>"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#<?= esc($collapseId) ?>"
                  aria-expanded="<?= $index === 0 ? 'true' : 'false' ?>"
                  aria-controls="<?= esc($collapseId) ?>"
                >
                  <?= esc($sub['name']) ?>
                </button>
              </h2>

              <div
                id="<?= esc($collapseId) ?>"
                class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>"
                data-bs-parent="#subsectionsAccordion"
              >
                <div class="accordion-body">
                  <?php if (!empty($sub['image_path']) || !empty($sub['image_url'])): ?>
                    <div class="subsection-image-frame mb-3">
                      <img
                        src="<?= !empty($sub['image_path']) ? site_url($sub['image_path']) : esc($sub['image_url']) ?>"
                        alt="<?= esc($sub['name']) ?>"
                      >
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($sub['description'])): ?>
                    <div class="subsection-description">
                      <?= esc($sub['description']) ?>
                    </div>
                  <?php endif; ?>

                  <?php if (!empty($sub['external_link'])): ?>
                    <div class="mb-3">
                      <a href="<?= esc($sub['external_link']) ?>" target="_blank" rel="noopener" class="link-chip">
                        <i class="bi bi-box-arrow-up-right"></i>
                        Open Link
                      </a>
                    </div>
                  <?php endif; ?>

                  <div class="subsection-body mb-3">
                    <?= $sub['body'] ?? '' ?>
                  </div>

                  <a class="btn btn-sm btn-outline-primary" href="<?= site_url('admin/contents/edit/' . $sub['id']) ?>">
                    <i class="bi bi-pencil-square me-1"></i> Edit Subsection
                  </a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="empty-subsection-state">
          <i class="bi bi-layout-text-window-reverse fs-2 d-block mb-2"></i>
          No subsections found for this content.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?= $this->endSection() ?>