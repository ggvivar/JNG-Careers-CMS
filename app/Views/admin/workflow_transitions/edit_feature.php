<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="mb-1"><?= esc($feature['name']) ?> Workflow</h3>
        <div class="text-muted small">Manage statuses and transitions for this feature</div>
    </div>
    <a href="<?= site_url('admin/workflows') ?>" class="btn btn-outline-secondary">
        Back
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header">Add Transition</div>
            <div class="card-body">
                <form method="post">
                    <?= csrf_field() ?>

                    <div class="mb-3">
                        <label class="form-label">From Status</label>
                        <select name="status_id_from" class="form-select">
                            <option value="">-- Initial / Blank --</option>
                            <?php foreach (($statusOptions ?? []) as $id => $name): ?>
                                <option value="<?= esc($id) ?>" <?= old('status_id_from') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">To Status</label>
                        <select name="status_id_to" class="form-select" required>
                            <option value="">-- Select --</option>
                            <?php foreach (($statusOptions ?? []) as $id => $name): ?>
                                <option value="<?= esc($id) ?>" <?= old('status_id_to') == $id ? 'selected' : '' ?>>
                                    <?= esc($name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Notification Days</label>
                        <input type="number" name="grace_period" class="form-control" value="<?= esc(old('grace_period', '')) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Template</label>
                        <select name="email_template_key" class="form-select">
                            <option value="">-- None --</option>
                            <?php foreach (($messageTemplateOptions ?? []) as $key => $label): ?>
                                <option value="<?= esc($key) ?>" <?= (string) $key === (string) old('email_template_key', '') ? 'selected' : '' ?>>
                                    <?= esc($label) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" class="form-control" value="<?= esc(old('sort_order', 0)) ?>">
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="require_remarks" value="1" id="require_remarks" <?= old('require_remarks') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="require_remarks">Require Remarks</label>
                    </div>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="checkbox" name="send_email" value="1" id="send_email" <?= old('send_email') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="send_email">Send Email</label>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="status_id" value="1" id="status_id" <?= old('status_id', '1') ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status_id">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary">Add Transition</button>
                </form>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header">Available Statuses</div>
            <div class="card-body">
                <?php if (empty($statusOptions)): ?>
                    <div class="text-muted">No statuses assigned to this feature.</div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach ($statusOptions as $name): ?>
                            <li class="list-group-item px-0"><?= esc($name) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <?php if (empty($groupedTransitions)): ?>
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-4">
                    No workflow transitions yet.
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($groupedTransitions as $fromStatus => $rows): ?>
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <strong><?= esc($fromStatus) ?></strong>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small">
                                    <tr>
                                        <th>To Status</th>
                                        <th>Days</th>
                                        <th>Email</th>
                                        <th>Template</th>
                                        <th>Sort</th>
                                        <th>Remarks</th>
                                        <th>Active</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rows as $row): ?>
                                        <tr>
                                            <td><?= esc($row['to_status_name']) ?></td>
                                            <td><?= esc($row['grace_period'] ?? '-') ?></td>
                                            <td><?= !empty($row['send_email']) ? 'Yes' : 'No' ?></td>
                                            <td><?= esc($row['email_template_key'] ?? '-') ?></td>
                                            <td><?= esc($row['sort_order']) ?></td>
                                            <td><?= (int) $row['require_remarks'] === 1 ? 'Required' : 'Optional' ?></td>
                                            <td><?= (int) $row['status_id'] === 1 ? 'Yes' : 'No' ?></td>
                                            <td class="text-end">
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <a href="<?= site_url('admin/workflows/edit/' . $row['id']) ?>"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    <form method="post"
                                                          action="<?= site_url('admin/workflows/delete/' . $row['id']) ?>"
                                                          class="d-inline">
                                                        <?= csrf_field() ?>
                                                        <button type="submit"
                                                                class="btn btn-sm btn-outline-danger"
                                                                onclick="return confirm('Delete this transition?')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>