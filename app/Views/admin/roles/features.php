<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
  .permission-wrap {
    width: 100%;
    max-width: 100%;
  }

  .permission-hero {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
  }

  .permission-title {
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    line-height: 1.15;
    margin-bottom: 0.2rem;
    color: #1f2937;
  }

  .permission-subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
  }

  .permission-role-badge {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    padding: .45rem .8rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    color: #475467;
    font-size: .85rem;
    font-weight: 700;
    margin-top: .55rem;
  }

  .permission-card {
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    overflow: hidden;
  }

  .permission-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .permission-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
  }

  .permission-card-subtitle {
    margin-top: .2rem;
    color: #6b7280;
    font-size: .9rem;
  }

  .permission-card-body {
    padding: 1rem 1.25rem 1.25rem;
  }

  .permission-table-wrap {
    border: 1px solid #eef2f7;
    border-radius: 16px;
    overflow: hidden;
  }

  .permission-table {
    margin-bottom: 0;
    min-width: 980px;
  }

  .permission-table thead th {
    background: #f8fafc;
    color: #475467;
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.95rem 0.8rem;
    white-space: nowrap;
    vertical-align: middle;
  }

  .permission-table tbody td {
    padding: 0.9rem 0.8rem;
    border-color: #eef2f7;
    vertical-align: middle;
    color: #344054;
  }

  .permission-table tbody tr:hover {
    background: #fafcff;
  }

  .module-cell {
    color: #667085;
    font-weight: 600;
    white-space: nowrap;
  }

  .feature-cell {
    font-weight: 700;
    color: #1f2937;
  }

  .check-cell {
    text-align: center;
  }

  .check-cell input[type="checkbox"] {
    width: 1.05rem;
    height: 1.05rem;
    cursor: pointer;
  }

  .sticky-actions {
    position: sticky;
    bottom: 16px;
    z-index: 10;
    margin-top: 1rem;
  }

  .sticky-actions-inner {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    background: rgba(255, 255, 255, 0.92);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(229, 231, 235, 0.9);
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.08);
    border-radius: 18px;
    padding: 0.9rem 1rem;
  }

  .sticky-note {
    color: #6b7280;
    font-size: 0.9rem;
  }

  .sticky-actions .btn {
    border-radius: 12px;
  }

  .sticky-actions .btn-dark {
    box-shadow: 0 10px 18px rgba(17, 24, 39, 0.18);
  }

  @media (max-width: 768px) {
    .permission-title {
      font-size: 1.5rem;
    }

    .sticky-actions-inner {
      flex-direction: column;
      align-items: stretch;
    }

    .sticky-actions .btn {
      width: 100%;
    }
  }
</style>

<div class="permission-wrap">
  <div class="permission-hero">
    <div>
      <h1 class="permission-title">Feature Permissions</h1>
      <p class="permission-subtitle">Manage access rights for each feature available to this role.</p>
      <div class="permission-role-badge">
        <i class="bi bi-shield-check"></i>
        Role: <?= esc($role['name']) ?>
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-secondary" href="<?= site_url('admin/roles') ?>">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  <div class="permission-card">
    <div class="permission-card-header">
      <h3 class="permission-card-title">Permissions Matrix</h3>
      <div class="permission-card-subtitle">Enable features and assign allowed actions per module.</div>
    </div>

    <div class="permission-card-body">
      <form method="post">
        <?= csrf_field() ?>

        <div class="permission-table-wrap table-responsive">
          <table class="table permission-table align-middle">
            <thead>
              <tr>
                <th style="width: 90px;">Enable</th>
                <th style="min-width: 140px;">Module</th>
                <th style="min-width: 220px;">Feature</th>
                <th style="width: 90px;" class="text-center">View</th>
                <th style="width: 90px;" class="text-center">Add</th>
                <th style="width: 90px;" class="text-center">Edit</th>
                <th style="width: 90px;" class="text-center">Delete</th>
                <th style="width: 90px;" class="text-center">Export</th>
                <th style="width: 90px;" class="text-center">Import</th>
              </tr>
            </thead>
            <tbody>
              <?php if (empty($features)): ?>
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">No features found.</td>
                </tr>
              <?php endif; ?>

              <?php foreach ($features as $feature): ?>
                <?php $fid = (int) $feature['id']; $row = $existing[$fid] ?? null; ?>
                <tr>
                  <td class="check-cell">
                    <input type="checkbox" name="enabled[<?= $fid ?>]" value="1" <?= $row ? 'checked' : '' ?>>
                  </td>
                  <td class="module-cell"><?= esc($feature['module_name']) ?></td>
                  <td class="feature-cell"><?= esc($feature['name']) ?></td>
                  <td class="check-cell">
                    <input type="checkbox" name="can_view[<?= $fid ?>]" value="1" <?= !empty($row['can_view']) ? 'checked' : '' ?>>
                  </td>
                  <td class="check-cell">
                    <input type="checkbox" name="can_add[<?= $fid ?>]" value="1" <?= !empty($row['can_add']) ? 'checked' : '' ?>>
                  </td>
                  <td class="check-cell">
                    <input type="checkbox" name="can_edit[<?= $fid ?>]" value="1" <?= !empty($row['can_edit']) ? 'checked' : '' ?>>
                  </td>
                  <td class="check-cell">
                    <input type="checkbox" name="can_delete[<?= $fid ?>]" value="1" <?= !empty($row['can_delete']) ? 'checked' : '' ?>>
                  </td>
                  <td class="check-cell">
                    <input type="checkbox" name="can_export[<?= $fid ?>]" value="1" <?= !empty($row['can_export']) ? 'checked' : '' ?>>
                  </td>
                  <td class="check-cell">
                    <input type="checkbox" name="can_import[<?= $fid ?>]" value="1" <?= !empty($row['can_import']) ? 'checked' : '' ?>>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="sticky-actions">
          <div class="sticky-actions-inner">
            <div class="sticky-note">
              Changes apply to the selected role after saving.
            </div>

            <div class="d-flex gap-2 flex-wrap">
              <a class="btn btn-outline-secondary" href="<?= site_url('admin/roles') ?>">Cancel</a>
              <button class="btn btn-dark px-4" type="submit">
                <i class="bi bi-check-lg me-1"></i> Save Feature Permissions
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>