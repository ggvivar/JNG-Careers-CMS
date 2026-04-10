<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0">
      <?= esc(trim(($application['firstname'] ?? '') . ' ' . ($application['lastname'] ?? ''))) ?>
      - <?= esc($application['job_name'] ?? '-') ?>
    </h3>
    <div class="text-muted">Application #<?= esc($application['id']) ?></div>
  </div>
  <a class="btn btn-outline-secondary" href="<?= site_url('admin/applications') ?>">Back</a>
</div>

<div class="row g-3">

  <!-- APPLICATION DETAILS -->
  <div class="col-12 col-lg-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5>Application Details</h5>
        <p><strong>Job:</strong> <?= esc($application['job_name'] ?? '-') ?></p>
        <p><strong>Email:</strong> <?= esc($application['email'] ?? '-') ?></p>
        <p><strong>Phone:</strong> <?= esc($application['phone'] ?? '-') ?></p>
        <p><strong>Source:</strong> <?= esc($application['source'] ?? '-') ?></p>
        <p><strong>Status:</strong> <?= esc($application['status_name'] ?? '-') ?></p>
        <p><strong>Processor:</strong> <?= esc($application['processor_name'] ?? 'Unassigned') ?></p>
        <p><strong>Due At:</strong> <?= esc($application['due_at'] ?? '-') ?></p>
      </div>
    </div>
  </div>

  <!-- UPDATE WORKFLOW -->
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
            </div>

            <div class="col-md-4">
              <label class="form-label">Processor</label>
              <select name="assignedUserId" class="form-select">
                <option value="">Unassigned</option>
                <?php foreach (($processorOptions ?? []) as $id => $label): ?>
                  <option value="<?= esc($id) ?>" <?= (string)$id === (string)($application['assigned_to'] ?? '') ? 'selected' : '' ?>>
                    <?= esc($label) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>

            <div class="col-md-12">
              <label class="form-label">Remarks</label>
              <textarea name="remarks" class="form-control"></textarea>
            </div>
          </div>

          <div class="mt-3">
            <button class="btn btn-primary" type="submit">Update Workflow</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- AUDIT TRAIL -->
  <div class="col-12">
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="d-flex align-items-center justify-content-between px-4 pt-4 pb-3 border-bottom">
        <div>
          <h5 class="mb-1">Workflow Timeline</h5>
          <div class="text-muted small">Track status changes, processor updates, and remarks</div>
        </div>
      </div>

      <?php if (empty($history)): ?>
        <div class="p-4 text-muted">No workflow history yet.</div>
      <?php else: ?>
        <div class="workflow-timeline p-4">
          <?php foreach ($history as $index => $row): ?>
            <?php
              $remarks = trim((string) ($row['remarks'] ?? ''));
              $collapseId = 'timelineCollapse' . (int) ($row['id'] ?? $index);
              $fromStatus = trim((string) ($row['from_status_name'] ?? ''));
              $toStatus = trim((string) ($row['to_status_name'] ?? ''));
              $assignedFrom = trim((string) ($row['assigned_from_name'] ?? ''));
              $assignedTo = trim((string) ($row['assigned_to_name'] ?? ''));
              $changedBy = trim((string) ($row['changed_by_name'] ?? ''));
              $dateCreated = trim((string) ($row['date_created'] ?? '-'));
              $dueAt = trim((string) ($row['due_at'] ?? ''));
              $isLast = $index === array_key_last($history);

              $title = 'Workflow updated';
              if ($fromStatus !== '' && $toStatus !== '' && $fromStatus !== $toStatus) {
                  $title = $fromStatus . ' → ' . $toStatus;
              } elseif ($toStatus !== '') {
                  $title = $toStatus;
              }

              $hasProcessorChange = $assignedFrom !== $assignedTo && ($assignedFrom !== '' || $assignedTo !== '');
              $isOpen = $index === 0;
            ?>

            <div class="timeline-item <?= $isLast ? 'last' : '' ?>">
              <div class="timeline-line"></div>
              <?php
                $icon = 'bi-clock-history'; // default

                // Created
                if (stripos($remarks ?? '', 'created') !== false) {
                    $icon = 'bi-plus-circle';
                }
                // Status change
                elseif (($fromStatus ?? '') !== ($toStatus ?? '') && !empty($toStatus)) {
                    $icon = 'bi-arrow-right-circle';
                }
                // Reassignment
                elseif (($assignedFrom ?? '') !== ($assignedTo ?? '') && (!empty($assignedFrom) || !empty($assignedTo))) {
                    $icon = 'bi-person-gear';
                }
                // Remarks (fallback if only remarks exist)
                elseif (!empty($remarks)) {
                    $icon = 'bi-chat-left-text';
                }
                ?>
                <div class="timeline-dot">
                  <i class="bi <?= esc($icon) ?>"></i>
                </div>

              <div class="timeline-card card border-0 shadow-sm">
                <button
                  class="timeline-toggle btn btn-link text-decoration-none text-start w-100 <?= $isOpen ? '' : 'collapsed' ?>"
                  type="button"
                  data-bs-toggle="collapse"
                  data-bs-target="#<?= esc($collapseId) ?>"
                  aria-expanded="<?= $isOpen ? 'true' : 'false' ?>"
                  aria-controls="<?= esc($collapseId) ?>"
                >
                  <div class="card-body p-3 p-lg-4">
                    <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between gap-3">
                      <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                          <h6 class="mb-0 fw-semibold text-dark"><?= esc($title) ?></h6>
                        </div>

                        <div class="timeline-meta text-muted small">
                          <span class="me-3">
                            <i class="bi bi-clock me-1"></i><?= esc($dateCreated) ?>
                          </span>
                          <span class="me-3">
                            <i class="bi bi-person me-1"></i><?= esc($changedBy !== '' ? $changedBy : '-') ?>
                          </span>
                          <?php if ($hasProcessorChange): ?>
                            <span>
                              <i class="bi bi-person-gear me-1"></i>
                              <?= esc($assignedFrom !== '' ? $assignedFrom : 'Unassigned') ?>
                              →
                              <?= esc($assignedTo !== '' ? $assignedTo : 'Unassigned') ?>
                            </span>
                          <?php endif; ?>
                        </div>
                      </div>

                      <div class="timeline-toggle-icon">
                        <i class="bi bi-chevron-down"></i>
                      </div>
                    </div>
                  </div>
                </button>

                <div id="<?= esc($collapseId) ?>" class="collapse <?= $isOpen ? 'show' : '' ?>">
                  <div class="px-3 px-lg-4 pb-4">
                    <div class="row g-3">
                      <div class="col-12 col-lg-6">
                        <div class="timeline-info-box">
                          <div class="timeline-label">Status Change</div>
                          <div class="timeline-value">
                            <span class="text-muted"><?= esc($fromStatus !== '' ? $fromStatus : '-') ?></span>
                            <i class="bi bi-arrow-right mx-2 text-muted"></i>
                            <span class="fw-semibold"><?= esc($toStatus !== '' ? $toStatus : '-') ?></span>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-lg-6">
                        <div class="timeline-info-box">
                          <div class="timeline-label">Processor</div>
                          <div class="timeline-value">
                            <?php if ($hasProcessorChange): ?>
                              <span class="text-muted"><?= esc($assignedFrom !== '' ? $assignedFrom : 'Unassigned') ?></span>
                              <i class="bi bi-arrow-right mx-2 text-muted"></i>
                              <span class="fw-semibold"><?= esc($assignedTo !== '' ? $assignedTo : 'Unassigned') ?></span>
                            <?php else: ?>
                              <span class="fw-semibold"><?= esc($assignedTo !== '' ? $assignedTo : ($assignedFrom !== '' ? $assignedFrom : 'Unassigned')) ?></span>
                            <?php endif; ?>
                          </div>
                        </div>
                      </div>

                      <div class="col-12 col-lg-6">
                        <div class="timeline-info-box">
                          <div class="timeline-label">Changed By</div>
                          <div class="timeline-value"><?= esc($changedBy !== '' ? $changedBy : '-') ?></div>
                        </div>
                      </div>

                      <div class="col-12 col-lg-6">
                        <div class="timeline-info-box">
                          <div class="timeline-label">Date</div>
                          <div class="timeline-value"><?= esc($dateCreated) ?></div>
                        </div>
                      </div>

                      <?php if ($dueAt !== ''): ?>
                        <div class="col-12 col-lg-6">
                          <div class="timeline-info-box">
                            <div class="timeline-label">Due At</div>
                            <div class="timeline-value"><?= esc($dueAt) ?></div>
                          </div>
                        </div>
                      <?php endif; ?>

                      <div class="col-12">
                        <div class="timeline-info-box">
                          <div class="timeline-label">Remarks</div>

                          <?php
                            $remarksId = 'remarksText' . (int) ($row['id'] ?? $index);
                            $btnId = 'remarksBtn' . (int) ($row['id'] ?? $index);
                            $remarksText = trim((string) ($row['remarks'] ?? ''));
                            $remarksShort = mb_strlen($remarksText) > 140 ? mb_substr($remarksText, 0, 140) . '...' : $remarksText;
                            $hasLongRemarks = mb_strlen($remarksText) > 140;
                          ?>

                          <?php if ($remarksText !== ''): ?>
                            <div class="timeline-remarks-compact">
                              <div
                                id="<?= esc($remarksId) ?>"
                                class="timeline-remarks-text"
                                data-full="<?= esc($remarksText, 'attr') ?>"
                                data-short="<?= esc($remarksShort, 'attr') ?>"
                                data-expanded="0"
                              ><?= nl2br(esc($remarksShort)) ?></div>

                              <?php if ($hasLongRemarks): ?>
                                <button
                                  type="button"
                                  id="<?= esc($btnId) ?>"
                                  class="btn btn-link btn-sm p-0 mt-2 timeline-remarks-toggle"
                                  onclick="toggleRemarks('<?= esc($remarksId) ?>', '<?= esc($btnId) ?>')"
                                >
                                  Show more
                                </button>
                              <?php endif; ?>
                            </div>
                          <?php else: ?>
                            <div class="timeline-remarks-text text-muted">No remarks</div>
                          <?php endif; ?>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

</div>

<!-- STATUS SCRIPT -->
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
        if (String(row.id) === String(currentStatusId)) return;

        const dayLabel = row.grace_period !== null && row.grace_period !== undefined
            ? ` (${row.grace_period} days)`
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
<script>
function toggleRemarks(textId, btnId) {
    const textEl = document.getElementById(textId);
    const btnEl = document.getElementById(btnId);

    if (!textEl || !btnEl) return;

    const isExpanded = textEl.getAttribute('data-expanded') === '1';
    const fullText = textEl.getAttribute('data-full') || '';
    const shortText = textEl.getAttribute('data-short') || '';

    if (isExpanded) {
        textEl.innerHTML = shortText.replace(/\n/g, '<br>');
        textEl.setAttribute('data-expanded', '0');
        btnEl.textContent = 'Show more';
    } else {
        textEl.innerHTML = fullText.replace(/\n/g, '<br>');
        textEl.setAttribute('data-expanded', '1');
        btnEl.textContent = 'Show less';
    }
}
</script>
<style>
.workflow-timeline{
  position:relative;
}

.timeline-item{
  position:relative;
  padding-left:56px;
  padding-bottom:22px;
}

.timeline-item.last{
  padding-bottom:0;
}

.timeline-line{
  position:absolute;
  left:20px;
  top:36px;
  bottom:-8px;
  width:2px;
  background:linear-gradient(180deg, #dbe4f0 0%, #eef2f7 100%);
}

.timeline-item.last .timeline-line{
  display:none;
}

.timeline-dot{
  position:absolute;
  left:0;
  top:6px;
  width:40px;
  height:40px;
  border-radius:999px;
  background:#fff;
  border:2px solid #0d6efd;
  color:#0d6efd;
  display:flex;
  align-items:center;
  justify-content:center;
  box-shadow:0 0 0 4px #f8f9fa;
  z-index:2;
}

.timeline-card{
  border-radius:18px;
  overflow:hidden;
  background:#fff;
}

.timeline-toggle{
  padding:0;
  color:inherit;
  border-radius:0;
}

.timeline-toggle:hover,
.timeline-toggle:focus{
  color:inherit;
  background:#f8fafc;
}

.timeline-toggle-icon{
  font-size:1rem;
  color:#6c757d;
  transition:transform .2s ease;
}

.timeline-toggle[aria-expanded="true"] .timeline-toggle-icon{
  transform:rotate(180deg);
}

.timeline-meta{
  display:flex;
  flex-wrap:wrap;
  gap:8px 0;
}

.timeline-info-box{
  background:#f8f9fb;
  border:1px solid #edf1f5;
  border-radius:14px;
  padding:12px 14px;
  height:100%;
}

.timeline-label{
  font-size:.75rem;
  font-weight:600;
  color:#6c757d;
  text-transform:uppercase;
  letter-spacing:.04em;
  margin-bottom:6px;
}

.timeline-value{
  color:#212529;
  line-height:1.5;
  word-break:break-word;
}

.timeline-remarks-text{
  white-space:pre-wrap;
  line-height:1.7;
  color:#212529;
}
.timeline-dot i{
  font-size: 1.05rem;
}
@media (max-width: 991.98px){
  .timeline-item{
    padding-left:52px;
  }

  .timeline-dot{
    width:36px;
    height:36px;
    left:2px;
  }

  .timeline-line{
    left:19px;
  }
}
</style>  
<style>
.timeline-remarks-compact{
  max-width: 100%;
}

.timeline-remarks-text{
  white-space: pre-wrap;
  line-height: 1.6;
  color: #212529;
  word-break: break-word;
  font-size: .95rem;
}

.timeline-remarks-toggle{
  font-size: .85rem;
  font-weight: 600;
}
</style>
<?= $this->endSection() ?>