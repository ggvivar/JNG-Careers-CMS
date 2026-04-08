<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-0">My Profile</h3>
        <div class="text-muted small">Manage your account information and password</div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-5">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;font-size:24px;">
                        <i class="bi bi-person"></i>
                    </div>
                    <div>
                        <h5 class="mb-0"><?= esc($user['name'] ?: $user['username']) ?></h5>
                        <div class="text-muted small">@<?= esc($user['username']) ?></div>
                    </div>
                </div>

                <hr>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Full Name</div>
                    <div class="fw-semibold"><?= esc($user['name'] ?: '-') ?></div>
                </div>

                <div class="mb-3">
                    <div class="text-muted small mb-1">Username</div>
                    <div class="fw-semibold"><?= esc($user['username']) ?></div>
                </div>

                <div class="mb-0">
                    <div class="text-muted small mb-1">Role</div>
                    <div class="fw-semibold"><?= esc($user['role_name'] ?: '-') ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="mb-3">Change Password</h5>

                <form action="<?= site_url('admin/profile/change-password') ?>" method="post" autocomplete="off">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">Current Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password"
                                   class="form-control"
                                   id="current_password"
                                   name="current_password"
                                   required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label fw-semibold">New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                            <input type="password"
                                   class="form-control"
                                   id="new_password"
                                   name="new_password"
                                   minlength="8"
                                   required>
                        </div>
                        <div class="form-text">Use at least 8 characters.</div>
                    </div>

                    <div class="mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">Confirm New Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-check2-circle"></i></span>
                            <input type="password"
                                   class="form-control"
                                   id="confirm_password"
                                   name="confirm_password"
                                   minlength="8"
                                   required>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-key me-1"></i>
                            Update Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>