<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
  .assign-users-page {
    width: 100%;
    max-width: 100%;
  }

  .assign-users-hero {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
  }

  .assign-users-title {
    font-size: 1.8rem;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -0.02em;
    margin-bottom: 0.2rem;
    color: #1f2937;
  }

  .assign-users-subtitle {
    color: #6b7280;
    font-size: 1rem;
    margin: 0;
  }

  .role-badge-soft {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin-top: 0.65rem;
    padding: 0.45rem 0.8rem;
    border-radius: 999px;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    color: #475467;
    font-size: 0.85rem;
    font-weight: 700;
  }

  .assign-users-card {
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    max-width: 1100px;
  }

  .assign-users-card-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #eef2f7;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
  }

  .assign-users-card-title {
    margin: 0;
    font-size: 1rem;
    font-weight: 700;
    color: #111827;
  }

  .assign-users-card-subtitle {
    margin-top: 0.2rem;
    color: #6b7280;
    font-size: 0.92rem;
  }

  .assign-users-card-body {
    padding: 1.25rem;
  }

  .assign-users-form .form-label {
    font-size: 0.95rem;
    font-weight: 700;
    color: #374151;
    margin-bottom: 0.5rem;
  }

  .assign-users-form .form-select {
    min-height: 50px;
    border-radius: 14px;
    border-color: #dbe3ec;
    padding-left: 0.95rem;
    padding-right: 2.5rem;
    font-size: 0.98rem;
    box-shadow: none !important;
  }

  .assign-users-form .form-select:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12) !important;
  }

  .assign-users-help {
    margin-top: 0.75rem;
    color: #6b7280;
    font-size: 0.92rem;
  }

  .assign-users-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
    align-items: center;
    margin-top: 1.25rem;
  }

  .assign-users-actions .btn {
    min-width: 120px;
    border-radius: 12px;
  }

  .assign-users-actions .btn-dark {
    box-shadow: 0 10px 18px rgba(17, 24, 39, 0.18);
  }

  @media (max-width: 768px) {
    .assign-users-title {
      font-size: 1.5rem;
    }

    .assign-users-card-body {
      padding: 1rem;
    }

    .assign-users-actions {
      flex-direction: column-reverse;
      align-items: stretch;
    }

    .assign-users-actions .btn {
      width: 100%;
    }
  }
</style>

<div class="assign-users-page">
  <div class="assign-users-hero">
    <div>
      <h1 class="assign-users-title">Assign Users</h1>
      <p class="assign-users-subtitle">Assign a user account to this role.</p>
      <div class="role-badge-soft">
        <i class="bi bi-person-badge"></i>
        Role: <?= esc($role['name']) ?>
      </div>
    </div>

    <a class="btn btn-outline-secondary" href="<?= site_url('admin/roles') ?>">
      <i class="bi bi-arrow-left me-1"></i> Back
    </a>
  </div>

  <div class="assign-users-card">
    <div class="assign-users-card-header">
      <h3 class="assign-users-card-title">User Assignment</h3>
      <div class="assign-users-card-subtitle">Select one user and assign them to the selected role.</div>
    </div>

    <div class="assign-users-card-body">
      <form method="post" class="assign-users-form">
        <?= csrf_field() ?>

        <div class="row g-3 align-items-end">
          <div class="col-12 col-xl-8">
            <label class="form-label">User</label>
            <select name="user_id" class="form-select" required>
              <option value="">-- Select User --</option>
              <?php foreach (($users ?? []) as $user): ?>
                <option value="<?= esc($user['id']) ?>">
                  <?= esc(($user['name'] ?: $user['username']) . ' (' . $user['username'] . ')') ?>
                </option>
              <?php endforeach; ?>
            </select>
            <div class="assign-users-help">
              Choose the account you want to associate with this role.
            </div>
          </div>
        </div>

        <div class="assign-users-actions">
          <a class="btn btn-outline-secondary" href="<?= site_url('admin/roles') ?>">Cancel</a>
          <button class="btn btn-dark" type="submit">
            <i class="bi bi-person-plus me-1"></i> Assign
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>