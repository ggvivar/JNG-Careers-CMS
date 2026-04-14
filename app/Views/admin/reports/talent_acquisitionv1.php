<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
.report-card,
.report-panel{
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 4px 14px rgba(15,23,42,.04);
}
.report-card{
    padding: 18px;
    height: 100%;
}
.report-kicker{
    font-size: .78rem;
    text-transform: uppercase;
    color: #6b7280;
    letter-spacing: .04em;
    margin-bottom: 8px;
}
.report-value{
    font-size: 1.9rem;
    font-weight: 700;
    color: #111827;
    line-height: 1.1;
}
.report-sub{
    color: #6b7280;
    font-size: .88rem;
    margin-top: 6px;
}
.report-panel .panel-header{
    padding: 16px 18px;
    border-bottom: 1px solid #f1f5f9;
}
.report-panel .panel-body{
    padding: 18px;
}
.badge-soft-danger{
    background: #fee2e2;
    color: #b91c1c;
}
.badge-soft-success{
    background: #dcfce7;
    color: #166534;
}
.badge-soft-warning{
    background: #fef3c7;
    color: #92400e;
}
.badge-soft-secondary{
    background: #f3f4f6;
    color: #374151;
}
.table td, .table th{
    vertical-align: middle;
}
</style>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h3 class="mb-1">Talent Acquisition Report</h3>
        <div class="text-muted small">Recruitment analytics based on applications, workload, approvals, and overdue monitoring</div>
    </div>
</div>

<div class="report-panel mb-4">
    <div class="panel-header">
        <h5 class="mb-0">Filters</h5>
    </div>
    <div class="panel-body">
        <form method="get" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" class="form-control" value="<?= esc($dateFrom) ?>">
            </div>

            <div class="col-12 col-md-3">
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" class="form-control" value="<?= esc($dateTo) ?>">
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label">Source</label>
                <select name="source" class="form-select">
                    <option value="">All Sources</option>
                    <?php foreach (($sourceOptions ?? []) as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= (string)$selectedSource === (string)$key ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label">Status</label>
                <select name="status_id" class="form-select">
                    <option value="">All Statuses</option>
                    <?php foreach (($statusOptions ?? []) as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= (string)$selectedStatusId === (string)$key ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-2">
                <label class="form-label">Assigned User</label>
                <select name="assigned_to" class="form-select">
                    <option value="">All Users</option>
                    <?php foreach (($assignedUserOptions ?? []) as $key => $label): ?>
                        <option value="<?= esc($key) ?>" <?= (string)$selectedAssignedTo === (string)$key ? 'selected' : '' ?>>
                            <?= esc($label) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel me-1"></i> Apply Filters
                </button>
                <a href="<?= site_url('admin/reports/talent-acquisition') ?>" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Applications</div>
            <div class="report-value"><?= number_format((int)($stats['applications'] ?? 0)) ?></div>
            <div class="report-sub">Total applications in selected period</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">My Processing</div>
            <div class="report-value"><?= number_format((int)($stats['my_processing'] ?? 0)) ?></div>
            <div class="report-sub">Applications assigned to current user</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Unique Applicants</div>
            <div class="report-value"><?= number_format((int)$totalApplicants) ?></div>
            <div class="report-sub">Distinct candidates in selected period</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Jobs Applied</div>
            <div class="report-value"><?= number_format((int)$totalJobs) ?></div>
            <div class="report-sub">Distinct job posts with applications</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">For Review</div>
            <div class="report-value"><?= number_format((int)($statsForApproval['applications'] ?? 0)) ?></div>
            <div class="report-sub">Applications currently pending review</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Applications Overdue</div>
            <div class="report-value text-danger"><?= number_format((int)($overdueStats['applications_overdue'] ?? 0)) ?></div>
            <div class="report-sub">Past due date</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">My Overdue</div>
            <div class="report-value text-warning"><?= number_format((int)($myProcessingOverdue ?? 0)) ?></div>
            <div class="report-sub">Assigned to me and overdue</div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Hired</div>
            <div class="report-value text-success"><?= number_format((int)$hiredCount) ?></div>
            <div class="report-sub">Successful applications</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Failed / Declined</div>
            <div class="report-value"><?= number_format((int)$failedCount) ?></div>
            <div class="report-sub">Unsuccessful application outcomes</div>
        </div>
    </div>

    <div class="col-12 col-md-6 col-xl-3">
        <div class="report-card">
            <div class="report-kicker">Withdrawn</div>
            <div class="report-value"><?= number_format((int)$withdrawnCount) ?></div>
            <div class="report-sub">Candidate withdrawals</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-xl-6">
        <div class="report-panel h-100">
            <div class="panel-header">
                <h5 class="mb-0">Status Breakdown</h5>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Status</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($statusBreakdown)): ?>
                            <?php foreach ($statusBreakdown as $row): ?>
                                <tr>
                                    <td><?= esc($row['status_name'] ?: '-') ?></td>
                                    <td class="text-end fw-semibold"><?= number_format((int)$row['total_count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No data found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="report-panel h-100">
            <div class="panel-header">
                <h5 class="mb-0">Source Breakdown</h5>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Source</th>
                                <th class="text-end">Count</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($sourceBreakdown)): ?>
                            <?php foreach ($sourceBreakdown as $row): ?>
                                <tr>
                                    <td><?= esc($row['source_name'] ?: '-') ?></td>
                                    <td class="text-end fw-semibold"><?= number_format((int)$row['total_count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No data found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-xl-6">
        <div class="report-panel h-100">
            <div class="panel-header">
                <h5 class="mb-0">Processor Workload</h5>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Processor</th>
                                <th class="text-end">Applications</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($processorWorkload)): ?>
                            <?php foreach ($processorWorkload as $row): ?>
                                <tr>
                                    <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
                                    <td class="text-end fw-semibold"><?= number_format((int)$row['total_count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No data found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-xl-6">
        <div class="report-panel h-100">
            <div class="panel-header">
                <h5 class="mb-0">Top Jobs by Applications</h5>
            </div>
            <div class="panel-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Job</th>
                                <th class="text-end">Applications</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if (!empty($jobBreakdown)): ?>
                            <?php foreach ($jobBreakdown as $row): ?>
                                <tr>
                                    <td><?= esc($row['job_name'] ?: '-') ?></td>
                                    <td class="text-end fw-semibold"><?= number_format((int)$row['total_count']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-muted py-4">No data found.</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="report-panel mb-4">
    <div class="panel-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0 text-danger">Overdue Applications</h5>
        <a href="<?= site_url('admin/applications?overdue=1') ?>" class="btn btn-sm btn-outline-danger">View All</a>
    </div>
    <div class="panel-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Job</th>
                        <th>Status</th>
                        <th>Processor</th>
                        <th>Applied At</th>
                        <th>Due At</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($overdueApplications)): ?>
                    <?php foreach ($overdueApplications as $row): ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('admin/applications/' . (int)$row['id']) ?>" class="fw-semibold text-decoration-none">
                                    #<?= (int)$row['id'] ?>
                                </a>
                            </td>
                            <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
                            <td><?= esc($row['job_name'] ?: '-') ?></td>
                            <td><span class="badge badge-soft-secondary"><?= esc($row['status_name'] ?: '-') ?></span></td>
                            <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
                            <td><?= esc($row['applied_at'] ?: '-') ?></td>
                            <td>
                                <span class="badge badge-soft-danger">Overdue</span>
                                <div class="small text-danger mt-1"><?= esc($row['due_at'] ?: '-') ?></div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No overdue applications found.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="report-panel">
    <div class="panel-header">
        <h5 class="mb-0">Recent Applications</h5>
    </div>
    <div class="panel-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Applicant</th>
                        <th>Email</th>
                        <th>Job</th>
                        <th>Status</th>
                        <th>Processor</th>
                        <th>Applied At</th>
                        <th>Due At</th>
                        <th>Source</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($recentApplications)): ?>
                    <?php foreach ($recentApplications as $row): ?>
                        <?php $isOverdue = !empty($row['due_at']) && strtotime($row['due_at']) < time(); ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('admin/applications/' . (int)$row['id']) ?>" class="fw-semibold text-decoration-none">
                                    #<?= (int)$row['id'] ?>
                                </a>
                            </td>
                            <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
                            <td><?= esc($row['email'] ?: '-') ?></td>
                            <td><?= esc($row['job_name'] ?: '-') ?></td>
                            <td><span class="badge badge-soft-secondary"><?= esc($row['status_name'] ?: '-') ?></span></td>
                            <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
                            <td><?= esc($row['applied_at'] ?: '-') ?></td>
                            <td>
                                <?php if (empty($row['due_at'])): ?>
                                    <span class="text-muted">-</span>
                                <?php elseif ($isOverdue): ?>
                                    <span class="badge badge-soft-danger">Overdue</span>
                                    <div class="small text-danger mt-1"><?= esc($row['due_at']) ?></div>
                                <?php else: ?>
                                    <span class="badge badge-soft-success">On Time</span>
                                    <div class="small text-muted mt-1"><?= esc($row['due_at']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><?= esc($row['source'] ?: '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No applications found for selected period.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>