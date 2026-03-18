<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0"><?= $mode === 'edit' ? 'Edit User' : 'Create User' ?></h3>
    <div class="text-muted">Admin user account details</div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">Back</a>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post">
      <?= csrf_field() ?>

      <div class="row g-3">
        <div class="col-12 col-lg-4">
          <label class="form-label">Username</label>
          <input type="text" name="username" class="form-control" required value="<?= esc(old('username', $user['username'] ?? '')) ?>">
        </div>

        <div class="col-12 col-lg-4">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= esc(old('email', $user['email'] ?? '')) ?>">
        </div>

        <div class="col-12 col-lg-4">
          <label class="form-label">Name</label>
          <input type="text" name="name" class="form-control" value="<?= esc(old('name', $user['name'] ?? '')) ?>">
        </div>

        <div class="col-12 col-lg-6">
          <label class="form-label">Companies</label>
          <select name="company_ids[]" class="form-select" multiple size="8">
            <?php foreach (($companyOptions ?? []) as $id => $label): ?>
              <option value="<?= esc($id) ?>" <?= in_array((int) $id, $selectedCompanies ?? [], true) ? 'selected' : '' ?>>
                <?= esc($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <div class="form-text">Hold Ctrl or Cmd to select multiple companies.</div>
        </div>

        <div class="col-12 col-lg-3">
          <label class="form-label">Department</label>
          <select name="department_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach (($departmentOptions ?? []) as $id => $label): ?>
              <option value="<?= esc($id) ?>" <?= (string) $id === (string) old('department_id', $user['department_id'] ?? '') ? 'selected' : '' ?>>
                <?= esc($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 col-lg-3">
          <label class="form-label">Role</label>
          <select name="role_id" class="form-select">
            <option value="">-- Select --</option>
            <?php foreach (($roleOptions ?? []) as $id => $label): ?>
              <option value="<?= esc($id) ?>" <?= (string) $id === (string) old('role_id', $user['role_id'] ?? '') ? 'selected' : '' ?>>
                <?= esc($label) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-12 col-lg-4">
          <label class="form-label"><?= $mode === 'edit' ? 'Password (leave blank to keep)' : 'Password' ?></label>
          <input type="password" name="password" class="form-control" <?= $mode === 'create' ? 'required' : '' ?>>
        </div>
      </div>

      <div class="mt-4 d-flex gap-2">
        <button class="btn btn-dark" type="submit">Save</button>
        <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">Cancel</a>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>