<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$fullName = trim(
    ($applicant['firstname'] ?? '') . ' ' .
    ($applicant['middlename'] ?? '') . ' ' .
    ($applicant['lastname'] ?? '')
);

$educations = $educations ?? [];
$jobHistory = $jobHistory ?? [];
$employment = $employment ?? [];
$isEdit = $mode === 'edit';
?>

<style>
  .applicant-form-wrap {
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

  .applicant-subtitle {
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

  .section-note {
    font-size: .92rem;
    color: #6b7280;
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
    margin-bottom: 0.45rem;
  }

  .profile-value {
    font-size: 1rem;
    color: #1f2937;
    word-break: break-word;
  }

  .profile-value .form-control,
  .profile-value .form-select,
  .profile-value .form-control:focus,
  .profile-value .form-select:focus {
    border-radius: 12px;
  }

  .profile-item.full {
    grid-column: 1 / -1;
  }

  .application-list {
    display: grid;
    gap: 0.75rem;
  }

  .application-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    border: 1px solid #edf2f7;
    background: #f8fafc;
    border-radius: 14px;
    padding: 1rem;
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

  .inline-edit-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
  }

  .icon-btn {
    width: 36px;
    height: 36px;
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

  .section-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    flex-wrap: wrap;
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

  .empty-state {
    padding: 1.25rem;
    border: 1px dashed #d0d5dd;
    border-radius: 14px;
    background: #f8fafc;
    color: #98a2b3;
    text-align: center;
    font-size: 0.98rem;
  }

  .row-card-fields {
    display: grid;
    gap: .75rem;
  }

  .row-card-fields .row {
    --bs-gutter-x: 1rem;
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
      align-items: stretch;
    }
  }
</style>

<div class="applicant-form-wrap">
  <!-- <form method="post"
        action="<?= $isEdit ? site_url('admin/applicants/update/' . $applicant['id']) : site_url('admin/applicants/create') ?>"
        enctype="multipart/form-data"> -->
<form id="applicantForm"
      method="post"
      action="<?= $isEdit
          ? site_url('admin/applicants/edit/' . $applicant['id'])
          : site_url('admin/applicants/create') ?>"
      enctype="multipart/form-data">
    <?= csrf_field() ?>

    <div class="applicant-hero">
      <div>
        <h1 class="applicant-name">
          <?= esc($isEdit ? ($fullName !== '' ? $fullName : 'Edit Applicant') : 'Create Applicant') ?>
        </h1>
        <div class="applicant-subtitle">
          <?= esc($isEdit ? 'Update applicant information, education, job history, and employment details.' : 'Enter applicant information, education, job history, and supporting details.') ?>
        </div>
      </div>

      <div class="d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-check-circle me-1"></i>
          <?= esc($isEdit ? 'Update Applicant' : 'Create Applicant') ?>
        </button>

        <a class="btn btn-outline-secondary" href="<?= site_url('admin/applicants') ?>">
          <i class="bi bi-arrow-left me-1"></i> Back
        </a>
      </div>
    </div>

    <div class="row g-4">

      <!-- PROFILE -->
      <div class="col-12">
        <div class="applicant-card">
          <div class="toggle-card-header">
            <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#profileSection" aria-expanded="true" aria-controls="profileSection">
              <span>Profile</span>
              <i class="bi bi-chevron-down toggle-icon"></i>
            </button>
          </div>

          <div id="profileSection" class="collapse show">
            <div class="card-body">
              <div class="section-note">Main applicant identity, contact details, and resume upload.</div>

              <div class="profile-grid">
                <div class="profile-item">
                  <div class="profile-label">First Name</div>
                  <div class="profile-value">
                    <input type="text" name="firstname" class="form-control" value="<?= esc(old('firstname', $applicant['firstname'] ?? '')) ?>" required>
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Middle Name</div>
                  <div class="profile-value">
                    <input type="text" name="middlename" class="form-control" value="<?= esc(old('middlename', $applicant['middlename'] ?? '')) ?>">
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Last Name</div>
                  <div class="profile-value">
                    <input type="text" name="lastname" class="form-control" value="<?= esc(old('lastname', $applicant['lastname'] ?? '')) ?>" required>
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Email</div>
                  <div class="profile-value">
                    <input type="email" name="email" class="form-control" value="<?= esc(old('email', $applicant['email'] ?? '')) ?>" required>
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Phone</div>
                  <div class="profile-value">
                    <input type="text" name="phone" class="form-control" value="<?= esc(old('phone', $applicant['phone'] ?? '')) ?>">
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Birthdate</div>
                  <div class="profile-value">
                    <input type="date" name="birthdate" class="form-control" value="<?= esc(old('birthdate', $applicant['birthdate'] ?? '')) ?>">
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Gender</div>
                  <div class="profile-value">
                    <select name="gender" class="form-select">
                      <option value="">Select gender</option>
                      <?php foreach (($genderOptions ?? []) as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= old('gender', $applicant['gender'] ?? '') == $value ? 'selected' : '' ?>>
                          <?= esc($label) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Civil Status</div>
                  <div class="profile-value">
                    <select name="civil_status" class="form-select">
                      <option value="">Select civil status</option>
                      <?php foreach (($civilStatusOptions ?? []) as $value => $label): ?>
                        <option value="<?= esc($value) ?>" <?= old('civil_status', $applicant['civil_status'] ?? '') == $value ? 'selected' : '' ?>>
                          <?= esc($label) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Nationality</div>
                  <div class="profile-value">
                    <input type="text" name="nationality" class="form-control" value="<?= esc(old('nationality', $applicant['nationality'] ?? '')) ?>">
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">City</div>
                  <div class="profile-value">
                    <input type="text" name="city" class="form-control" value="<?= esc(old('city', $applicant['city'] ?? '')) ?>">
                  </div>
                </div>

                <div class="profile-item">
                  <div class="profile-label">Province</div>
                  <div class="profile-value">
                    <input type="text" name="province" class="form-control" value="<?= esc(old('province', $applicant['province'] ?? '')) ?>">
                  </div>
                </div>

                <div class="profile-item full">
                  <div class="profile-label">Address</div>
                  <div class="profile-value">
                    <textarea name="address" class="form-control" rows="3"><?= esc(old('address', $applicant['address'] ?? '')) ?></textarea>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
        <!-- DOCUMENTS -->
        <div class="col-12">
  <div class="applicant-card">
    <div class="toggle-card-header">
      <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#documentsSection" aria-expanded="true" aria-controls="documentsSection">
        <span>Document Attachments</span>
        <i class="bi bi-chevron-down toggle-icon"></i>
      </button>
    </div>

    <div id="documentsSection" class="collapse show">
      <div class="card-body">
        <div class="section-toolbar">
          <div class="text-muted small">
            Upload Resume, Cover Letter, Transcript, Certificates, and other supporting files here.
          </div>
        </div>

        <?php if (($mode ?? 'create') === 'create' || empty($applicant['id'])): ?>
          <div class="empty-state">
            Save the applicant first before uploading documents.
          </div>
        <?php else: ?>
          <div id="uploadDocumentForm" class="mb-4">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Document Type</label>
                <select name="document_type" class="form-select">
                    <option value="">Select document type</option>
                    <?php foreach (($documentTypeOptions ?? []) as $value => $label): ?>
                        <option value="<?= esc($value) ?>"><?= esc($label) ?></option>
                    <?php endforeach; ?>
                </select>
             </div>

              <div class="col-md-4">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
              </div>

              <div class="col-md-4">
                <label class="form-label">File</label>
                <input type="file" name="document_file" class="form-control">
              </div>

              <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-primary" onclick="uploadDocument()">
                  <i class="bi bi-upload me-1"></i> Upload Document
                </button>
              </div>
            </div>
          </div>

          <div id="documentsList" class="application-list">
            <?php if (!empty($documents)): ?>
              <?php foreach ($documents as $doc): ?>
                <div class="application-item doc-item" data-id="<?= esc($doc['id']) ?>">
                  <div class="flex-grow-1">
                    <div class="application-job"><?= esc($doc['document_type'] ?? 'Document') ?></div>
                    <div class="application-meta"><?= esc($doc['file_name'] ?? '-') ?></div>
                    <?php if (!empty($doc['remarks'])): ?>
                      <div class="application-meta"><?= esc($doc['remarks']) ?></div>
                    <?php endif; ?>
                  </div>

                  <div class="inline-edit-actions">
                    <a href="<?= base_url($doc['file_path']) ?>" target="_blank" class="icon-btn text-decoration-none">
                      <i class="bi bi-eye"></i>
                    </a>
                    <button type="button" class="icon-btn text-danger" onclick="deleteDocumentRow(this.closest('.doc-item'))">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            <?php else: ?>
              <div id="documentsEmpty" class="empty-state">No uploaded documents yet.</div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>  

         <!-- EDUCATION -->
      <div class="col-12">
        <div class="applicant-card">
          <div class="toggle-card-header">
            <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#educationSection" aria-expanded="true" aria-controls="educationSection">
              <span>Education</span>
              <i class="bi bi-chevron-down toggle-icon"></i>
            </button>
          </div>

          <div id="educationSection" class="collapse show">
            <div class="card-body">
              <div class="section-toolbar">
                <div class="text-muted small">Add one or more education records in the same design style as the applicant view.</div>
                <button type="button" class="btn btn-primary btn-sm" onclick="addEducation()">
                  <i class="bi bi-plus-lg me-1"></i> Add Education
                </button>
              </div>

              <div id="education-wrapper" class="application-list">
                <?php if (empty($educations)): ?>
                  <div class="application-item education-item">
                    <div class="flex-grow-1 row-card-fields">
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label class="form-label">School Name</label>
                          <input type="text" name="education_school_name[]" class="form-control">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Degree</label>
                          <input type="text" name="education_degree[]" class="form-control">
                        </div>
                        <div class="col-md-6">
                          <label class="form-label">Field of Study</label>
                          <input type="text" name="education_field_of_study[]" class="form-control">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">Start Year</label>
                          <input type="text" name="education_start_year[]" class="form-control">
                        </div>
                        <div class="col-md-3">
                          <label class="form-label">End Year</label>
                          <input type="text" name="education_end_year[]" class="form-control">
                        </div>
                        <div class="col-12">
                          <label class="form-label">Honors</label>
                          <input type="text" name="education_honors[]" class="form-control">
                        </div>
                      </div>
                    </div>

                    <div class="inline-edit-actions">
                      <button type="button" class="icon-btn text-danger" onclick="removeRow(this)">
                        <i class="bi bi-trash"></i>
                      </button>
                    </div>
                  </div>
                <?php else: ?>
                  <?php foreach ($educations as $edu): ?>
                    <div class="application-item education-item">
                      <div class="flex-grow-1 row-card-fields">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label">School Name</label>
                            <input type="text" name="education_school_name[]" class="form-control" value="<?= esc($edu['school_name'] ?? '') ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Degree</label>
                            <input type="text" name="education_degree[]" class="form-control" value="<?= esc($edu['degree'] ?? '') ?>">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Field of Study</label>
                            <input type="text" name="education_field_of_study[]" class="form-control" value="<?= esc($edu['field_of_study'] ?? '') ?>">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Start Year</label>
                            <input type="text" name="education_start_year[]" class="form-control" value="<?= esc($edu['start_year'] ?? '') ?>">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">End Year</label>
                            <input type="text" name="education_end_year[]" class="form-control" value="<?= esc($edu['end_year'] ?? '') ?>">
                          </div>
                          <div class="col-12">
                            <label class="form-label">Honors</label>
                            <input type="text" name="education_honors[]" class="form-control" value="<?= esc($edu['honors'] ?? '') ?>">
                          </div>
                        </div>
                      </div>

                      <div class="inline-edit-actions">
                        <button type="button" class="icon-btn text-danger" onclick="removeRow(this)">
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

      <!-- JOB HISTORY -->
      <div class="col-12">
        <div class="applicant-card">
          <div class="toggle-card-header">
            <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#jobHistorySection" aria-expanded="true" aria-controls="jobHistorySection">
              <span>Job History</span>
              <i class="bi bi-chevron-down toggle-icon"></i>
            </button>
          </div>

          <div id="jobHistorySection" class="collapse show">
            <div class="card-body">
              <div class="section-toolbar">
                <div class="text-muted small">Structured like the view page, with add/remove and optional drag reorder.</div>
                <button type="button" class="btn btn-primary btn-sm" onclick="addJob()">
                  <i class="bi bi-plus-lg me-1"></i> Add Job History
                </button>
              </div>

              <div id="job-wrapper" class="application-list">
                <?php if (empty($jobHistory)): ?>
                  <div class="application-item job-item">
                    <div class="d-flex align-items-start gap-2 w-100">
                      <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>

                      <div class="flex-grow-1 row-card-fields">
                        <div class="row g-3">
                          <div class="col-md-6">
                            <label class="form-label">Company Name</label>
                            <input type="text" name="job_company_name[]" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Company Address</label>
                            <input type="text" name="job_company_address[]" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="job_title[]" class="form-control">
                          </div>
                          <div class="col-md-6">
                            <label class="form-label">Department</label>
                            <input type="text" name="job_department[]" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="job_start_date[]" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="job_end_date[]" class="form-control">
                          </div>
                          <div class="col-md-3">
                            <label class="form-label">Salary</label>
                            <input type="text" name="job_salary[]" class="form-control">
                          </div>
                          <div class="col-md-3 d-flex align-items-end">
                            <div class="form-check mb-2">
                              <input type="checkbox" name="job_currently_working[0]" value="1" class="form-check-input">
                              <label class="form-check-label">Currently Working</label>
                            </div>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Responsibilities</label>
                            <textarea name="job_responsibilities[]" class="form-control" rows="3"></textarea>
                          </div>
                          <div class="col-12">
                            <label class="form-label">Reason for Leaving</label>
                            <input type="text" name="job_reason_for_leaving[]" class="form-control">
                          </div>
                        </div>
                      </div>

                      <div class="inline-edit-actions">
                        <button type="button" class="icon-btn text-danger" onclick="removeRow(this)">
                          <i class="bi bi-trash"></i>
                        </button>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <?php foreach ($jobHistory as $i => $job): ?>
                    <div class="application-item job-item">
                      <div class="d-flex align-items-start gap-2 w-100">
                        <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>

                        <div class="flex-grow-1 row-card-fields">
                          <div class="row g-3">
                            <div class="col-md-6">
                              <label class="form-label">Company Name</label>
                              <input type="text" name="job_company_name[]" class="form-control" value="<?= esc($job['company_name'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Company Address</label>
                              <input type="text" name="job_company_address[]" class="form-control" value="<?= esc($job['company_address'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Job Title</label>
                              <input type="text" name="job_title[]" class="form-control" value="<?= esc($job['job_title'] ?? '') ?>">
                            </div>
                            <div class="col-md-6">
                              <label class="form-label">Department</label>
                              <input type="text" name="job_department[]" class="form-control" value="<?= esc($job['department'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                              <label class="form-label">Start Date</label>
                              <input type="date" name="job_start_date[]" class="form-control" value="<?= esc($job['start_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                              <label class="form-label">End Date</label>
                              <input type="date" name="job_end_date[]" class="form-control" value="<?= esc($job['end_date'] ?? '') ?>">
                            </div>
                            <div class="col-md-3">
                              <label class="form-label">Salary</label>
                              <input type="text" name="job_salary[]" class="form-control" value="<?= esc($job['salary'] ?? '') ?>">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                              <div class="form-check mb-2">
                                <input type="checkbox" name="job_currently_working[<?= $i ?>]" value="1" class="form-check-input" <?= !empty($job['currently_working']) ? 'checked' : '' ?>>
                                <label class="form-check-label">Currently Working</label>
                              </div>
                            </div>
                            <div class="col-12">
                              <label class="form-label">Responsibilities</label>
                              <textarea name="job_responsibilities[]" class="form-control" rows="3"><?= esc($job['responsibilities'] ?? '') ?></textarea>
                            </div>
                            <div class="col-12">
                              <label class="form-label">Reason for Leaving</label>
                              <input type="text" name="job_reason_for_leaving[]" class="form-control" value="<?= esc($job['reason_for_leaving'] ?? '') ?>">
                            </div>
                          </div>
                        </div>

                        <div class="inline-edit-actions">
                          <button type="button" class="icon-btn text-danger" onclick="removeRow(this)">
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

      <!-- EMPLOYMENT DETAILS -->
      <?php if (($applicant['status_name'] ?? '') === 'Hired' || !empty($employment)): ?>
        <div class="col-12">
          <div class="applicant-card">
            <div class="toggle-card-header">
              <button class="toggle-btn" type="button" data-bs-toggle="collapse" data-bs-target="#employmentSection" aria-expanded="true" aria-controls="employmentSection">
                <span>Employment Details</span>
                <i class="bi bi-chevron-down toggle-icon"></i>
              </button>
            </div>

            <div id="employmentSection" class="collapse show">
              <div class="card-body">
                <div class="section-note">Shown when the applicant is hired or already has employment details.</div>

                <div class="profile-grid">
                  <div class="profile-item">
                    <div class="profile-label">Employee No</div>
                    <div class="profile-value">
                      <input type="text" name="employee_no" class="form-control" value="<?= esc(old('employee_no', $employment['employee_no'] ?? '')) ?>">
                    </div>
                  </div>

                  <div class="profile-item">
                    <div class="profile-label">Position</div>
                    <div class="profile-value">
                      <input type="text" name="position" class="form-control" value="<?= esc(old('position', $employment['position'] ?? '')) ?>">
                    </div>
                  </div>

                  <div class="profile-item">
                    <div class="profile-label">Department</div>
                    <div class="profile-value">
                      <input type="text" name="department" class="form-control" value="<?= esc(old('department', $employment['department'] ?? '')) ?>">
                    </div>
                  </div>

                  <div class="profile-item">
                    <div class="profile-label">Employment Type</div>
                    <div class="profile-value">
                      <input type="text" name="employment_type" class="form-control" value="<?= esc(old('employment_type', $employment['employment_type'] ?? '')) ?>">
                    </div>
                  </div>

                  <div class="profile-item">
                    <div class="profile-label">Date Hired</div>
                    <div class="profile-value">
                      <input type="date" name="date_hired" class="form-control" value="<?= esc(old('date_hired', $employment['date_hired'] ?? '')) ?>">
                    </div>
                  </div>

                  <div class="profile-item">
                    <div class="profile-label">Salary</div>
                    <div class="profile-value">
                      <input type="text" name="salary" class="form-control" value="<?= esc(old('salary', $employment['salary'] ?? '')) ?>">
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

    </div>
  </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
<script>
(function () {
  const form = document.getElementById('applicantForm');
  const educationWrapper = document.getElementById('education-wrapper');
  const jobWrapper = document.getElementById('job-wrapper');
  const applicantId = <?= (int) ($applicant['id'] ?? 0) ?>;

  function showFlash(type, message) {
    if (typeof window.showFlashMessage === 'function') {
      window.showFlashMessage(type, message);
      return;
    }

    if (typeof window.flashMessage === 'function') {
      window.flashMessage(type, message);
      return;
    }

    console.log(type.toUpperCase() + ': ' + message);
  }

  function escapeHtml(val) {
    return String(val ?? '').replace(/[&<>"']/g, function (m) {
      return {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
      }[m];
    });
  }

  window.removeRow = function (button) {
    const row = button.closest('.application-item');
    if (row) row.remove();
  };

  window.addEducation = function () {
    if (!educationWrapper) return;

    educationWrapper.insertAdjacentHTML('beforeend', `
      <div class="application-item education-item">
        <div class="flex-grow-1 row-card-fields">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">School Name</label>
              <input type="text" name="education_school_name[]" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Degree</label>
              <input type="text" name="education_degree[]" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Field of Study</label>
              <input type="text" name="education_field_of_study[]" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">Start Year</label>
              <input type="text" name="education_start_year[]" class="form-control">
            </div>
            <div class="col-md-3">
              <label class="form-label">End Year</label>
              <input type="text" name="education_end_year[]" class="form-control">
            </div>
            <div class="col-12">
              <label class="form-label">Honors</label>
              <input type="text" name="education_honors[]" class="form-control">
            </div>
          </div>
        </div>
        <div class="inline-edit-actions">
          <button type="button" class="icon-btn text-danger" onclick="removeRow(this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `);
  };

  window.addJob = function () {
    if (!jobWrapper) return;

    const idx = document.querySelectorAll('#job-wrapper .job-item').length;

    jobWrapper.insertAdjacentHTML('beforeend', `
      <div class="application-item job-item">
        <div class="d-flex align-items-start gap-2 w-100">
          <span class="drag-handle"><i class="bi bi-grip-vertical"></i></span>
          <div class="flex-grow-1 row-card-fields">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Company Name</label>
                <input type="text" name="job_company_name[]" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Company Address</label>
                <input type="text" name="job_company_address[]" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Job Title</label>
                <input type="text" name="job_title[]" class="form-control">
              </div>
              <div class="col-md-6">
                <label class="form-label">Department</label>
                <input type="text" name="job_department[]" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" name="job_start_date[]" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" name="job_end_date[]" class="form-control">
              </div>
              <div class="col-md-3">
                <label class="form-label">Salary</label>
                <input type="text" name="job_salary[]" class="form-control">
              </div>
              <div class="col-md-3 d-flex align-items-end">
                <div class="form-check mb-2">
                  <input type="checkbox" name="job_currently_working[${idx}]" value="1" class="form-check-input">
                  <label class="form-check-label">Currently Working</label>
                </div>
              </div>
              <div class="col-12">
                <label class="form-label">Responsibilities</label>
                <textarea name="job_responsibilities[]" class="form-control" rows="3"></textarea>
              </div>
              <div class="col-12">
                <label class="form-label">Reason for Leaving</label>
                <input type="text" name="job_reason_for_leaving[]" class="form-control">
              </div>
            </div>
          </div>
          <div class="inline-edit-actions">
            <button type="button" class="icon-btn text-danger" onclick="removeRow(this)">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
    `);
  };

  if (typeof Sortable !== 'undefined' && jobWrapper) {
    Sortable.create(jobWrapper, {
      animation: 150,
      handle: '.drag-handle',
      ghostClass: 'sortable-ghost',
      chosenClass: 'sortable-chosen'
    });
  }

  if (form) {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const submitBtn = form.querySelector('button[type="submit"]');
      const originalHtml = submitBtn ? submitBtn.innerHTML : '';

      try {
        if (submitBtn) {
          submitBtn.disabled = true;
          submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
        }

        const fd = new FormData(form);

        const res = await fetch(form.action, {
          method: 'POST',
          body: fd,
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await res.json();

        if (!res.ok || !data.success) {
          showFlash('error', data.message || 'Save failed.');
          return;
        }

        showFlash('success', data.message || 'Saved successfully.');

        if (data.edit_url) {
          form.action = data.edit_url;
          window.history.replaceState({}, '', data.edit_url);
        }
      } catch (err) {
        showFlash('error', 'An error occurred while saving.');
      } finally {
        if (submitBtn) {
          submitBtn.disabled = false;
          submitBtn.innerHTML = originalHtml;
        }
      }
    });
  }

  window.uploadDocument = async function () {
    const uploadWrap = document.getElementById('uploadDocumentForm');
    const documentsList = document.getElementById('documentsList');
    const empty = document.getElementById('documentsEmpty');
    let currentApplicantId = <?= (int) ($applicant['id'] ?? 0) ?>;

    if (!currentApplicantId && form) {
      const match = form.action.match(/\/edit\/(\d+)$/);
      if (match) currentApplicantId = match[1];
    }

    if (!uploadWrap) return;

    const documentType = uploadWrap.querySelector('[name="document_type"]');
    const remarks = uploadWrap.querySelector('[name="remarks"]');
    const fileInput = uploadWrap.querySelector('[name="document_file"]');

    if (!currentApplicantId) {
      showFlash('warning', 'Save the applicant first before uploading documents.');
      return;
    }

    if (!documentType || !documentType.value) {
      showFlash('error', 'Document type is required.');
      return;
    }

    if (!fileInput || !fileInput.files || !fileInput.files.length) {
      showFlash('error', 'Please select a file.');
      return;
    }

    try {
      const fd = new FormData();
      fd.append('document_type', documentType.value);
      fd.append('remarks', remarks ? remarks.value : '');
      fd.append('document_file', fileInput.files[0]);

      const res = await fetch(`<?= site_url('admin/applicants/upload-document') ?>/${currentApplicantId}`, {
        method: 'POST',
        body: fd,
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        showFlash('error', data.message || 'Upload failed.');
        return;
      }

      if (empty) empty.remove();

      const row = data.row;
      const html = `
        <div class="application-item doc-item" data-id="${row.id}">
          <div class="flex-grow-1">
            <div class="application-job">${escapeHtml(row.document_type || 'Document')}</div>
            <div class="application-meta">${escapeHtml(row.file_name || '-')}</div>
            ${row.remarks ? `<div class="application-meta">${escapeHtml(row.remarks)}</div>` : ''}
          </div>
          <div class="inline-edit-actions">
            <a href="${data.url}" target="_blank" class="icon-btn text-decoration-none">
              <i class="bi bi-eye"></i>
            </a>
            <button type="button" class="icon-btn text-danger" onclick="deleteDocumentRow(this.closest('.doc-item'))">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      `;

      documentsList.insertAdjacentHTML('afterbegin', html);

      documentType.value = '';
      if (remarks) remarks.value = '';
      fileInput.value = '';

      showFlash('success', data.message || 'Document uploaded successfully.');
    } catch (err) {
      showFlash('error', 'An error occurred while uploading document.');
    }
  };

  window.deleteDocumentRow = async function (item) {
    if (!item) return;

    const id = item.dataset.id;
    if (!id) return;

    try {
      const res = await fetch(`<?= site_url('admin/applicants/delete-document') ?>/${id}`, {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      });

      const data = await res.json();

      if (!res.ok || !data.success) {
        showFlash('error', data.message || 'Delete failed.');
        return;
      }

      item.remove();

      const documentsList = document.getElementById('documentsList');
      if (documentsList && !documentsList.querySelector('.doc-item')) {
        documentsList.innerHTML = '<div id="documentsEmpty" class="empty-state">No uploaded documents yet.</div>';
      }

      showFlash('success', data.message || 'Document deleted.');
    } catch (err) {
      showFlash('error', 'An error occurred while deleting document.');
    }
  };
})();
</script>
<?= $this->endSection() ?>