<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<style>
  .applicant-view-wrap {
    width: 100%;
    max-width: 100%;
  }

  .applicant-hero {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1.25rem;
  }

  .applicant-name {
    font-size: 2rem;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -0.02em;
    margin-bottom: 0.25rem;
    color: #1f2937;
  }

  .applicant-email {
    font-size: 1rem;
    color: #6b7280;
  }

  .applicant-card {
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    background: #fff;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
    overflow: hidden;
    height: 100%;
  }

  .applicant-card .card-body {
    padding: 1.4rem 1.5rem;
  }

  .section-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #111827;
    margin-bottom: 1rem;
  }

  .section-divider {
    margin: 1.25rem 0 1rem;
    border-top: 1px solid #eef2f7;
    padding-top: 1.25rem;
  }

  .profile-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem 1.25rem;
  }

  .profile-item {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 14px;
    padding: 0.9rem 1rem;
  }

  .profile-label {
    font-size: 0.82rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #98a2b3;
    margin-bottom: 0.25rem;
  }

  .profile-value {
    font-size: 1rem;
    color: #1f2937;
    word-break: break-word;
  }

  .resume-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    padding: 0.65rem 0.95rem;
    border-radius: 999px;
    border: 1px solid #dbe3ec;
    background: #fff;
    color: #344054;
    text-decoration: none;
    font-weight: 600;
  }

  .resume-chip:hover {
    background: #f8fafc;
    color: #0d6efd;
  }

  .application-list {
    display: grid;
    gap: 0.75rem;
  }

  .application-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    border: 1px solid #edf2f7;
    background: #f8fafc;
    border-radius: 14px;
    padding: 0.9rem 1rem;
  }

  .application-job {
    font-size: 1rem;
    font-weight: 600;
    color: #1f2937;
  }

  .application-meta {
    font-size: 0.9rem;
    color: #6b7280;
    margin-top: 0.2rem;
  }

  .status-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.4rem 0.75rem;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
    border: 1px solid transparent;
    white-space: nowrap;
  }

  .status-default {
    background: #f3f4f6;
    color: #374151;
    border-color: #e5e7eb;
  }

  .status-approved,
  .status-hired {
    background: #ecfdf3;
    color: #027a48;
    border-color: #abefc6;
  }

  .status-submitted,
  .status-pending,
  .status-under-review {
    background: #fffaeb;
    color: #b54708;
    border-color: #fedf89;
  }

  .status-rejected {
    background: #fef3f2;
    color: #b42318;
    border-color: #fecdca;
  }

  .table-modern {
    margin-bottom: 0;
    font-size: 0.96rem;
  }

  .table-modern thead th {
    background: #f8fafc;
    color: #475467;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    font-weight: 700;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.95rem 1rem;
    white-space: nowrap;
  }

  .table-modern tbody td {
    padding: 0.95rem 1rem;
    vertical-align: middle;
    border-color: #eef2f7;
    color: #344054;
  }

  .table-modern tbody tr:hover {
    background: #fafcff;
  }

  .empty-state {
    padding: 1.25rem;
    border: 1px dashed #d0d5dd;
    border-radius: 14px;
    background: #f8fafc;
    color: #98a2b3;
    text-align: center;
    font-size: 0.98rem;
  }

  .toggle-card-header {
    padding: 0;
    background: #fff;
    border-bottom: 1px solid #eef2f7;
  }

  .toggle-btn {
    width: 100%;
    text-align: left;
    border: 0;
    background: transparent;
    padding: 1.1rem 1.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 1.05rem;
    font-weight: 700;
    color: #111827;
  }

  .toggle-btn:hover {
    background: #fafcff;
  }

  .toggle-btn:focus {
    box-shadow: none;
  }

  .toggle-btn .toggle-icon {
    transition: transform 0.2s ease;
    color: #6b7280;
  }

  .toggle-btn[aria-expanded="true"] .toggle-icon {
    transform: rotate(180deg);
  }

  @media (max-width: 991px) {
    .applicant-name {
      font-size: 1.65rem;
    }

    .profile-grid {
      grid-template-columns: 1fr;
    }

    .application-item {
      flex-direction: column;
      align-items: flex-start;
    }
  }
</style>

<?php
$fullName = trim(($applicant['firstname'] ?? '') . ' ' . ($applicant['lastname'] ?? ''));

$statusClass = function ($status) {
    $status = strtolower(trim((string) $status));
    return match ($status) {
        'approved', 'hired' => 'status-approved',
        'submitted', 'pending', 'under review', 'under-review' => 'status-submitted',
        'rejected' => 'status-rejected',
        default => 'status-default',
    };
};
?>

<div class="applicant-view-wrap">
  <div class="applicant-hero">
    <div>
      <h1 class="applicant-name"><?= esc($fullName !== '' ? $fullName : 'Unnamed Applicant') ?></h1>
      <div class="applicant-email"><?= esc($applicant['email'] ?? '-') ?></div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-outline-secondary" href="<?= site_url('admin/applicants') ?>">
        <i class="bi bi-arrow-left me-1"></i> Back
      </a>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-12 col-xl-7">
      <div class="applicant-card">
        <div class="toggle-card-header">
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#profileSection" aria-expanded="true" aria-controls="profileSection">
            <span>Profile</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>
        <div id="profileSection" class="collapse show">
          <div class="card-body">
            <div class="profile-grid">
              <div class="profile-item">
                <div class="profile-label">Phone</div>
                <div class="profile-value"><?= esc($applicant['phone'] ?? '-') ?></div>
              </div>

              <div class="profile-item">
                <div class="profile-label">Email</div>
                <div class="profile-value"><?= esc($applicant['email'] ?? '-') ?></div>
              </div>

              <div class="profile-item">
                <div class="profile-label">Address</div>
                <div class="profile-value"><?= esc($applicant['address'] ?? '-') ?></div>
              </div>

              <div class="profile-item">
                <div class="profile-label">City</div>
                <div class="profile-value"><?= esc($applicant['city'] ?? '-') ?></div>
              </div>

              <div class="profile-item">
                <div class="profile-label">Province</div>
                <div class="profile-value"><?= esc($applicant['province'] ?? '-') ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-5">
      <div class="applicant-card">
        <div class="toggle-card-header">
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#applicationsSection" aria-expanded="true" aria-controls="applicationsSection">
            <span>Applications</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>
        <div id="applicationsSection" class="collapse show">
          <div class="card-body">
            <?php if (empty($applications)): ?>
              <div class="empty-state">No applications yet.</div>
            <?php else: ?>
              <div class="application-list">
                <?php foreach ($applications as $application): ?>
                  <?php
                    $statusName = $application['status_name'] ?? '-';
                    $jobName = $application['job_name'] ?? '-';
                    $appliedAt = $application['applied_at'] ?? null;
                  ?>
                  <div class="application-item">
                    <div>
                      <div class="application-job"><?= esc($jobName) ?></div>
                      <div class="application-meta">
                        Applied: <?= esc($appliedAt ?: '-') ?>
                      </div>
                    </div>

                    <span class="status-badge <?= esc($statusClass($statusName)) ?>">
                      <?= esc($statusName) ?>
                    </span>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="applicant-card">
        <div class="toggle-card-header">
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#personalInfoSection" aria-expanded="false" aria-controls="personalInfoSection">
            <span>Personal Information</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>
        <div id="personalInfoSection" class="collapse">
          <div class="card-body">
            <div class="profile-grid">
              <div class="profile-item">
                <div class="profile-label">First Name</div>
                <div class="profile-value"><?= esc($applicant['firstname'] ?? '-') ?></div>
              </div>

              <div class="profile-item">
                <div class="profile-label">Last Name</div>
                <div class="profile-value"><?= esc($applicant['lastname'] ?? '-') ?></div>
              </div>

              <div class="profile-item">
                <div class="profile-label">Full Name</div>
                <div class="profile-value"><?= esc($fullName !== '' ? $fullName : '-') ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="applicant-card">
        <div class="toggle-card-header">
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#documentsSection" aria-expanded="false" aria-controls="documentsSection">
            <span>Document Attachments</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>
        <div id="documentsSection" class="collapse">
          <div class="card-body">
            <?php if (empty($applicant['resume'])): ?>
              <div class="empty-state">No document records.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-modern">
                  <thead>
                    <tr>
                      <th>Document Name</th>
                      <th>Attachment</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><span class="fw-semibold">Resume</span></td>
                      <td>
                        <span class="resume-chip">
                          <i class="bi bi-file-earmark-text"></i>
                          <?= esc($applicant['resume']) ?>
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="applicant-card">
        <div class="toggle-card-header">
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#educationSection" aria-expanded="false" aria-controls="educationSection">
            <span>Education</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>
        <div id="educationSection" class="collapse">
          <div class="card-body">
            <?php if (empty($educations)): ?>
              <div class="empty-state">No education records.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-modern">
                  <thead>
                    <tr>
                      <th>School</th>
                      <th>Degree</th>
                      <th>Field</th>
                      <th>Years</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($educations as $edu): ?>
                      <tr>
                        <td><?= esc($edu['school_name'] ?? '-') ?></td>
                        <td><?= esc($edu['degree'] ?? '-') ?></td>
                        <td><?= esc($edu['field_of_study'] ?? '-') ?></td>
                        <td><?= esc(trim(($edu['start_year'] ?? '') . ' - ' . ($edu['end_year'] ?? ''))) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="applicant-card">
        <div class="toggle-card-header">
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#jobHistorySection" aria-expanded="false" aria-controls="jobHistorySection">
            <span>Job History</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>
        <div id="jobHistorySection" class="collapse">
          <div class="card-body">
            <?php if (empty($jobHistory)): ?>
              <div class="empty-state">No job history records.</div>
            <?php else: ?>
              <div class="table-responsive">
                <table class="table table-modern">
                  <thead>
                    <tr>
                      <th>Company</th>
                      <th>Job Title</th>
                      <th>Responsibilities</th>
                      <th>Dates</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($jobHistory as $job): ?>
                      <tr>
                        <td><?= esc($job['company_name'] ?? '-') ?></td>
                        <td><?= esc($job['job_title'] ?? '-') ?></td>
                        <td><?= esc($job['responsibilities'] ?? '-') ?></td>
                        <td><?= esc(($job['start_date'] ?? '') . ' - ' . ($job['end_date'] ?? 'Present')) ?></td>
                      </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?= $this->endSection() ?>