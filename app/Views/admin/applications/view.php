<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0"><?= esc(trim(($application['firstname'] ?? '') . ' ' . ($application['lastname'] ?? ''))) ?> - <?= esc($application['job_name'] ?? '-') ?></h3>
    <div class="text-muted">Application #<?= esc($application['id']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/applications') ?>">Back</a>
</div>

<div class="row g-3">
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5>Application Details</h5>
        <p class="mb-1"><strong>Job:</strong> <?= esc($application['job_name'] ?? '-') ?></p>
        <p class="mb-1"><strong>Email:</strong> <?= esc($application['email'] ?? '-') ?></p>
        <p class="mb-1"><strong>Phone:</strong> <?= esc($application['phone'] ?? '-') ?></p>
        <p class="mb-1"><strong>Source:</strong> <?= esc($application['source'] ?? '-') ?></p>
        <p class="mb-1"><strong>Status:</strong> <?= esc($application['status_name'] ?? '-') ?></p>
        <p class="mb-1"><strong>Processor:</strong> <?= esc($application['processor_name'] ?? 'Unassigned') ?></p>
        <p class="mb-0"><strong>Due At:</strong> <?= esc($application['due_at'] ?? '-') ?></p>
      </div>
    </div>
  </div>
<div class="col-12">
  <div class="card shadow-sm">
    <div class="card-body">
      <h5>Update Workflow</h5>

      <form method="post" action="<?= site_url('admin/applications/' . $application['id'] . '/workflow') ?>">
        <?= csrf_field() ?>

        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Next Status</label>
            <select id="statusSelect" name="status_id" class="form-select">
              <option value="">Loading...</option>
            </select>
            <div class="form-text">Leave as current if no status change is needed.</div>
          </div>

          <div class="col-md-4">
            <label class="form-label">Processor</label>
            <select name="assigned_user_id" class="form-select">
              <option value="">Unassigned</option>
              <?php foreach (($processorOptions ?? []) as $id => $label): ?>
                <option value="<?= esc($id) ?>" <?= (string) $id === (string) ($application['assigned_to'] ?? '') ? 'selected' : '' ?>>
                  <?= esc($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-md-12">
            <label class="form-label">Remarks</label>
            <textarea type="" name="remarks" class="form-control" placeholder=""></textarea>
          </div>
        </div>

        <div class="mt-3">
          <button class="btn btn-primary" type="submit">Update Workflow</button>
        </div>
      </form>
    </div>
  </div>
</div>

    <!-- <div class="col-12 col-lg-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Assign Processor</h5>
          <form method="post" action="<?= site_url('admin/applications/assign/' . $application['id']) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label class="form-label">Processor</label>
              <select name="assigned_user_id" class="form-select">
                <option value="">Unassigned</option>
                <?php foreach (($processorOptions ?? []) as $id => $label): ?>
                  <option value="<?= esc($id) ?>" <?= (string) $id === (string) ($application['assigned_user_id'] ?? '') ? 'selected' : '' ?>>
                    <?= esc($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <button class="btn btn-dark" type="submit">Assign Processor</button>
          </form>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card shadow-sm">
        <div class="card-body">
          <h5>Update Status</h5>
          <form method="post" action="<?= site_url('admin/applications/' . $application['id'] . '/status') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Next Status</label>
                <select id="statusSelect" name="status_id" class="form-select" required>
                  <option value="">Loading...</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label">Remarks</label>
                <input type="text" name="remarks" class="form-control" placeholder="Optional remarks">
              </div>
            </div>
            <button class="btn btn-primary mt-3" type="submit">Update Status</button>
          </form>
        </div>
      </div>
    </div> -->

  <div class="col-12">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5>Audit Trail</h5>

        <?php if (empty($history)): ?>
          <div class="text-muted">No workflow history yet.</div>
        <?php else: ?>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Date</th>
                  <th>From</th>
                  <th>To</th>
                  <th>Assigned From</th>
                  <th>Assigned To</th>
                  <th>Remarks</th>
                  <th>Changed By</th>
                  <th>Due At</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($history as $row): ?>
                  <tr>
                    <td><?= esc($row['date_created'] ?? '-') ?></td>
                    <td><?= esc($row['from_status_name'] ?? '-') ?></td>
                    <td><?= esc($row['to_status_name'] ?? '-') ?></td>
                    <td><?= esc($row['assigned_from_name'] ?? '-') ?></td>
                    <td><?= esc($row['assigned_to_name'] ?? '-') ?></td>
                    <td><?= esc($row['remarks'] ?? '-') ?></td>
                    <td><?= esc($row['changed_by_name'] ?? '-') ?></td>
                    <td><?= esc($row['due_at'] ?? '-') ?></td>
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

<script>
async function loadStatuses(appId, currentStatusId, currentStatusName) {
    const res = await fetch("<?= site_url('admin/applications/next-statuses/') ?>" + appId);
    const data = await res.json();

    const select = document.getElementById('statusSelect');
    select.innerHTML = '';

    if (currentStatusId) {
        select.innerHTML += `<option value="${currentStatusId}" selected>${currentStatusName} (Current)</option>`;
    } else {
        select.innerHTML += `<option value="">-- Select --</option>`;
    }

    if (!Array.isArray(data) || !data.length) {
        return;
    }

    data.forEach(function (row) {
        if (String(row.id) === String(currentStatusId)) {
            return;
        }

        const dayLabel = row.notification_days !== null && row.notification_days !== undefined
            ? ` (${row.notification_days} days)`
            : '';

        select.innerHTML += `<option value="${row.id}">${row.name}${dayLabel}</option>`;
    });
}

loadStatuses(
    <?= (int) $application['id'] ?>,
    <?= (int) ($application['status_id'] ?? 0) ?>,
    <?= json_encode((string) ($application['status_name'] ?? 'Current')) ?>
);
</script>

<?= $this->endSection() ?>