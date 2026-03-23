<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
    .content-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .modern-card {
        border: 1px solid #e9ecef;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06);
        overflow: hidden;
        background: #fff;
    }

    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .section-subtitle {
        font-size: .85rem;
        color: #6c757d;
        margin-top: .2rem;
    }

    .page-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .page-title {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -.02em;
        margin-bottom: .15rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .75rem;
        font-weight: 600;
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.08);
        border: 1px solid rgba(13, 110, 253, 0.12);
        padding: .4rem .7rem;
        border-radius: 999px;
    }

    .modern-card .card-body {
        padding: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        color: #344054;
        margin-bottom: .45rem;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe1e7;
        padding-top: .6rem;
        padding-bottom: .6rem;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12) !important;
    }

    select[multiple].form-select {
        min-height: 220px;
        padding-top: .75rem;
        padding-bottom: .75rem;
    }

    .meta-note,
    .form-text {
        font-size: .8rem;
        color: #98a2b3;
    }

    .sticky-actions {
        position: sticky;
        bottom: 16px;
        z-index: 10;
        margin-top: 1.25rem;
    }

    .sticky-actions-inner {
        display: flex;
        gap: .75rem;
        justify-content: flex-end;
        align-items: center;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(222, 226, 230, 0.8);
        box-shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
        border-radius: 18px;
        padding: .9rem 1rem;
    }

    .btn {
        border-radius: 12px;
    }

    .btn-primary {
        box-shadow: 0 8px 18px rgba(13, 110, 253, 0.18);
    }
</style>

<div class="content-page">
    <div class="page-hero">
        <div>
            <div class="badge-soft mb-2">
                <span>Users</span>
                <span><?= esc($mode === 'edit' ? 'Edit Mode' : 'Create Mode') ?></span>
            </div>
            <h2 class="page-title"><?= esc($mode === 'edit' ? 'Edit User' : 'Create User') ?></h2>
            <p class="page-subtitle">Manage admin user account details, company assignments, and access role.</p>
        </div>

        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form method="post">
        <?= csrf_field() ?>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">User Details</h3>
                <div class="section-subtitle">Basic profile and account information</div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    <div class="col-12 col-lg-4">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
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
                        <div class="form-text mt-2">Hold Ctrl or Cmd to select multiple companies.</div>
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
                        <label class="form-label">
                            <?= $mode === 'edit' ? 'Password (leave blank to keep current password)' : 'Password' ?>
                            <?php if ($mode === 'create'): ?>
                                <span class="text-danger">*</span>
                            <?php endif; ?>
                        </label>
                        <input type="password" name="password" class="form-control" <?= $mode === 'create' ? 'required' : '' ?>>
                        <div class="meta-note mt-1">
                            <?= $mode === 'edit' ? 'Only enter a new password if you want to change it.' : 'Create a secure password for this user.' ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="sticky-actions">
            <div class="sticky-actions-inner">
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/users') ?>">Cancel</a>
                <button class="btn btn-primary px-4" type="submit">
                    <i class="bi bi-check-lg me-1"></i> Save User
                </button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>