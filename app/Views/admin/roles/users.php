<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0">Assign Users</h3>
    <div class="text-muted">Role: <?= esc($role['name']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/roles') ?>">Back</a>
</div>

<div class="card shadow-sm mb-3">
  <div class="card-body">
    <form method="post" class="row g-2 align-items-end">
      <?= csrf_field() ?>
      <div class="col-12 col-lg-6">
        <label class="form-label">User</label>
        <select name="user_id" class="form-select" required>
          <option value="">-- Select User --</option>
          <?php foreach (($users ?? []) as $user): ?>
            <option value="<?= esc($user['id']) ?>">
              <?= esc(($user['name'] ?: $user['username']) . ' (' . $user['username'] . ')') ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-auto">
        <button class="btn btn-dark" type="submit">Assign</button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>