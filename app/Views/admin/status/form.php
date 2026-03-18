<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0"><?= $mode === 'edit' ? 'Edit Status' : 'Create Status' ?></h3>
    <div class="text-muted">Assign status to one or more features</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/status') ?>">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-12 col-lg-6">
          <label class="form-label">Status Name</label>
          <input type="text" name="name" class="form-control" required value="<?= esc(old('name', $status['name'] ?? '')) ?>">
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label">Features</label>
          <select name="feature_ids[]" class="form-select" multiple size="10" required>
            <?php foreach (($featureOptions ?? []) as $id => $label): ?>
              <option value="<?= esc($id) ?>" <?= in_array((int) $id, $selectedFeatures ?? [], true) ? 'selected' : '' ?>>
                <?= esc($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Hold Ctrl or Cmd to select multiple features.</div>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-dark" type="submit">Save</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/status') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>