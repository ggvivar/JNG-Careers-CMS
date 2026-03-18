<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0">Feature Permissions</h3>
    <div class="text-muted">Role: <?= esc($role['name']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/roles') ?>">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post">
      <?= csrf_field() ?>

      <div class="table-responsive">
        <table class="table table-bordered align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:90px;">Enable</th>
              <th>Module</th>
              <th>Feature</th>
              <th style="width:80px;">View</th>
              <th style="width:80px;">Add</th>
              <th style="width:80px;">Edit</th>
              <th style="width:80px;">Delete</th>
              <th style="width:80px;">Export</th>
              <th style="width:80px;">Import</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($features as $feature): ?>
              <?php $fid = (int) $feature['id']; $row = $existing[$fid] ?? null; ?>
              <tr>
                <td class="text-center"><input type="checkbox" name="enabled[<?= $fid ?>]" value="1" <?= $row ? 'checked' : '' ?>></td>
                <td><?= esc($feature['module_name']) ?></td>
                <td class="fw-semibold"><?= esc($feature['name']) ?></td>
                <td class="text-center"><input type="checkbox" name="can_view[<?= $fid ?>]" value="1" <?= !empty($row['can_view']) ? 'checked' : '' ?>></td>
                <td class="text-center"><input type="checkbox" name="can_add[<?= $fid ?>]" value="1" <?= !empty($row['can_add']) ? 'checked' : '' ?>></td>
                <td class="text-center"><input type="checkbox" name="can_edit[<?= $fid ?>]" value="1" <?= !empty($row['can_edit']) ? 'checked' : '' ?>></td>
                <td class="text-center"><input type="checkbox" name="can_delete[<?= $fid ?>]" value="1" <?= !empty($row['can_delete']) ? 'checked' : '' ?>></td>
                <td class="text-center"><input type="checkbox" name="can_export[<?= $fid ?>]" value="1" <?= !empty($row['can_export']) ? 'checked' : '' ?>></td>
                <td class="text-center"><input type="checkbox" name="can_import[<?= $fid ?>]" value="1" <?= !empty($row['can_import']) ? 'checked' : '' ?>></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <button class="btn btn-dark" type="submit">Save Feature Permissions</button>
    </form>
  </div>
</div>

<?= $this->endSection() ?>