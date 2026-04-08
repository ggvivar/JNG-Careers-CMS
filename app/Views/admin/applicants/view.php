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

  .inline-edit-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
  }

  .icon-btn {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    border: 1px solid #dbe3ec;
    background: #fff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    color: #475467;
    cursor: pointer;
    transition: all .2s ease;
  }

  .icon-btn:hover {
    background: #f8fafc;
    color: #0d6efd;
    border-color: #bfd4ff;
  }

  .drag-handle {
    cursor: grab;
    color: #98a2b3;
    margin-right: .5rem;
    display: inline-flex;
    align-items: center;
  }

  .sortable-ghost {
    opacity: .45;
    background: #eef6ff !important;
  }

  .sortable-chosen {
    box-shadow: 0 8px 18px rgba(13,110,253,.12);
  }

  .modal-field-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
  }

  .modal-field-grid .full {
    grid-column: 1 / -1;
  }

  .inline-form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem 1.25rem;
    margin-bottom: 1rem;
  }

  .section-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
  }

  @media (max-width: 991px) {
    .applicant-name {
      font-size: 1.65rem;
    }

    .profile-grid,
    .inline-form-grid,
    .modal-field-grid {
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
<div id="pageAlert" class="d-none mb-3"></div>
<div class="applicant-view-wrap">
  <div class="applicant-hero">
    <div>
      <h1 class="applicant-name"><?= esc($fullName !== '' ? $fullName : 'Unnamed Applicant') ?></h1>
      <div class="applicant-email"><?= esc($applicant['email'] ?? '-') ?></div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <?php if (function_exists('rbac_can_feature') && rbac_can_feature('applicants', 'can_edit')): ?>
        <a class="btn btn-primary" href="<?= site_url('admin/applicants/edit/' . $applicant['id']) ?>">
          <i class="bi bi-pencil-square me-1"></i> Edit
        </a>
      <?php endif; ?>

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
            <div class="section-toolbar">
              <div class="text-muted small">Update profile fields without leaving this page.</div>
              <button type="button" class="btn btn-primary btn-sm" onclick="toggleProfileEdit(true)">
                <i class="bi bi-pencil-square me-1"></i> Inline Edit
              </button>
            </div>

            <form id="profileInlineForm" class="d-none">
              <div class="inline-form-grid">
                <div>
                  <label class="form-label">Phone</label>
                  <input type="text" name="phone" class="form-control" value="<?= esc($applicant['phone'] ?? '') ?>">
                </div>
                <div>
                  <label class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" value="<?= esc($applicant['email'] ?? '') ?>">
                </div>
                <div>
                  <label class="form-label">Address</label>
                  <input type="text" name="address" class="form-control" value="<?= esc($applicant['address'] ?? '') ?>">
                </div>
                <div>
                  <label class="form-label">City</label>
                  <input type="text" name="city" class="form-control" value="<?= esc($applicant['city'] ?? '') ?>">
                </div>
                <div>
                  <label class="form-label">Province</label>
                  <input type="text" name="province" class="form-control" value="<?= esc($applicant['province'] ?? '') ?>">
                </div>
              </div>

              <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary btn-sm" onclick="toggleProfileEdit(false)">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm" onclick="saveProfileInline()">Save Changes</button>
              </div>
            </form>

            <div id="profileDisplay">
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
                      <div class="application-meta">Applied: <?= esc($appliedAt ?: '-') ?></div>
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
        <?php if (empty($documents)): ?>
          <div class="empty-state">No document records.</div>
        <?php else: ?>
          <div class="application-list">
            <?php foreach ($documents as $doc): ?>
              <div class="application-item">
                <div class="flex-grow-1">
                  <div class="application-job"><?= esc($doc['document_type'] ?? 'Document') ?></div>
                  <div class="application-meta"><?= esc($doc['file_name'] ?? '-') ?></div>

                  <?php if (!empty($doc['remarks'])): ?>
                    <div class="application-meta"><?= esc($doc['remarks']) ?></div>
                  <?php endif; ?>

                  <?php if (!empty($doc['file_ext']) || !empty($doc['file_size'])): ?>
                    <div class="application-meta">
                      <?= !empty($doc['file_ext']) ? strtoupper(esc($doc['file_ext'])) : '' ?>
                      <?= !empty($doc['file_ext']) && !empty($doc['file_size']) ? ' • ' : '' ?>
                      <?= !empty($doc['file_size']) ? number_format(((float)$doc['file_size']) / 1024, 2) . ' KB' : '' ?>
                    </div>
                  <?php endif; ?>
                </div>

                <div class="inline-edit-actions">
                  <a class="resume-chip" href="<?= base_url($doc['file_path']) ?>" target="_blank">
                    <i class="bi bi-file-earmark-text"></i>
                    View File
                  </a>
                </div>
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
          <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#educationSection" aria-expanded="false" aria-controls="educationSection">
            <span>Education</span>
            <i class="bi bi-chevron-down toggle-icon"></i>
          </button>
        </div>

        <div id="educationSection" class="collapse">
          <div class="card-body">
            <div class="section-toolbar">
              <div class="text-muted small">Add or edit education records inline with a modal.</div>
              <button type="button" class="btn btn-primary btn-sm" onclick="openEducationModal()">
                <i class="bi bi-plus-lg me-1"></i> Add Education
              </button>
            </div>

            <?php if (empty($educations)): ?>
              <div class="empty-state" id="educationEmpty">No education records.</div>
            <?php endif; ?>

            <div id="educationList" class="application-list">
              <?php foreach ($educations as $edu): ?>
                <div class="application-item education-item"
                     data-id="<?= esc($edu['id'] ?? '') ?>"
                     data-school_name="<?= esc($edu['school_name'] ?? '') ?>"
                     data-degree="<?= esc($edu['degree'] ?? '') ?>"
                     data-field_of_study="<?= esc($edu['field_of_study'] ?? '') ?>"
                     data-start_year="<?= esc($edu['start_year'] ?? '') ?>"
                     data-end_year="<?= esc($edu['end_year'] ?? '') ?>"
                     data-honors="<?= esc($edu['honors'] ?? '') ?>">
                  <div class="flex-grow-1">
                    <div class="application-job"><?= esc($edu['school_name'] ?? '-') ?></div>
                    <div class="application-meta">
                      <?= esc($edu['degree'] ?? '-') ?>
                      <?php if (!empty($edu['field_of_study'])): ?>
                        • <?= esc($edu['field_of_study']) ?>
                      <?php endif; ?>
                    </div>
                    <div class="application-meta">
                      <?= esc(trim(($edu['start_year'] ?? '') . ' - ' . ($edu['end_year'] ?? ''))) ?>
                    </div>
                  </div>

                  <div class="inline-edit-actions">
                    <button type="button" class="icon-btn" onclick="openEducationModal(this.closest('.education-item'))">
                      <i class="bi bi-pencil"></i>
                    </button>
                    <button type="button" class="icon-btn text-danger" onclick="deleteEducationRow(this.closest('.education-item'))">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
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
            <div class="section-toolbar">
              <div class="text-muted small">Drag to reorder. Edit with modal without leaving this page.</div>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm" onclick="saveJobOrder()">
                  <i class="bi bi-save me-1"></i> Save Order
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="openJobModal()">
                  <i class="bi bi-plus-lg me-1"></i> Add Job History
                </button>
              </div>
            </div>

            <?php if (empty($jobHistory)): ?>
              <div class="empty-state" id="jobHistoryEmpty">No job history records.</div>
            <?php endif; ?>

            <div id="jobHistoryList" class="application-list">
              <?php foreach ($jobHistory as $job): ?>
                <div class="application-item job-history-item"
                     data-id="<?= esc($job['id'] ?? '') ?>"
                     data-company_name="<?= esc($job['company_name'] ?? '') ?>"
                     data-company_address="<?= esc($job['company_address'] ?? '') ?>"
                     data-job_title="<?= esc($job['job_title'] ?? '') ?>"
                     data-department="<?= esc($job['department'] ?? '') ?>"
                     data-start_date="<?= esc($job['start_date'] ?? '') ?>"
                     data-end_date="<?= esc($job['end_date'] ?? '') ?>"
                     data-currently_working="<?= esc($job['currently_working'] ?? 0) ?>"
                     data-responsibilities="<?= esc($job['responsibilities'] ?? '') ?>"
                     data-salary="<?= esc($job['salary'] ?? '') ?>"
                     data-reason_for_leaving="<?= esc($job['reason_for_leaving'] ?? '') ?>">
                  <div class="d-flex align-items-start gap-2 w-100">
                    <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>

                    <div class="flex-grow-1">
                      <div class="application-job"><?= esc($job['company_name'] ?? '-') ?></div>
                      <div class="application-meta">
                        <?= esc($job['job_title'] ?? '-') ?>
                        <?php if (!empty($job['department'])): ?>
                          • <?= esc($job['department']) ?>
                        <?php endif; ?>
                      </div>
                      <div class="application-meta">
                        <?= esc(($job['start_date'] ?? '') . ' - ' . ($job['end_date'] ?? 'Present')) ?>
                      </div>
                      <?php if (!empty($job['responsibilities'])): ?>
                        <div class="application-meta mt-1"><?= esc($job['responsibilities']) ?></div>
                      <?php endif; ?>
                    </div>

                    <div class="inline-edit-actions">
                      <button type="button" class="icon-btn" onclick="openJobModal(this.closest('.job-history-item'))">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <button type="button" class="icon-btn text-danger" onclick="deleteJobRow(this.closest('.job-history-item'))">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php if (!empty($employment)): ?>
      <div class="col-12">
        <div class="applicant-card">
          <div class="toggle-card-header">
            <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#employmentSection" aria-expanded="false" aria-controls="employmentSection">
              <span>Employment Details</span>
              <i class="bi bi-chevron-down toggle-icon"></i>
            </button>
          </div>

          <div id="employmentSection" class="collapse">
            <div class="card-body">
              <div class="profile-grid">
                <div class="profile-item">
                  <div class="profile-label">Employee No</div>
                  <div class="profile-value"><?= esc($employment['employee_no'] ?? '-') ?></div>
                </div>
                <div class="profile-item">
                  <div class="profile-label">Position</div>
                  <div class="profile-value"><?= esc($employment['position'] ?? '-') ?></div>
                </div>
                <div class="profile-item">
                  <div class="profile-label">Department</div>
                  <div class="profile-value"><?= esc($employment['department'] ?? '-') ?></div>
                </div>
                <div class="profile-item">
                  <div class="profile-label">Employment Type</div>
                  <div class="profile-value"><?= esc($employment['employment_type'] ?? '-') ?></div>
                </div>
                <div class="profile-item">
                  <div class="profile-label">Date Hired</div>
                  <div class="profile-value"><?= esc($employment['date_hired'] ?? '-') ?></div>
                </div>
                <div class="profile-item">
                  <div class="profile-label">Salary</div>
                  <div class="profile-value"><?= esc($employment['salary'] ?? '-') ?></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Education Modal -->
<div class="modal fade" id="educationModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Education</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="edu_id">
        <div class="modal-field-grid">
          <div>
            <label class="form-label">School Name</label>
            <input type="text" id="edu_school_name" class="form-control">
          </div>
          <div>
            <label class="form-label">Degree</label>
            <input type="text" id="edu_degree" class="form-control">
          </div>
          <div>
            <label class="form-label">Field of Study</label>
            <input type="text" id="edu_field_of_study" class="form-control">
          </div>
          <div>
            <label class="form-label">Honors</label>
            <input type="text" id="edu_honors" class="form-control">
          </div>
          <div>
            <label class="form-label">Start Year</label>
            <input type="text" id="edu_start_year" class="form-control">
          </div>
          <div>
            <label class="form-label">End Year</label>
            <input type="text" id="edu_end_year" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveEducationModal()">Save</button>
      </div>
    </div>
  </div>
</div>

<!-- Job Modal -->
<div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Job History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="job_id">
        <div class="modal-field-grid">
          <div>
            <label class="form-label">Company Name</label>
            <input type="text" id="job_company_name" class="form-control">
          </div>
          <div>
            <label class="form-label">Company Address</label>
            <input type="text" id="job_company_address" class="form-control">
          </div>
          <div>
            <label class="form-label">Job Title</label>
            <input type="text" id="job_job_title" class="form-control">
          </div>
          <div>
            <label class="form-label">Department</label>
            <input type="text" id="job_department" class="form-control">
          </div>
          <div>
            <label class="form-label">Start Date</label>
            <input type="date" id="job_start_date" class="form-control">
          </div>
          <div>
            <label class="form-label">End Date</label>
            <input type="date" id="job_end_date" class="form-control">
          </div>
          <div>
            <label class="form-label">Salary</label>
            <input type="text" id="job_salary" class="form-control">
          </div>
          <div class="d-flex align-items-end">
            <div class="form-check">
              <input type="checkbox" id="job_currently_working" class="form-check-input">
              <label for="job_currently_working" class="form-check-label">Currently Working</label>
            </div>
          </div>
          <div class="full">
            <label class="form-label">Responsibilities</label>
            <textarea id="job_responsibilities" class="form-control" rows="3"></textarea>
          </div>
          <div class="full">
            <label class="form-label">Reason for Leaving</label>
            <input type="text" id="job_reason_for_leaving" class="form-control">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="saveJobModal()">Save</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>


<script>
(function () {

  let educationModal = null;
  let jobModal = null;
  const applicantId = <?= (int)$applicant['id'] ?>;

  // ================= ALERT =================
  function pageAlert(type, message) {
    const el = document.getElementById('pageAlert');
    el.className = `alert alert-${type}`;
    el.innerHTML = message;
    el.classList.remove('d-none');
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }

  function clearAlert() {
    const el = document.getElementById('pageAlert');
    el.className = 'd-none';
    el.innerHTML = '';
  }

  function escapeHtml(val) {
    return String(val ?? '').replace(/[&<>"']/g, m => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  // ================= INIT =================
  document.addEventListener('DOMContentLoaded', function () {

    if (typeof bootstrap === 'undefined') {
      pageAlert('danger', 'Bootstrap not loaded');
      return;
    }

    educationModal = new bootstrap.Modal(document.getElementById('educationModal'));
    jobModal = new bootstrap.Modal(document.getElementById('jobModal'));

    // Sortable
    const jobList = document.getElementById('jobHistoryList');
    if (jobList && typeof Sortable !== 'undefined') {
      Sortable.create(jobList, {
        animation: 150,
        handle: '.drag-handle'
      });
    }

  });

  // ================= PROFILE =================
  window.toggleProfileEdit = function (edit) {
    document.getElementById('profileInlineForm').classList.toggle('d-none', !edit);
    document.getElementById('profileDisplay').classList.toggle('d-none', edit);
  };

  window.saveProfileInline = async function () {
    clearAlert();

    const fd = new FormData(document.getElementById('profileInlineForm'));

    const res = await fetch(`<?= site_url('admin/applicants/update-inline-profile') ?>/${applicantId}`, {
      method: 'POST',
      body: fd
    });

    const data = await res.json();

    if (!data.success) {
      pageAlert('danger', 'Failed to save profile');
      return;
    }

    // update UI
    document.querySelectorAll('[data-profile]').forEach(el => {
      const key = el.dataset.profile;
      el.textContent = fd.get(key) || '-';
    });

    document.getElementById('heroName').textContent =
      `${fd.get('firstname')} ${fd.get('lastname')}`;

    toggleProfileEdit(false);
    pageAlert('success', 'Profile updated');
  };

  // ================= EDUCATION =================
  window.openEducationModal = function (item = null) {
    document.getElementById('edu_id').value = item?.dataset.id || '';
    document.getElementById('edu_school_name').value = item?.dataset.school_name || '';
    document.getElementById('edu_degree').value = item?.dataset.degree || '';
    document.getElementById('edu_field_of_study').value = item?.dataset.field_of_study || '';

    educationModal.show();
  };

  window.saveEducationModal = async function () {
    clearAlert();

    const fd = new FormData();
    fd.append('id', document.getElementById('edu_id').value);
    fd.append('school_name', document.getElementById('edu_school_name').value);
    fd.append('degree', document.getElementById('edu_degree').value);
    fd.append('field_of_study', document.getElementById('edu_field_of_study').value);

    const res = await fetch(`<?= site_url('admin/applicants/save-education') ?>/${applicantId}`, {
      method: 'POST',
      body: fd
    });

    const data = await res.json();

    if (!data.success) {
      pageAlert('danger', 'Save failed');
      return;
    }

    const html = `
      <div class="application-item education-item" data-id="${data.row.id}">
        <div>
          <strong>${escapeHtml(data.row.school_name)}</strong>
          <div class="application-meta">${escapeHtml(data.row.degree)}</div>
        </div>
      </div>`;

    document.getElementById('educationList').insertAdjacentHTML('afterbegin', html);

    educationModal.hide();
    pageAlert('success', 'Education saved');
  };

  // ================= JOB =================
  window.openJobModal = function (item = null) {
    document.getElementById('job_id').value = item?.dataset.id || '';
    document.getElementById('job_company_name').value = item?.dataset.company_name || '';
    document.getElementById('job_job_title').value = item?.dataset.job_title || '';

    jobModal.show();
  };

  window.saveJobModal = async function () {
    clearAlert();

    const fd = new FormData();
    fd.append('id', document.getElementById('job_id').value);
    fd.append('company_name', document.getElementById('job_company_name').value);
    fd.append('job_title', document.getElementById('job_job_title').value);

    const res = await fetch(`<?= site_url('admin/applicants/save-job-history') ?>/${applicantId}`, {
      method: 'POST',
      body: fd
    });

    const data = await res.json();

    if (!data.success) {
      pageAlert('danger', 'Save failed');
      return;
    }

    const html = `
      <div class="application-item job-history-item" data-id="${data.row.id}">
        <div>
          <strong>${escapeHtml(data.row.company_name)}</strong>
          <div class="application-meta">${escapeHtml(data.row.job_title)}</div>
        </div>
      </div>`;

    document.getElementById('jobHistoryList').insertAdjacentHTML('afterbegin', html);

    jobModal.hide();
    pageAlert('success', 'Job saved');
  };

  // ================= DOCUMENT =================
  window.uploadDocument = async function () {
    clearAlert();

    const fd = new FormData(document.getElementById('uploadDocumentForm'));

    const res = await fetch(`<?= site_url('admin/applicants/upload-document') ?>/${applicantId}`, {
      method: 'POST',
      body: fd
    });

    const data = await res.json();

    if (!data.success) {
      pageAlert('danger', 'Upload failed');
      return;
    }

    const html = `
      <div class="doc-row">
        <div>
          <strong>${escapeHtml(data.row.document_type)}</strong>
          <div>${escapeHtml(data.row.file_name)}</div>
        </div>
      </div>`;

    document.getElementById('documentsList').insertAdjacentHTML('afterbegin', html);

    pageAlert('success', 'Uploaded');
  };

})();
</script>

<?= $this->endSection() ?>