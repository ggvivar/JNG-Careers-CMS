<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
helper('rbac');


$featureMap = [
    'users' => 'users',
    'roles' => 'roles',
    'jobs' => 'jobs',
    'job_posts' => 'job-posts',
    'applicants' => 'applicants',
    'applications' => 'applications',
    'my_processing' => 'applications',
    'contents' => 'contents',
];

$featureUrls = [
    'users' => 'admin/users',
    'roles' => 'admin/roles',
    'jobs' => 'admin/jobs',
    'job_posts' => 'admin/job-posts',
    'applicants' => 'admin/applicants',
    'applications' => 'admin/applications',
    'my_processing' => 'admin/applications/assigned-to-me',
    'contents' => 'admin/contents',
];

$icons = [
    'users' => 'bi-people',
    'roles' => 'bi-shield-lock',
    'jobs' => 'bi-briefcase',
    'job_posts' => 'bi-file-earmark-text',
    'applicants' => 'bi-person-vcard',
    'applications' => 'bi-send',
    'my_processing' => 'bi-person-workspace',
    'contents' => 'bi-file-richtext'
];

$quickLinks = [
    [
        'label'   => 'Users',
        'url'     => 'admin/users',
        'icon'    => 'bi-people',
        'feature' => 'users',
    ],
    [
        'label'   => 'Roles',
        'url'     => 'admin/roles',
        'icon'    => 'bi-shield-lock',
        'feature' => 'roles',
    ],
    [
        'label'   => 'Jobs',
        'url'     => 'admin/jobs',
        'icon'    => 'bi-briefcase',
        'feature' => 'jobs',
    ],
    [
        'label'   => 'Job Posts',
        'url'     => 'admin/job-posts',
        'icon'    => 'bi-file-earmark-text',
        'feature' => 'job-posts',
    ],
    [
        'label'   => 'Applications',
        'url'     => 'admin/applications',
        'icon'    => 'bi-send',
        'feature' => 'applications',
    ],
    [
        'label'   => 'My Processing',
        'url'     => 'admin/applications/assigned-to-me',
        'icon'    => 'bi-person-workspace',
        'feature' => 'applications',
    ],
    [
        'label'   => 'Contents',
        'url'     => 'admin/contents',
        'icon'    => 'bi-file-richtext',
        'feature' => 'contents',
    ],
];
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="mb-0">Dashboard</h3>
    <div class="text-muted small">Overview of your Career CMS</div>
  </div>
</div>

<div class="row g-3 mb-4">
<?php foreach (($stats ?? []) as $label => $value): ?>
  <?php
    $featureCode = $featureMap[$label] ?? null;
    if ($featureCode && !rbac_can_feature($featureCode, 'can_view')) {
        continue;
    }

    $cardUrl = $featureUrls[$label] ?? null;
  ?>
  <div class="col-12 col-sm-6 col-xl-3">
    <?php if ($cardUrl): ?>
      <a href="<?= site_url($cardUrl) ?>" class="text-decoration-none text-reset d-block h-100">
    <?php endif; ?>

    <div class="dashboard-card h-100 dashboard-card-clickable">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="text-muted small text-uppercase">
            <?= esc(str_replace('_', ' ', $label)) ?>
          </div>
          <div class="fs-3 fw-semibold">
            <?= esc((string) $value) ?>
          </div>
        </div>

        <div class="dashboard-icon">
          <i class="bi <?= esc($icons[$label] ?? 'bi-bar-chart') ?>"></i>
        </div>
      </div>
    </div>

    <?php if ($cardUrl): ?>
      </a>
    <?php endif; ?>
  </div>
<?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
  <div class="col-12">
    <h5 class="mb-0">For Review</h5>
  </div>

  <?php foreach (($stats_for_approval ?? []) as $label => $value): ?>
    <?php
      $featureCode = $featureMap[$label] ?? null;
      if ($featureCode && !rbac_can_feature($featureCode, 'can_approve') && !rbac_can_feature($featureCode, 'can_view')) {
          continue;
      }

      $cardUrl = $featureUrls[$label] ?? null;
    ?>
    <div class="col-12 col-sm-6 col-xl-3">
      <?php if ($cardUrl): ?>
        <a href="<?= site_url($cardUrl) ?>" class="text-decoration-none text-reset d-block h-100">
      <?php endif; ?>

      <div class="dashboard-card h-100 dashboard-card-clickable">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="text-muted small text-uppercase">
              <?= esc(str_replace('_', ' ', $label)) ?>
            </div>
            <div class="fs-3 fw-semibold">
              <?= esc((string) $value) ?>
            </div>
          </div>

          <div class="dashboard-icon">
            <i class="bi <?= esc($icons[$label] ?? 'bi-bar-chart') ?>"></i>
          </div>
        </div>
      </div>

      <?php if ($cardUrl): ?>
        </a>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<?php if (!empty($overdueStats) && (int)($overdueStats['applications_overdue'] ?? 0) > 0): ?>
<div class="row g-3 mb-4">
  <div class="col-12">
    <h5 class="mb-0 text-danger">Overdue Alerts</h5>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="dashboard-card border border-danger">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="text-muted small text-uppercase">Applications Overdue</div>
          <div class="fs-3 fw-semibold text-danger">
            <?= esc((string) ($overdueStats['applications_overdue'] ?? 0)) ?>
          </div>
        </div>

        <div class="dashboard-icon text-danger">
          <i class="bi bi-exclamation-triangle"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-xl-3">
    <div class="dashboard-card border border-warning">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <div class="text-muted small text-uppercase">My Overdue</div>
          <div class="fs-3 fw-semibold text-warning">
            <?= esc((string) ($myProcessingOverdue ?? 0)) ?>
          </div>
        </div>

        <div class="dashboard-icon text-warning">
          <i class="bi bi-person-workspace"></i>
        </div>
      </div>
    </div>
  </div>
</div>
<?php endif; ?>

<?php if (!empty($overdueApplications)): ?>
<div class="card border-danger mb-4">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0 text-danger">Overdue Applications</h5>
      <a href="<?= site_url('admin/applications?overdue=1') ?>" class="btn btn-sm btn-outline-danger">View All</a>
    </div>

    <div class="table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>ID</th>
            <th>Applicant</th>
            <th>Job</th>
            <th>Status</th>
            <th>Processor</th>
            <th>Due At</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($overdueApplications as $row): ?>
            <tr>
              <td><?= esc($row['id']) ?></td>
              <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? ''))) ?></td>
              <td><?= esc($row['job_name'] ?? '-') ?></td>
              <td>
                <span class="badge bg-danger-subtle text-danger border">
                  <?= esc($row['status_name'] ?? '-') ?>
                </span>
              </td>
              <td><?= esc($row['processor_name'] ?? 'Unassigned') ?></td>
              <td><?= esc($row['status_due_at'] ?? '-') ?></td>
              <td class="text-end">
                <a href="<?= site_url('admin/applications/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary">
                  View
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php endif; ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius:16px;">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <div>
                <h5 class="mb-1">Talent Acquisition Report</h5>
                <div class="text-muted small">Quick view of recruitment operations</div>
            </div>

            <a href="<?= site_url('admin/reports/talent-acquisition') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-bar-chart-line me-1"></i>
                Open Report
            </a>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="border rounded-4 p-3 h-100 bg-white">
                    <div class="text-muted small text-uppercase mb-1">Applications</div>
                    <div class="fs-4 fw-bold"><?= esc((string) ($stats['applications'] ?? 0)) ?></div>
                    <div class="small text-muted">Total active applications</div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="border rounded-4 p-3 h-100 bg-white">
                    <div class="text-muted small text-uppercase mb-1">My Processing</div>
                    <div class="fs-4 fw-bold text-primary"><?= esc((string) ($stats['my_processing'] ?? 0)) ?></div>
                    <div class="small text-muted">Assigned to me</div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="border rounded-4 p-3 h-100 bg-white">
                    <div class="text-muted small text-uppercase mb-1">For Review</div>
                    <div class="fs-4 fw-bold text-warning"><?= esc((string) ($stats_for_approval['applications'] ?? 0)) ?></div>
                    <div class="small text-muted">Pending approval / review</div>
                </div>
            </div>

            <div class="col-12 col-sm-6 col-xl-3">
                <div class="border rounded-4 p-3 h-100 bg-white">
                    <div class="text-muted small text-uppercase mb-1">Applications Overdue</div>
                    <div class="fs-4 fw-bold text-danger"><?= esc((string) ($overdueStats['applications_overdue'] ?? 0)) ?></div>
                    <div class="small text-muted">Past due date</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <div class="col-12 col-xl-6">
                <div class="border rounded-4 h-100">
                    <div class="p-3 border-bottom bg-light rounded-top-4">
                        <div class="fw-semibold">Recruitment Highlights</div>
                    </div>
                    <div class="p-3">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">Total Applications</span>
                            <span class="fw-semibold"><?= esc((string) ($stats['applications'] ?? 0)) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">My Processing</span>
                            <span class="fw-semibold"><?= esc((string) ($stats['my_processing'] ?? 0)) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">For Review</span>
                            <span class="fw-semibold"><?= esc((string) ($stats_for_approval['applications'] ?? 0)) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted">Applications Overdue</span>
                            <span class="fw-semibold text-danger"><?= esc((string) ($overdueStats['applications_overdue'] ?? 0)) ?></span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted">My Overdue</span>
                            <span class="fw-semibold text-warning"><?= esc((string) ($myProcessingOverdue ?? 0)) ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6">
                <div class="border rounded-4 h-100">
                    <div class="p-3 border-bottom bg-light rounded-top-4 d-flex justify-content-between align-items-center">
                        <div class="fw-semibold">Top Overdue Applications</div>
                        <a href="<?= site_url('admin/applications?overdue=1') ?>" class="btn btn-sm btn-outline-danger">
                            View All
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Applicant</th>
                                    <th>Job</th>
                                    <th>Due At</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($overdueApplications)): ?>
                                    <?php foreach (array_slice($overdueApplications, 0, 5) as $row): ?>
                                        <tr>
                                            <td>
                                                <a href="<?= site_url('admin/applications/' . (int) $row['id']) ?>" class="text-decoration-none fw-semibold">
                                                    #<?= (int) $row['id'] ?>
                                                </a>
                                            </td>
                                            <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
                                            <td><?= esc($row['job_name'] ?? '-') ?></td>
                                            <td>
                                                <span class="badge text-bg-danger">Overdue</span>
                                                <div class="small text-danger mt-1"><?= esc($row['due_at'] ?? '-') ?></div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No overdue applications found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$visibleQuickLinks = array_filter($quickLinks, function ($item) {
    return empty($item['feature']) || rbac_can_feature($item['feature'], 'can_view');
});
?>

<?php if (!empty($visibleQuickLinks)): ?>
<div class="card">
  <div class="card-body">
    <h5 class="mb-3">Quick Links</h5>

    <div class="row g-2">
      <?php foreach ($visibleQuickLinks as $item): ?>
        <div class="col-6 col-md-4 col-lg-2">
          <a href="<?= site_url($item['url']) ?>" class="btn btn-outline-primary w-100">
            <i class="bi <?= esc($item['icon']) ?>"></i><br>
            <span class="small"><?= esc($item['label']) ?></span>
          </a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>