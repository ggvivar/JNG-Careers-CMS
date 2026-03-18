<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0">Role Permissions</h3>
    <div class="text-muted">
      Configure permissions for role:
      <strong><?= esc($role['name'] ?? '') ?></strong>
    </div>
  </div>

  <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-secondary">
    Back
  </a>
</div>

<form method="post">
<?= csrf_field() ?>

<?php foreach (($groupedFeatures ?? []) as $moduleName => $features): ?>

<div class="card shadow-sm mb-4">
  <div class="card-header bg-light fw-semibold">
    <?= esc($moduleName) ?>
  </div>

  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-bordered table-hover align-middle mb-0">

        <thead class="table-light">
          <tr>
            <th style="width:260px">Feature</th>
            <th class="text-center">View</th>
            <th class="text-center">Add</th>
            <th class="text-center">Edit</th>
            <th class="text-center">Delete</th>
            <th class="text-center">Export</th>
            <th class="text-center">Import</th>
          </tr>
        </thead>

        <tbody>

        <?php foreach ($features as $feature): ?>

        <tr>
          <td>
            <div class="fw-semibold">
              <?= esc($feature['feature_name']) ?>
            </div>

            <div class="small text-muted">
              <?= esc($feature['feature_code']) ?>
            </div>
          </td>

          <?php foreach ([
            'can_view',
            'can_add',
            'can_edit',
            'can_delete',
            'can_export',
            'can_import'
          ] as $perm): ?>

          <td class="text-center">

            <input
              type="checkbox"
              class="form-check-input"
              name="permissions[<?= (int)$feature['id'] ?>][<?= esc($perm) ?>]"
              value="1"
              <?= !empty($feature['permissions'][$perm]) ? 'checked' : '' ?>
            >

          </td>

          <?php endforeach; ?>

        </tr>

        <?php endforeach; ?>

        </tbody>

      </table>
    </div>
  </div>
</div>

<?php endforeach; ?>


<div class="mt-3 d-flex gap-2">
  <button type="submit" class="btn btn-dark">
    Save Permissions
  </button>

  <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-secondary">
    Cancel
  </a>
</div>

</form>

<?= $this->endSection() ?>