<?php
$modalId = $modalId ?? 'importModal';
$importUrl = $importUrl ?? '#';
$importHelp = $importHelp ?? '';
?>

<div class="modal fade" id="<?= esc($modalId) ?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="post" action="<?= site_url($importUrl) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="modal-header">
          <h5 class="modal-title">Import File</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Format</label>
            <select name="format" class="form-select" required>
              <option value="csv">CSV</option>
              <option value="xlsx">XLSX</option>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">File</label>
            <input type="file" name="import_file" class="form-control" required>
          </div>

          <?php if ($importHelp): ?>
            <div class="form-text">
              <?= esc($importHelp) ?>
            </div>
          <?php endif; ?>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Import</button>
        </div>
      </form>
    </div>
  </div>
</div>