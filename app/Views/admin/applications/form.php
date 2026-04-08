<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container .select2-selection--single {
    height: 38px !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 0.375rem !important;
    padding: 4px 10px !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 28px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px !important;
}
.select2-dropdown {
    border-radius: 0.375rem !important;
    border: 1px solid #dee2e6 !important;
}
</style>

<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h3 class="mb-0">Add Application</h3>
        <div class="text-muted">Create a new application with default workflow</div>
    </div>
    <a class="btn btn-outline-secondary" href="<?= site_url('admin/applications') ?>">Back</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="post">
            <?= csrf_field() ?>

            <div class="row g-3">
                
                <div class="col-md-6">
                    <label class="form-label">Applicant</label>

                    <input type="hidden" name="applicant_id" id="applicantId" value="<?= esc(old('applicant_id', $application['applicant_id'] ?? '')) ?>">

                    <div class="input-group">
                        <input
                            type="text"
                            id="applicantDisplay"
                            class="form-control"
                            value="<?= esc(old('applicant_display', '')) ?>"
                            placeholder="No applicant selected"
                            readonly
                        >
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#applicantModal">
                            Select Applicant
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Job</label>

                    <input type="hidden" name="job_list_id" id="jobListId" value="<?= esc(old('job_list_id', $application['job_list_id'] ?? '')) ?>">

                    <div class="input-group">
                        <input
                            type="text"
                            id="jobDisplay"
                            class="form-control"
                            value="<?= esc(old('job_display', '')) ?>"
                            placeholder="No job selected"
                            readonly
                        >
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#jobModal">
                            Select Job
                        </button>
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Assign Processor</label>
                    <select name="assigned_user_id" id="processorSelect" class="form-select">
                        <option value="">Unassigned</option>
                        <?php foreach (($processorOptions ?? []) as $id => $label): ?>
                            <option value="<?= esc($id) ?>" <?= old('assigned_user_id') == $id ? 'selected' : '' ?>>
                                <?= esc($label) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Source</label>
                    <input type="text" name="source" class="form-control" value="<?= esc(old('source', 'Manual')) ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Applied At</label>
                    <input type="datetime-local" name="applied_at" class="form-control" value="<?= esc(old('applied_at', date('Y-m-d\TH:i'))) ?>">
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-outline-secondary" href="<?= site_url('admin/applications') ?>">Cancel</a>
            </div>
        </form>
    </div>

    <div class="modal fade" id="applicantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Select Applicant</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="d-flex justify-content-between gap-2 mb-3">
                    <input type="text" id="applicantSearchInput" class="form-control" placeholder="Search applicant, email, phone, city...">
                    <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addApplicantInline">
                        <i class="bi bi-plus-lg me-1"></i>Add Applicant
                    </button>
                </div>

                <div class="collapse mb-3" id="addApplicantInline">
                    <div class="card card-body bg-light">
                        <form id="applicantForm">
                            <?= csrf_field() ?>
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="firstname" class="form-control" required>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Middle Name</label>
                                    <input type="text" name="middlename" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="lastname" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">City</label>
                                    <input type="text" name="city" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Province</label>
                                    <input type="text" name="province" class="form-control">
                                </div>
                                <div class="col-md-3 d-flex align-items-end">
                                    <button type="button" class="btn btn-primary w-100" id="saveApplicantBtn">Save Applicant</button>
                                </div>
                            </div>
                        </form>

                        <div id="applicantFormError" class="text-danger small mt-2 d-none"></div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle" id="applicantTable">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>City</th>
                                <th>Province</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="7" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

    <div class="modal fade" id="jobModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Select Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <input type="text" id="jobSearchInput" class="form-control" placeholder="Search job, company, department, location...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="jobTable">
                            <thead class="table-light">
                                <tr>
                                    <th>ID</th>
                                    <th>Job</th>
                                    <th>Company</th>
                                    <th>Department</th>
                                    <th>Location</th>
                                    <th>Salary</th>
                                    <th>Experience</th>
                                    <th>Status</th>
                                    <th>Valid From</th>
                                    <th>Valid To</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="11" class="text-center text-muted">Loading...</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function () {
    $('#applicantSelect').select2({
        placeholder: '-- Select Applicant --',
        width: '100%',
        dropdownParent: $(document.body)
    });

    $('#processorSelect').select2({
        placeholder: 'Unassigned',
        allowClear: true,
        width: '100%',
        dropdownParent: $(document.body)
    });
});

async function loadApplicants(query = '') {
    const tbody = document.querySelector('#applicantTable tbody');
    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Loading...</td></tr>`;

    try {
        const res = await fetch("<?= site_url('admin/applicants/list') ?>?q=" + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await res.json();

        if (!result.success || !Array.isArray(result.rows) || !result.rows.length) {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No applicants found.</td></tr>`;
            return;
        }

        tbody.innerHTML = '';

        result.rows.forEach(function (row) {
            const fullName = [
                row.firstname || '',
                row.middlename || '',
                row.lastname || ''
            ].filter(Boolean).join(' ');

            const displayText = [
                fullName,
                row.email || '',
                row.phone || ''
            ].filter(Boolean).join(' | ');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.id ?? ''}</td>
                <td>${fullName}</td>
                <td>${row.email ?? ''}</td>
                <td>${row.phone ?? ''}</td>
                <td>${row.city ?? ''}</td>
                <td>${row.province ?? ''}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary select-applicant-btn">Select</button>
                </td>
            `;

            tr.querySelector('.select-applicant-btn').addEventListener('click', function () {
                document.getElementById('applicantId').value = row.id ?? '';
                document.getElementById('applicantDisplay').value = displayText || ('Applicant #' + row.id);

                const modalEl = document.getElementById('applicantModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            });

            tbody.appendChild(tr);
        });
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Failed to load applicants.</td></tr>`;
    }
}

document.getElementById('saveApplicantBtn').addEventListener('click', async function () {
    const form = document.getElementById('applicantForm');
    const errorBox = document.getElementById('applicantFormError');
    const formData = new FormData(form);

    errorBox.classList.add('d-none');
    errorBox.textContent = '';

    try {
        const response = await fetch("<?= site_url('admin/applicants/add') ?>", {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await response.json();

        if (!result.success) {
            errorBox.textContent = result.message || 'Failed to create applicant.';
            errorBox.classList.remove('d-none');
            return;
        }

        document.getElementById('applicantId').value = result.id;
        document.getElementById('applicantDisplay').value = result.name;

        form.reset();

        const collapseEl = document.getElementById('addApplicantInline');
        const collapse = bootstrap.Collapse.getInstance(collapseEl);
        if (collapse) {
            collapse.hide();
        }

        loadApplicants();

    } catch (error) {
        errorBox.textContent = 'An unexpected error occurred.';
        errorBox.classList.remove('d-none');
    }
});

document.getElementById('applicantModal').addEventListener('shown.bs.modal', function () {
    loadApplicants();
});

document.getElementById('applicantSearchInput').addEventListener('input', function () {
    loadApplicants(this.value);
});
async function loadJobs(query = '') {
    const tbody = document.querySelector('#jobTable tbody');
    tbody.innerHTML = `<tr><td colspan="11" class="text-center text-muted">Loading...</td></tr>`;

    try {
        const res = await fetch("<?= site_url('admin/job-posts/list') ?>?q=" + encodeURIComponent(query), {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        const result = await res.json();

        if (!result.success || !Array.isArray(result.rows) || !result.rows.length) {
            tbody.innerHTML = `<tr><td colspan="11" class="text-center text-muted">No jobs found.</td></tr>`;
            return;
        }

        tbody.innerHTML = '';

        result.rows.forEach(function (row) {
            const displayText = [
                row.job_name || '',
                row.company_name || '',
                row.department_name || '',
                row.location || ''
            ].filter(Boolean).join(' | ');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${row.id ?? ''}</td>
                <td>${row.job_name ?? ''}</td>
                <td>${row.company_name ?? ''}</td>
                <td>${row.department_name ?? ''}</td>
                <td>${row.location ?? ''}</td>
                <td>${row.salary_range ?? ''}</td>
                <td>${row.experience_range ?? ''}</td>
                <td>${row.status_name ?? ''}</td>
                <td>${row.valid_from ?? ''}</td>
                <td>${row.valid_to ?? ''}</td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-primary select-job-btn">Select</button>
                </td>
            `;

            tr.querySelector('.select-job-btn').addEventListener('click', function () {
                document.getElementById('jobListId').value = row.id ?? '';
                document.getElementById('jobDisplay').value = displayText || ('Job #' + row.id);

                const modalEl = document.getElementById('jobModal');
                const modal = bootstrap.Modal.getInstance(modalEl);
                if (modal) {
                    modal.hide();
                }
            });

            tbody.appendChild(tr);
        });
    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="11" class="text-center text-danger">Failed to load jobs.</td></tr>`;
    }
}

document.getElementById('jobModal').addEventListener('shown.bs.modal', function () {
    loadJobs();
});

document.getElementById('jobSearchInput').addEventListener('input', function () {
    loadJobs(this.value);
});
</script>

<?= $this->endSection() ?>