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
.confirm-delete-wrap {
  animation: fadeIn .2s ease;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-2px); }
  to { opacity: 1; transform: translateY(0); }
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
$fullName = trim(($applicant['firstname'] ?? '') . ' ' . ($applicant['middlename'] ?? ''). ' ' . ($applicant['lastname'] ?? ''));

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
      <h1 class="applicant-name" id="heroName"><?= esc($fullName !== '' ? $fullName : 'Unnamed Applicant') ?></h1>
      <div class="applicant-email" id="heroEmail"><?= esc($applicant['email'] ?? '-') ?></div>
      <!-- <pre><?php print_r($applicant); ?></pre> -->
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
      <label class="form-label">First Name</label>
      <input type="text" name="firstname" class="form-control" value="<?= esc($applicant['firstname'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Middle Name</label>
      <input type="text" name="middlename" class="form-control" value="<?= esc($applicant['middlename'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Last Name</label>
      <input type="text" name="lastname" class="form-control" value="<?= esc($applicant['lastname'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Suffix</label>
      <input type="text" name="suffix" class="form-control" value="<?= esc($applicant['suffix'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Phone</label>
      <input type="text" name="phone" class="form-control" value="<?= esc($applicant['phone'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" value="<?= esc($applicant['email'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Birth Date</label>
      <input type="date" name="birthdate" class="form-control" value="<?= esc($applicant['birthdate'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Gender</label>
      <select name="gender" class="form-select">
        <option value="">Select gender</option>
        <?php foreach (($genderOptions ?? []) as $value => $label): ?>
          <option value="<?= esc($value) ?>" <?= (string)($applicant['gender'] ?? '') === (string)$value ? 'selected' : '' ?>>
            <?= esc($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="form-label">Civil Status</label>
      <select name="civil_status" class="form-select">
        <option value="">Select civil status</option>
        <?php foreach (($civilStatusOptions ?? []) as $value => $label): ?>
          <option value="<?= esc($value) ?>" <?= (string)($applicant['civil_status'] ?? '') === (string)$value ? 'selected' : '' ?>>
            <?= esc($label) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="form-label">Nationality</label>
      <input type="text" name="nationality" class="form-control" value="<?= esc($applicant['nationality'] ?? '') ?>">
    </div>

    <div class="full">
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

    <div>
      <label class="form-label">Zip Code</label>
      <input type="text" name="zip_code" class="form-control" value="<?= esc($applicant['zip_code'] ?? '') ?>">
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
                  <div class="profile-value" data-profile="phone"><?= esc($applicant['phone'] ?? '-') ?></div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Email</div>
                  <div class="profile-value" data-profile="email"><?= esc($applicant['email'] ?? '-') ?></div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Address</div>
                  <div class="profile-value" data-profile="address"><?= esc($applicant['address'] ?? '-') ?></div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">City</div>
                  <div class="profile-value" data-profile="city"><?= esc($applicant['city'] ?? '-') ?></div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Province</div>
                  <div class="profile-value" data-profile="province"><?= esc($applicant['province'] ?? '-') ?></div>
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
            <span>Application History</span>
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
                    $applicationId = $application['id']??'';
                    $statusName = $application['status_name'] ?? '-';
                    $jobName = $application['job_name'] ?? '-';
                    $appliedAt = $application['applied_at'] ?? null;
                  ?>
                  <div class="application-item">
                    <div>
                      <div class="application-job"><a href="../applications/<?= esc($applicationId) ?>"><?= esc($jobName) ?></a></div>
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
        <div class="profile-value" data-profile="firstname"><?= esc($applicant['firstname'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Middle Name</div>
        <div class="profile-value" data-profile="middlename"><?= esc($applicant['middlename'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Last Name</div>
        <div class="profile-value" data-profile="lastname"><?= esc($applicant['lastname'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Full Name</div>
        <div class="profile-value" data-profile="full_name"><?= esc($fullName !== '' ? $fullName : '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Gender</div>
        <div class="profile-value" data-profile="gender"><?= esc($applicant['gender'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Civil Status</div>
        <div class="profile-value" data-profile="civil_status"><?= esc($applicant['civil_status'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Nationality</div>
        <div class="profile-value" data-profile="nationality"><?= esc($applicant['nationality'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Birth Date</div>
        <div class="profile-value" data-profile="birthdate"><?= esc($applicant['birthdate'] ?? '-') ?></div>
      </div>

      <div class="profile-item">
        <div class="profile-label">Zip Code</div>
        <div class="profile-value" data-profile="zip_code"><?= esc($applicant['zip_code'] ?? '-') ?></div>
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
    <div class="section-toolbar">
      <div class="text-muted small">Upload and manage applicant documents from this page.</div>
      <?php if (!empty($applicant['id'])): ?>
        <button type="button" class="btn btn-primary btn-sm" onclick="toggleDocumentUpload(true)">
          <i class="bi bi-upload me-1"></i> Upload Document
        </button>
      <?php endif; ?>
    </div>

    <?php if (!empty($applicant['id'])): ?>
      <div id="documentUploadWrap" class="d-none mb-4">
        <div class="applicant-card" style="box-shadow:none;border-style:dashed;">
          <div class="card-body">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Document Type</label>
                <select id="view_document_type" class="form-select">
                  <option value="">Select document type</option>
                  <?php foreach (($documentTypeOptions ?? []) as $value => $label): ?>
                    <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Remarks</label>
                <input type="text" id="view_document_remarks" class="form-control" placeholder="Optional remarks">
              </div>

              <div class="col-md-4">
                <label class="form-label">File</label>
                <input type="file" id="view_document_file" class="form-control">
              </div>

              <div class="col-12 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-outline-secondary" onclick="toggleDocumentUpload(false)">
                  Cancel
                </button>
                <button type="button" class="btn btn-primary" onclick="uploadApplicantDocumentFromView()">
                  <i class="bi bi-upload me-1"></i> Upload
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    <?php endif; ?>

    <div id="documentsList" class="application-list">
      <?php if (empty($documents)): ?>
        <div id="documentsEmpty" class="empty-state">No document records.</div>
      <?php else: ?>
        <?php foreach ($documents as $doc): ?>
          <div class="application-item doc-item" data-id="<?= esc($doc['id']) ?>">
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
                  <?= !empty($doc['file_size']) ? number_format(((float) $doc['file_size']) / 1024, 2) . ' KB' : '' ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="inline-edit-actions">
              <a class="resume-chip" href="<?= base_url($doc['file_path']) ?>" target="_blank">
                <i class="bi bi-file-earmark-text"></i>
                View File
              </a>

              <button type="button" class="icon-btn text-danger" onclick="deleteApplicantDocument(<?= (int) $doc['id'] ?>, this)">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
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

    <div id="educationList" class="application-list">
      <?php if (empty($educations)): ?>
        <div class="empty-state" id="educationEmpty">No education records.</div>
      <?php else: ?>
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
              <?php if (!empty($edu['start_year']) || !empty($edu['end_year'])): ?>
                <div class="application-meta">
                  <?= esc($edu['start_year'] ?? '') ?><?= !empty($edu['start_year']) || !empty($edu['end_year']) ? ' - ' : '' ?><?= esc($edu['end_year'] ?? '') ?>
                </div>
              <?php endif; ?>
              <?php if (!empty($edu['honors'])): ?>
                <div class="application-meta"><?= esc($edu['honors']) ?></div>
              <?php endif; ?>
            </div>

            <div class="inline-edit-actions">
              <button type="button" class="icon-btn" onclick="openEducationModal(this.closest('.education-item'))">
                <i class="bi bi-pencil"></i>
              </button>
              <button type="button" class="icon-btn text-danger" onclick="confirmDelete(this, 'education')">
                <i class="bi bi-trash"></i>
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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

    <div id="jobHistoryList" class="application-list">
      <?php if (empty($jobHistory)): ?>
        <div class="empty-state" id="jobHistoryEmpty">No job history records.</div>
      <?php else: ?>
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
                  <?= esc($job['start_date'] ?? '-') ?> - <?= esc(!empty($job['currently_working']) ? 'Present' : ($job['end_date'] ?? '-')) ?>
                </div>
                <?php if (!empty($job['responsibilities'])): ?>
                  <div class="application-meta mt-1"><?= esc($job['responsibilities']) ?></div>
                <?php endif; ?>
              </div>

              <div class="inline-edit-actions">
                <button type="button" class="icon-btn" onclick="openJobModal(this.closest('.job-history-item'))">
                  <i class="bi bi-pencil"></i>
                </button>
                <button type="button" class="icon-btn text-danger" onclick="confirmDelete(this, 'job')">
                  <i class="bi bi-trash"></i>
                </button>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
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

  const form = document.getElementById('profileInlineForm');
  const fd = new FormData(form);

  try {
    const res = await fetch("<?= site_url('admin/applicants/update-inline-profile/' . $applicant['id']) ?>", {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      throw new Error(data.message || 'Failed to save profile');
    }

    document.querySelectorAll('[data-profile]').forEach(el => {
      const key = el.dataset.profile;

      if (key === 'full_name') {
        el.textContent = data.full_name || '-';
      } else {
        el.textContent = fd.get(key) || '-';
      }
    });

    document.getElementById('heroName').textContent = data.full_name || '-';
    document.getElementById('heroEmail').textContent = fd.get('email') || '-';

    toggleProfileEdit(false);
    pageAlert('success', data.message || 'Profile updated successfully.');
  } catch (err) {
    pageAlert('danger', err.message || 'Failed to save profile');
  }
};

  // ================= EDUCATION =================
  window.openEducationModal = function (item = null) {
  document.getElementById('edu_id').value = item?.dataset.id || '';
  document.getElementById('edu_school_name').value = item?.dataset.school_name || '';
  document.getElementById('edu_degree').value = item?.dataset.degree || '';
  document.getElementById('edu_field_of_study').value = item?.dataset.field_of_study || '';
  document.getElementById('edu_start_year').value = item?.dataset.start_year || '';
  document.getElementById('edu_end_year').value = item?.dataset.end_year || '';
  document.getElementById('edu_honors').value = item?.dataset.honors || '';

  educationModal.show();
};

window.saveEducationModal = async function () {
  clearAlert();

  const id = document.getElementById('edu_id').value;
  const school_name = document.getElementById('edu_school_name').value;
  const degree = document.getElementById('edu_degree').value;
  const field_of_study = document.getElementById('edu_field_of_study').value;
  const start_year = document.getElementById('edu_start_year').value;
  const end_year = document.getElementById('edu_end_year').value;
  const honors = document.getElementById('edu_honors').value;

  const fd = new FormData();
  fd.append('id', id);
  fd.append('school_name', school_name);
  fd.append('degree', degree);
  fd.append('field_of_study', field_of_study);
  fd.append('start_year', start_year);
  fd.append('end_year', end_year);
  fd.append('honors', honors);

  const res = await fetch(`<?= site_url('admin/applicants/save-education') ?>/${applicantId}`, {
    method: 'POST',
    body: fd,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  });

  const data = await res.json();

  if (!data.success) {
    pageAlert('danger', data.message || 'Save failed');
    return;
  }

  const row = data.row || {};
  const rowHtml = `
    <div class="application-item education-item"
         data-id="${escapeHtml(row.id || '')}"
         data-school_name="${escapeHtml(row.school_name || '')}"
         data-degree="${escapeHtml(row.degree || '')}"
         data-field_of_study="${escapeHtml(row.field_of_study || '')}"
         data-start_year="${escapeHtml(row.start_year || '')}"
         data-end_year="${escapeHtml(row.end_year || '')}"
         data-honors="${escapeHtml(row.honors || '')}">
      <div class="flex-grow-1">
        <div class="application-job">${escapeHtml(row.school_name || '-')}</div>
        <div class="application-meta">
          ${escapeHtml(row.degree || '-')}
          ${row.field_of_study ? ` • ${escapeHtml(row.field_of_study)}` : ''}
        </div>
        ${(row.start_year || row.end_year) ? `<div class="application-meta">${escapeHtml(row.start_year || '')} - ${escapeHtml(row.end_year || '')}</div>` : ''}
        ${row.honors ? `<div class="application-meta">${escapeHtml(row.honors)}</div>` : ''}
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
  `;

  const list = document.getElementById('educationList');
  const empty = document.getElementById('educationEmpty');
  if (empty) empty.remove();

  const existing = list.querySelector(`.education-item[data-id="${row.id}"]`);
  if (existing) {
    existing.outerHTML = rowHtml;
  } else {
    list.insertAdjacentHTML('afterbegin', rowHtml);
  }

  educationModal.hide();
  pageAlert('success', data.message || 'Education saved');
};
  // ================= JOB =================
  window.openJobModal = function (item = null) {
  document.getElementById('job_id').value = item?.dataset.id || '';
  document.getElementById('job_company_name').value = item?.dataset.company_name || '';
  document.getElementById('job_company_address').value = item?.dataset.company_address || '';
  document.getElementById('job_job_title').value = item?.dataset.job_title || '';
  document.getElementById('job_department').value = item?.dataset.department || '';
  document.getElementById('job_start_date').value = item?.dataset.start_date || '';
  document.getElementById('job_end_date').value = item?.dataset.end_date || '';
  document.getElementById('job_currently_working').checked = (item?.dataset.currently_working || '0') === '1';
  document.getElementById('job_responsibilities').value = item?.dataset.responsibilities || '';
  document.getElementById('job_salary').value = item?.dataset.salary || '';
  document.getElementById('job_reason_for_leaving').value = item?.dataset.reason_for_leaving || '';

  jobModal.show();
};

window.saveJobModal = async function () {
  clearAlert();

  const id = document.getElementById('job_id').value;
  const company_name = document.getElementById('job_company_name').value;
  const company_address = document.getElementById('job_company_address').value;
  const job_title = document.getElementById('job_job_title').value;
  const department = document.getElementById('job_department').value;
  const start_date = document.getElementById('job_start_date').value;
  const end_date = document.getElementById('job_end_date').value;
  const currently_working = document.getElementById('job_currently_working').checked ? '1' : '0';
  const responsibilities = document.getElementById('job_responsibilities').value;
  const salary = document.getElementById('job_salary').value;
  const reason_for_leaving = document.getElementById('job_reason_for_leaving').value;

  const fd = new FormData();
  fd.append('id', id);
  fd.append('company_name', company_name);
  fd.append('company_address', company_address);
  fd.append('job_title', job_title);
  fd.append('department', department);
  fd.append('start_date', start_date);
  fd.append('end_date', end_date);
  fd.append('currently_working', currently_working);
  fd.append('responsibilities', responsibilities);
  fd.append('salary', salary);
  fd.append('reason_for_leaving', reason_for_leaving);

  const res = await fetch(`<?= site_url('admin/applicants/save-job-history') ?>/${applicantId}`, {
    method: 'POST',
    body: fd,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  });

  const data = await res.json();

  if (!data.success) {
    pageAlert('danger', data.message || 'Save failed');
    return;
  }

  const row = data.row || {};
  const rowHtml = `
    <div class="application-item job-history-item"
         data-id="${escapeHtml(row.id || '')}"
         data-company_name="${escapeHtml(row.company_name || '')}"
         data-company_address="${escapeHtml(row.company_address || '')}"
         data-job_title="${escapeHtml(row.job_title || '')}"
         data-department="${escapeHtml(row.department || '')}"
         data-start_date="${escapeHtml(row.start_date || '')}"
         data-end_date="${escapeHtml(row.end_date || '')}"
         data-currently_working="${escapeHtml(row.currently_working || '0')}"
         data-responsibilities="${escapeHtml(row.responsibilities || '')}"
         data-salary="${escapeHtml(row.salary || '')}"
         data-reason_for_leaving="${escapeHtml(row.reason_for_leaving || '')}">
      <div class="d-flex align-items-start gap-2 w-100">
        <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
        <div class="flex-grow-1">
          <div class="application-job">${escapeHtml(row.company_name || '-')}</div>
          <div class="application-meta">
            ${escapeHtml(row.job_title || '-')}
            ${row.department ? ` • ${escapeHtml(row.department)}` : ''}
          </div>
          <div class="application-meta">
            ${escapeHtml(row.start_date || '-')} - ${row.currently_working == 1 ? 'Present' : escapeHtml(row.end_date || '-')}
          </div>
          ${row.responsibilities ? `<div class="application-meta mt-1">${escapeHtml(row.responsibilities)}</div>` : ''}
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
  `;

  const list = document.getElementById('jobHistoryList');
  const empty = document.getElementById('jobHistoryEmpty');
  if (empty) empty.remove();

  const existing = list.querySelector(`.job-history-item[data-id="${row.id}"]`);
  if (existing) {
    existing.outerHTML = rowHtml;
  } else {
    list.insertAdjacentHTML('afterbegin', rowHtml);
  }

  jobModal.hide();
  pageAlert('success', data.message || 'Job history saved');
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
<script>
function toggleDocumentUpload(show) {
  const wrap = document.getElementById('documentUploadWrap');
  if (!wrap) return;
  wrap.classList.toggle('d-none', !show);
}

function uploadApplicantDocumentFromView() {
  const applicantId = <?= (int) ($applicant['id'] ?? 0) ?>;
  const typeEl = document.getElementById('view_document_type');
  const remarksEl = document.getElementById('view_document_remarks');
  const fileEl = document.getElementById('view_document_file');

  if (!typeEl.value) {
    showPageAlert('danger', 'Document type is required.');
    return;
  }

  if (!fileEl.files.length) {
    showPageAlert('danger', 'Please choose a file.');
    return;
  }

  const formData = new FormData();
  formData.append('document_type', typeEl.value);
  formData.append('remarks', remarksEl.value || '');
  formData.append('document_file', fileEl.files[0]);
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  fetch("<?= site_url('admin/applicants/upload-document/') ?>" + applicantId, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(async res => {
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.message || 'Upload failed.');
    return data;
  })
  .then(data => {
    const list = document.getElementById('documentsList');
    const empty = document.getElementById('documentsEmpty');
    if (empty) empty.remove();

    const html = `
      <div class="application-item doc-item" data-id="${data.row.id}">
        <div class="flex-grow-1">
          <div class="application-job">${escapeHtml(data.row.document_type || 'Document')}</div>
          <div class="application-meta">${escapeHtml(data.row.file_name || '-')}</div>
          ${data.row.remarks ? `<div class="application-meta">${escapeHtml(data.row.remarks)}</div>` : ''}
        </div>
        <div class="inline-edit-actions">
          <a class="resume-chip" href="${data.url}" target="_blank">
            <i class="bi bi-file-earmark-text"></i>
            View File
          </a>
          <button type="button" class="icon-btn text-danger" onclick="deleteApplicantDocument(${data.row.id}, this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;
    list.insertAdjacentHTML('afterbegin', html);

    typeEl.value = '';
    remarksEl.value = '';
    fileEl.value = '';
    toggleDocumentUpload(false);
    showPageAlert('success', data.message || 'Document uploaded successfully.');
  })
  .catch(err => showPageAlert('danger', err.message || 'Upload failed.'));
}
function deleteApplicantDocument(id, btn) {
  if (!confirm('Delete this document?')) return;

  const formData = new FormData();
  formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  fetch("<?= site_url('admin/applicants/delete-document/') ?>" + id, {
    method: 'POST',
    body: formData,
    headers: {
      'X-Requested-With': 'XMLHttpRequest',
      'Accept': 'application/json'
    }
  })
  .then(async res => {
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.message || 'Delete failed.');
    return data;
  })
  .then(data => {
    const item = btn.closest('.doc-item');
    if (item) item.remove();

    const list = document.getElementById('documentsList');
    if (list && !list.querySelector('.doc-item')) {
      list.innerHTML = '<div id="documentsEmpty" class="empty-state">No document records.</div>';
    }

    showPageAlert('success', data.message || 'Document deleted successfully.');
  })
  .catch(err => showPageAlert('danger', err.message || 'Delete failed.'));
}

function showPageAlert(type, message) {
  const el = document.getElementById('pageAlert');
  if (!el) return;
  el.className = `alert alert-${type} mb-3`;
  el.innerHTML = message;
  el.classList.remove('d-none');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function escapeHtml(str) {
  return String(str).replace(/[&<>"']/g, function(m) {
    return ({
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    })[m];
  });
}

window.deleteEducationRow = async function (item) {
  if (!item) return;

  const id = item.dataset.id;
  const applicantId = <?= (int) ($applicant['id'] ?? 0) ?>;

  const fd = new FormData();
  fd.append('id', id);
  fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  try {
    const res = await fetch(`<?= site_url('admin/applicants/delete-education') ?>/${applicantId}`, {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      throw new Error(data.message || 'Delete failed');
    }

    item.remove();

    const list = document.getElementById('educationList');
    if (list && !list.querySelector('.education-item')) {
      list.innerHTML = '<div class="empty-state" id="educationEmpty">No education records.</div>';
    }

    showPageAlert('success', data.message || 'Education deleted successfully.');
  } catch (err) {
    showPageAlert('danger', err.message || 'Delete failed.');
  }
};

window.deleteJobRow = async function (item) {
  if (!item) return;

  const id = item.dataset.id;
  const applicantId = <?= (int) ($applicant['id'] ?? 0) ?>;

  const fd = new FormData();
  fd.append('id', id);
  fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

  try {
    const res = await fetch(`<?= site_url('admin/applicants/delete-job-history') ?>/${applicantId}`, {
      method: 'POST',
      body: fd,
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      }
    });

    const data = await res.json();

    if (!res.ok || !data.success) {
      throw new Error(data.message || 'Delete failed');
    }

    item.remove();

    const list = document.getElementById('jobHistoryList');
    if (list && !list.querySelector('.job-history-item')) {
      list.innerHTML = '<div class="empty-state" id="jobHistoryEmpty">No job history records.</div>';
    }

    showPageAlert('success', data.message || 'Job history deleted successfully.');
  } catch (err) {
    showPageAlert('danger', err.message || 'Delete failed.');
  }
};
window.confirmDelete = function(btn, type) {
  const parent = btn.closest('.inline-edit-actions');
  if (!parent || parent.querySelector('.confirm-delete-wrap')) return;

  btn.style.display = 'none';

  const wrap = document.createElement('div');
  wrap.className = 'confirm-delete-wrap d-flex align-items-center gap-2 flex-wrap';
  wrap.innerHTML = `
    <span class="text-danger small fw-semibold">Are you sure you want to delete?</span>
    <button type="button" class="btn btn-sm btn-danger js-confirm-yes">Yes</button>
    <button type="button" class="btn btn-sm btn-outline-secondary js-confirm-cancel">Cancel</button>
  `;

  parent.appendChild(wrap);

  wrap.querySelector('.js-confirm-cancel').addEventListener('click', function () {
    wrap.remove();
    btn.style.display = '';
  });

  wrap.querySelector('.js-confirm-yes').addEventListener('click', function () {
    const item = btn.closest(type === 'education' ? '.education-item' : '.job-history-item');

    if (type === 'education') {
      deleteEducationRow(item);
    } else if (type === 'job') {
      deleteJobRow(item);
    }

    wrap.remove();
    btn.style.display = '';
  });
};
</script>
<?= $this->endSection() ?>