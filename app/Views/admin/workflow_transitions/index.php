<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1">Workflows</h3>
        <div class="text-muted small">Manage workflow per feature</div>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="get" action="<?= current_url() ?>" class="row g-2">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        value="<?= esc($searchQuery ?? '') ?>"
                        placeholder="Search workflows..."
                    >
                </div>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
            </div>
            <?php if (!empty($searchQuery)): ?>
                <div class="col-auto">
                    <a href="<?= current_url() ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th>Feature</th>
                        <th>Code</th>
                        <th>Transitions</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                No workflows found.
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><?= esc($row['feature_name']) ?> Workflow</td>
                            <td><code><?= esc($row['feature_code']) ?></code></td>
                            <td><?= esc($row['transition_count']) ?></td>
                            <td class="text-end">
                                <a href="<?= site_url('admin/workflows/edit-feature/' . $row['feature_code']) ?>"
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i> Edit Workflow
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>