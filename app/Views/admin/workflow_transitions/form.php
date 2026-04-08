<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h3 class="mb-0">Edit Workflow Transition</h3>
        <div class="text-muted">Update transition for <?= esc($feature['name'] ?? 'Feature') ?></div>
    </div>
    <a class="btn btn-outline-secondary" href="<?= site_url('admin/workflows/edit-feature/' . ($feature['code'] ?? '')) ?>">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">From Status</label>
                    <select name="status_id_from" class="form-select">
                        <option value="">-- Initial / Blank --</option>
                        <?php foreach (($statusOptions ?? []) as $id => $name): ?>
                            <option value="<?= esc($id) ?>" <?= (string) $id === (string) old('status_id_from', $row['status_id_from'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">To Status</label>
                    <select name="status_id_to" class="form-select" required>
                        <option value="">-- Select --</option>
                        <?php foreach (($statusOptions ?? []) as $id => $name): ?>
                            <option value="<?= esc($id) ?>" <?= (string) $id === (string) old('status_id_to', $row['status_id_to'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Notification Days</label>
                    <input type="number" name="grace_period" class="form-control" value="<?= esc(old('grace_period', $row['grace_period'] ?? '')) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?= esc(old('sort_order', $row['sort_order'] ?? 0)) ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label">Email Template</label>
                    <select name="email_template_key" class="form-select">
                        <option value="">-- None --</option>
                        <?php foreach (($messageTemplateOptions ?? []) as $key => $label): ?>
                            <option value="<?= esc($key) ?>" <?= (string) $key === (string) old('email_template_key', $row['email_template_key'] ?? '') ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" value="1" id="require_remarks" name="require_remarks" <?= old('require_remarks', $row['require_remarks'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="require_remarks">Require Remarks</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" value="1" id="send_email" name="send_email" <?= old('send_email', $row['send_email'] ?? 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="send_email">Send Email</label>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="form-check mt-4">
                        <input class="form-check-input" type="checkbox" value="1" id="status_id" name="status_id" <?= (string) old('status_id', $row['status_id'] ?? 1) === '1' ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_id">Active</label>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/workflows/edit-feature/' . ($feature['code'] ?? '')) ?>">Cancel</a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>