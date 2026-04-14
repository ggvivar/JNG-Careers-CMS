<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$generatedAt = date('F d, Y h:i A');

$totalApplications   = (int) ($stats['applications'] ?? 0);
$myProcessing        = (int) ($stats['my_processing'] ?? 0);
$forReview           = (int) ($statsForApproval['applications'] ?? 0);
$applicationsOverdue = (int) ($overdueStats['applications_overdue'] ?? 0);
$myOverdue           = (int) ($myProcessingOverdue ?? 0);

$vacantCount = max(
    0,
    $totalApplications
    - (int) $hiredCount
    - (int) $declinedCount
    - (int) $failedCount
    - (int) $withdrawnCount
    - (int) $noShowCount
);
?>

<style>
.ta-report-page{color:#1f2937}
.ta-cover,.ta-panel,.ta-stat-card{background:#fff;border:1px solid #e6eaf2;border-radius:20px;box-shadow:0 6px 18px rgba(15,23,42,.05)}
.ta-cover{overflow:hidden;position:relative;min-height:220px}
/* .ta-cover::before{content:"";position:absolute;top:-60px;left:-40px;width:220px;height:220px;border-radius:50%;background:linear-gradient(135deg,#f7b500 0%,#ffcf4d 100%);opacity:.95}
.ta-cover::after{content:"";position:absolute;right:-80px;bottom:-80px;width:260px;height:260px;border-radius:50%;background:linear-gradient(135deg,#0a43c8 0%,#2d68ff 100%);opacity:.92} */
.ta-cover-inner{position:relative;z-index:1;padding:28px 28px 24px}
.ta-brand-row{display:flex;align-items:center;gap:14px;margin-bottom:26px;flex-wrap:wrap}
.ta-brand-badge{display:inline-flex;align-items:center;justify-content:center;width:52px;height:52px;border-radius:14px;background:#fff7db;color:#d69400;font-weight:800;font-size:22px;border:1px solid rgba(214,148,0,.15)}
.ta-brand-text{font-size:13px;color:#6b7280;text-transform:uppercase;letter-spacing:.08em;font-weight:700}
.ta-title{font-size:2.1rem;line-height:1.02;font-weight:800;margin:0 0 8px 0;color:#111827;max-width:720px}
.ta-subtitle{font-size:1.05rem;color:#374151;margin:0 0 14px 0}
.ta-meta{display:flex;gap:12px;flex-wrap:wrap}
.ta-pill{display:inline-flex;align-items:center;gap:8px;background:rgba(255,255,255,.92);border:1px solid #e8ecf5;color:#374151;padding:8px 12px;border-radius:999px;font-size:.85rem;font-weight:600}
.ta-stat-card{padding:18px;height:100%}
.ta-stat-label{font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:#6b7280;margin-bottom:10px;font-weight:700}
.ta-stat-value{font-size:2rem;line-height:1;font-weight:800;color:#111827}
.ta-stat-sub{font-size:.86rem;color:#6b7280;margin-top:8px}
.ta-stat-value.success{color:#198754}
.ta-stat-value.danger{color:#dc3545}
.ta-stat-value.warning{color:#d97706}
.ta-panel{overflow:hidden}
.ta-panel-header{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 20px;border-bottom:1px solid #eef2f7;background:linear-gradient(180deg,#ffffff 0%,#fbfcfe 100%)}
.ta-panel-title{margin:0;font-size:1.1rem;font-weight:800;color:#111827}
.ta-panel-sub{margin:3px 0 0;color:#6b7280;font-size:.85rem}
.ta-panel-body{padding:18px 20px}
.ta-filter-grid .form-label{font-size:.82rem;font-weight:700;color:#4b5563}
.ta-filter-grid .form-control,.ta-filter-grid .form-select{border-radius:12px;min-height:42px}
.ta-table{margin-bottom:0}
.ta-table thead th{background:#f8fafc;color:#475569;font-size:.8rem;text-transform:uppercase;letter-spacing:.04em;border-bottom:1px solid #e9eef5;white-space:nowrap}
.ta-table td{vertical-align:middle}
.ta-muted{color:#6b7280}
.ta-kpi-note{font-size:.92rem;color:#4b5563;line-height:1.6}
.ta-mini-kpi{border:1px solid #edf1f7;border-radius:16px;padding:16px;background:#fbfcfe;height:100%}
.ta-mini-kpi-title{font-size:.76rem;text-transform:uppercase;color:#6b7280;font-weight:700;margin-bottom:8px}
.ta-mini-kpi-value{font-size:1.5rem;font-weight:800;color:#111827}
.ta-badge-soft{display:inline-flex;align-items:center;padding:6px 10px;border-radius:999px;font-size:.76rem;font-weight:700}
.ta-badge-soft.gray{background:#f3f4f6;color:#374151}
.ta-badge-soft.blue{background:#dbeafe;color:#1d4ed8}
.ta-badge-soft.green{background:#dcfce7;color:#166534}
.ta-badge-soft.orange{background:#ffedd5;color:#c2410c}
.ta-badge-soft.red{background:#fee2e2;color:#b91c1c}
.ta-summary-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
@media (max-width:1199px){.ta-summary-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media (max-width:767px){.ta-title{font-size:1.55rem}.ta-summary-grid{grid-template-columns:1fr}}
</style>

<div class="ta-report-page">

    <div class="ta-cover mb-4">
        <div class="ta-cover-inner">
            <div class="ta-brand-row">
                <!-- <div class="ta-brand-badge">JN</div> -->
                <div>
                    <div class="ta-brand-text">Joy-Nostalg Group</div>
                    <div class="small text-muted">Talent Acquisition Operations</div>
                </div>
            </div>

            <h1 class="ta-title">Talent Acquisition Operations</h1>
            <p class="ta-subtitle">
                <!-- Report Period: <?= esc(date('F d, Y', strtotime($dateFrom))) ?> to <?= esc(date('F d, Y', strtotime($dateTo))) ?> -->
            </p>

            <div class="ta-meta">
                <!-- <span class="ta-pill"><i class="bi bi-calendar3"></i><?= esc(date('F Y', strtotime($dateFrom))) ?></span>
                <span class="ta-pill"><i class="bi bi-clock-history"></i>Generated <?= esc($generatedAt) ?></span>
                <span class="ta-pill"><i class="bi bi-bar-chart-line"></i>ATS Executive Report</span> -->
            </div>
        </div>
    </div>

    <div class="ta-panel mb-4">
        <div class="ta-panel-header">
            <div>
                <h5 class="ta-panel-title">Report Filters</h5>
                <div class="ta-panel-sub">Reporting period and application segments</div>
            </div>
        </div>
        <div class="ta-panel-body">
            <form method="get" class="row g-3 ta-filter-grid">
                <div class="col-12 col-md-3">
                    <label class="form-label">Date From</label>
                    <input type="date" name="date_from" class="form-control" value="<?= esc($dateFrom) ?>">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label">Date To</label>
                    <input type="date" name="date_to" class="form-control" value="<?= esc($dateTo) ?>">
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Company</label>
                    <select name="company_id" class="form-select">
                        <option value="">All Companies</option>
                        <?php foreach (($companyOptions ?? []) as $key => $label): ?>
                            <option value="<?= esc($key) ?>" <?= (string)($selectedCompanyId ?? '') === (string)$key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-2">
                    <label class="form-label">Source</label>
                    <select name="source" class="form-select">
                        <option value="">All Sources</option>
                        <?php foreach (($sourceOptions ?? []) as $key => $label): ?>
                            <option value="<?= esc($key) ?>" <?= (string)($selectedSource ?? '') === (string)$key ? 'selected' : '' ?>>
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
                            <option value="<?= esc($key) ?>" <?= (string)($selectedStatusId ?? '') === (string)$key ? 'selected' : '' ?>>
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
                            <option value="<?= esc($key) ?>" <?= (string)($selectedAssignedTo ?? '') === (string)$key ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-funnel me-1"></i> Apply Filters
                    </button>
                    <a href="<?= site_url('admin/reports/talent-acquisition') ?>" class="btn btn-outline-secondary">Reset</a>
                <a href="<?= site_url('admin/reports/talent-acquisition/download') ?>" 
                class="btn btn-primary">
                <i class="bi bi-download"></i>
                </a>
                </div>
                
            </form>
        </div>
    </div>

    <div class="ta-summary-grid mb-4">
        <div class="ta-stat-card">
            <div class="ta-stat-label">Grand Total</div>
            <div class="ta-stat-value"><?= number_format($totalApplications) ?></div>
            <div class="ta-stat-sub">Total applications within selected period</div>
        </div>

        <div class="ta-stat-card">
            <div class="ta-stat-label">Hired</div>
            <div class="ta-stat-value success"><?= number_format((int) $hiredCount) ?></div>
            <div class="ta-stat-sub">Successful application outcomes</div>
        </div>

        <div class="ta-stat-card">
            <div class="ta-stat-label">Vacant / Open</div>
            <div class="ta-stat-value"><?= number_format($vacantCount) ?></div>
            <div class="ta-stat-sub">Remaining open manpower pipeline</div>
        </div>

        <div class="ta-stat-card">
            <div class="ta-stat-label">For Review</div>
            <div class="ta-stat-value warning"><?= number_format($forReview) ?></div>
            <div class="ta-stat-sub">Applications pending review</div>
        </div>
        <div class="ta-stat-card">
            <div class="ta-stat-label">On Hold</div>
            <div class="ta-stat-value warning"><?= number_format($forReview) ?></div>
            <div class="ta-stat-sub">Applications waiting for approval</div>
        </div>
        <div class="ta-stat-card">
            <div class="ta-stat-label">Declined</div>
            <div class="ta-stat-value"><?= number_format((int) $declinedCount) ?></div>
            <div class="ta-stat-sub">Total declined</div>
        </div>

        <div class="ta-stat-card">
            <div class="ta-stat-label">Failed / Withdrawn / No Show</div>
            <div class="ta-stat-value"><?= number_format((int) $failedCount + (int) $withdrawnCount + (int) $noShowCount) ?></div>
            <div class="ta-stat-sub">Total Other closed unsuccessful</div>
        </div>

        <div class="ta-stat-card">
            <div class="ta-stat-label">Applications Overdue</div>
            <div class="ta-stat-value danger"><?= number_format($applicationsOverdue) ?></div>
            <div class="ta-stat-sub">Total Past Due Applications</div>
        </div>

        <!-- <div class="ta-stat-card">
            <div class="ta-stat-label">My Processing</div>
            <div class="ta-stat-value"><?= number_format($myProcessing) ?></div>
            <div class="ta-stat-sub">Assigned to current user</div>
        </div> -->
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Offer Success Snapshot</h5>
                        <div class="ta-panel-sub">Derived from offer / hired / declined statuses</div>
                    </div>
                </div>
                <div class="ta-panel-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <div class="ta-mini-kpi">
                                <div class="ta-mini-kpi-title">Offers</div>
                                <div class="ta-mini-kpi-value"><?= number_format((int) $offerTotal) ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="ta-mini-kpi">
                                <div class="ta-mini-kpi-title">Accepted</div>
                                <div class="ta-mini-kpi-value text-success"><?= number_format((int) $offerAccepted) ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="ta-mini-kpi">
                                <div class="ta-mini-kpi-title">Declined</div>
                                <div class="ta-mini-kpi-value"><?= number_format((int) $offerDeclined) ?></div>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <div class="ta-mini-kpi">
                                <div class="ta-mini-kpi-title">Success Rate</div>
                                <div class="ta-mini-kpi-value"><?= number_format((int) $offerSuccessRate) ?>%</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Hiring Turnaround Time</h5>
                        <div class="ta-panel-sub">Using MRF / posted date as the start date</div>
                    </div>
                </div>
                <div class="ta-panel-body">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <div class="ta-mini-kpi">
                                <div class="ta-mini-kpi-title">Average Time to Fill</div>
                                <div class="ta-mini-kpi-value"><?= number_format((int) $averageTimeToFill) ?> days</div>
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="ta-mini-kpi">
                                <div class="ta-mini-kpi-title">Average Days Open</div>
                                <div class="ta-mini-kpi-value"><?= number_format((int) $averageDaysOpen) ?> days</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 ta-kpi-note">
                        Time-to-fill and open-days are computed from <strong>job posted date</strong> to closed date or today.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Status Breakdown</h5>
                        <div class="ta-panel-sub">Detailed workflow status counts</div>
                    </div>
                </div>
                <div class="ta-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table ta-table table-hover align-middle">
                            <thead>
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
                                        <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
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
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Sourcing Channels</h5>
                        <div class="ta-panel-sub">Application source contribution</div>
                    </div>
                </div>
                <div class="ta-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table ta-table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th class="text-end">Applications</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($sourceBreakdown)): ?>
                                <?php foreach ($sourceBreakdown as $row): ?>
                                    <tr>
                                        <td><?= esc($row['source_name'] ?: '-') ?></td>
                                        <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
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

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Status per Level</h5>
                        <div class="ta-panel-sub">Using experience range as level</div>
                    </div>
                </div>
                <div class="ta-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table ta-table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Level</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Hired</th>
                                    <th class="text-end">Open</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($levelBreakdown)): ?>
                                <?php foreach ($levelBreakdown as $row): ?>
                                    <tr>
                                        <td><?= esc($row['level_name'] ?: '-') ?></td>
                                        <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
                                        <td class="text-end"><?= number_format((int) ($row['hired_count'] ?? 0)) ?></td>
                                        <td class="text-end"><?= number_format((int) ($row['open_count'] ?? 0)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No data found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Status per Business Unit</h5>
                        <div class="ta-panel-sub">Using job unit group</div>
                    </div>
                </div>
                <div class="ta-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table ta-table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Business Unit</th>
                                    <th class="text-end">Total</th>
                                    <th class="text-end">Hired</th>
                                    <th class="text-end">Open</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($businessUnitBreakdown)): ?>
                                <?php foreach ($businessUnitBreakdown as $row): ?>
                                    <tr>
                                        <td><?= esc($row['business_unit_name'] ?: '-') ?></td>
                                        <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
                                        <td class="text-end"><?= number_format((int) ($row['hired_count'] ?? 0)) ?></td>
                                        <td class="text-end"><?= number_format((int) ($row['open_count'] ?? 0)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">No data found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Decline Reasons</h5>
                        <div class="ta-panel-sub">Using declined status and workflow remarks</div>
                    </div>
                </div>
                <div class="ta-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table ta-table table-hover align-middle">
                            <thead>
                                <tr>
                                    <th>Reason</th>
                                    <th class="text-end">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php if (!empty($declineReasons)): ?>
                                <?php foreach ($declineReasons as $row): ?>
                                    <tr>
                                        <td><?= esc($row['decline_reason'] ?: '-') ?></td>
                                        <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="2" class="text-center text-muted py-4">No decline reasons found.</td>
                                </tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="ta-panel h-100">
                <div class="ta-panel-header">
                    <div>
                        <h5 class="ta-panel-title">Top Jobs by Applications</h5>
                        <div class="ta-panel-sub">Demand concentration by requisition</div>
                    </div>
                </div>
                <div class="ta-panel-body p-0">
                    <div class="table-responsive">
                        <table class="table ta-table table-hover align-middle">
                            <thead>
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
                                        <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
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

    <div class="ta-panel mb-4">
        <div class="ta-panel-header">
            <div>
                <h5 class="ta-panel-title">TA Team Performance</h5>
                <div class="ta-panel-sub">Using posted date for TAT and open days</div>
            </div>
        </div>
        <div class="ta-panel-body p-0">
            <div class="table-responsive">
                <table class="table ta-table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Recruiter / Processor</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Hires</th>
                            <!-- <th class="text-end">Avg Time to Fill</th> -->
                            <th class="text-end">Average Time-to-Hire</th>
                            <th class="text-end">Avg Days Open</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($taTeamPerformance)): ?>
                        <?php foreach ($taTeamPerformance as $row): ?>
                            <tr>
                                <td><?= esc($row['recruiter_name'] ?: 'Unassigned') ?></td>
                                <td class="text-end fw-semibold"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
                                <td class="text-end"><?= number_format((int) ($row['hires_count'] ?? 0)) ?></td>
                                <td class="text-end"><?= number_format((int) ($row['avg_time_to_fill'] ?? 0)) ?> days</td>
                                <td class="text-end"><?= number_format((int) ($row['avg_days_open'] ?? 0)) ?> days</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No data found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ta-panel mb-4">
        <div class="ta-panel-header">
            <div>
                <h5 class="ta-panel-title">Overdue Applications</h5>
                <div class="ta-panel-sub">Priority list of applications past due date</div>
            </div>
            <a href="<?= site_url('admin/applications?overdue=1') ?>" class="btn btn-sm btn-outline-danger">View All</a>
        </div>
        <div class="ta-panel-body p-0">
            <div class="table-responsive">
                <table class="table ta-table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Company</th>
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
                                    <a href="<?= site_url('admin/applications/' . (int) $row['id']) ?>" class="fw-semibold text-decoration-none">
                                        #<?= (int) $row['id'] ?>
                                    </a>
                                </td>
                                <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
                                <td><?= esc($row['company_name'] ?: '-') ?></td>
                                <td><?= esc($row['job_name'] ?: '-') ?></td>
                                <td><span class="ta-badge-soft gray"><?= esc($row['status_name'] ?: '-') ?></span></td>
                                <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
                                <td><?= esc($row['applied_at'] ?: '-') ?></td>
                                <td>
                                    <span class="ta-badge-soft red">Overdue</span>
                                    <div class="small text-danger mt-1"><?= esc($row['due_at'] ?: '-') ?></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No overdue applications found.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="ta-panel">
        <div class="ta-panel-header">
            <div>
                <h5 class="ta-panel-title">Recent Applications</h5>
                <div class="ta-panel-sub">Latest application activity in the selected period</div>
            </div>
        </div>
        <div class="ta-panel-body p-0">
            <div class="table-responsive">
                <table class="table ta-table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Applicant</th>
                            <th>Email</th>
                            <th>Company</th>
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
                                    <a href="<?= site_url('admin/applications/' . (int) $row['id']) ?>" class="fw-semibold text-decoration-none">
                                        #<?= (int) $row['id'] ?>
                                    </a>
                                </td>
                                <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
                                <td><?= esc($row['email'] ?: '-') ?></td>
                                <td><?= esc($row['company_name'] ?: '-') ?></td>
                                <td><?= esc($row['job_name'] ?: '-') ?></td>
                                <td><span class="ta-badge-soft gray"><?= esc($row['status_name'] ?: '-') ?></span></td>
                                <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
                                <td><?= esc($row['applied_at'] ?: '-') ?></td>
                                <td>
                                    <?php if (empty($row['due_at'])): ?>
                                        <span class="text-muted">-</span>
                                    <?php elseif ($isOverdue): ?>
                                        <!-- <span class="ta-badge-soft red">Overdue</span> -->
                                        <div class="small text-danger mt-1"><?= esc($row['due_at']) ?></div>
                                    <?php else: ?>
                                        <!-- <span class="ta-badge-soft green">On Time</span> -->
                                        <div class="small ta-muted mt-1"><?= esc($row['due_at']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <!-- <td><span class="ta-badge-soft blue"><?= esc($row['source'] ?: '-') ?></span></td> -->
                                <td><span class=""><?= esc($row['source'] ?: '-') ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">No applications found for selected period.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<?= $this->endSection() ?>