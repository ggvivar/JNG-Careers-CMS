<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0">Application #<?= esc($application['id']) ?></h3>
    <div class="text-muted"><?= esc(trim(($application['firstname'] ?? '') . ' ' . ($application['lastname'] ?? ''))) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/applications') ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5>Application Details</h5>
        <p class="mb-1"><strong>Job:</strong> <?= esc($application['job_name'] ?? '-') ?></p>
        <p class="mb-1"><strong>Email:</strong> <?= esc($application['email'] ?? '-') ?></p>
        <p class="mb-1"><strong>Phone:</strong> <?= esc($application['phone'] ?? '-') ?></p>
        <p class="mb-1"><strong>Source:</strong> <?= esc($application['source'] ?? '-') ?></p>
        <p class="mb-0"><strong>Status:</strong> <?= esc($application['status_name'] ?? '-') ?></p>
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5>Update Status</h5>
        <form method="post" action="<?= site_url('admin/applications/' . $application['id'] . '/status') ?>">
          <?= csrf_field() ?>
          <div class="mb-3">
            <label class="form-label">Status</label>
            <select name="status_id" class="form-select" required>
              <option value="">-- Select --</option>
              <?php foreach (($statusOptions ?? []) as $id => $label): ?>
                <option value="<?= esc($id) ?>" <?= (string) $id === (string) ($application['status_id'] ?? '') ? 'selected' : '' ?>>
                  <?= esc($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn btn-dark" type="submit">Update Status</button>
        </form>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>