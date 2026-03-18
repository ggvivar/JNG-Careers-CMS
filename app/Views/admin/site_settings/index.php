<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex align-items-center justify-content-between mb-3">
  <div>
    <h3 class="mb-0">Site Settings</h3>
    <div class="text-muted">Manage application settings by language</div>
  </div>
</div>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="get" class="row g-2 mb-3">
      <div class="col-12 col-lg-3">
        <label class="form-label">Language</label>
        <select name="language_code" class="form-select" onchange="this.form.submit()">
          <?php foreach (($languageOptions ?? []) as $lang): ?>
            <option value="<?= esc($lang) ?>" <?= ($selectedLanguage ?? 'en') === $lang ? 'selected' : '' ?>>
              <?= esc(strtoupper($lang)) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
    </form>

    <form method="post" action="<?= site_url('admin/site-settings/save') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="language_code" value="<?= esc($selectedLanguage ?? 'en') ?>">

      <div class="table-responsive">
        <table class="table align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:220px;">Key</th>
              <th>Value</th>
              <th style="width:120px;">Type</th>
              <th style="width:120px;">Autoload</th>
              <th>Description</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($settings)): ?>
              <tr>
                <td colspan="5" class="text-center text-muted py-4">No settings found.</td>
              </tr>
            <?php endif; ?>

            <?php foreach (($settings ?? []) as $i => $setting): ?>
              <tr>
                <td>
                  <input type="hidden" name="rows[<?= $i ?>][id]" value="<?= esc($setting['id']) ?>">
                  <input type="hidden" name="rows[<?= $i ?>][setting_key]" value="<?= esc($setting['setting_key']) ?>">
                  <div class="fw-semibold"><?= esc($setting['setting_key']) ?></div>
                </td>

                <td>
                  <?php if (($setting['setting_type'] ?? 'string') === 'boolean'): ?>
                    <select name="rows[<?= $i ?>][setting_value]" class="form-select">
                      <option value="1" <?= (string) ($setting['setting_value'] ?? '') === '1' ? 'selected' : '' ?>>True</option>
                      <option value="0" <?= (string) ($setting['setting_value'] ?? '') === '0' ? 'selected' : '' ?>>False</option>
                    </select>
                  <?php elseif (($setting['setting_type'] ?? 'string') === 'json'): ?>
                    <textarea name="rows[<?= $i ?>][setting_value]" class="form-control" rows="3"><?= esc($setting['setting_value'] ?? '') ?></textarea>
                  <?php else: ?>
                    <input type="text" name="rows[<?= $i ?>][setting_value]" class="form-control" value="<?= esc($setting['setting_value'] ?? '') ?>">
                  <?php endif; ?>
                </td>

                <td>
                  <select name="rows[<?= $i ?>][setting_type]" class="form-select">
                    <?php foreach (['string', 'boolean', 'json'] as $type): ?>
                      <option value="<?= esc($type) ?>" <?= ($setting['setting_type'] ?? 'string') === $type ? 'selected' : '' ?>>
                        <?= esc($type) ?>
                      </option>
                    <?php endforeach; ?>
                  </select>
                </td>

                <td>
                  <select name="rows[<?= $i ?>][autoload]" class="form-select">
                    <option value="1" <?= (string) ($setting['autoload'] ?? 1) === '1' ? 'selected' : '' ?>>Yes</option>
                    <option value="0" <?= (string) ($setting['autoload'] ?? 1) === '0' ? 'selected' : '' ?>>No</option>
                  </select>
                </td>

                <td>
                  <input type="text" name="rows[<?= $i ?>][description]" class="form-control" value="<?= esc($setting['description'] ?? '') ?>">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="mt-3">
        <button class="btn btn-dark" type="submit">Save Settings</button>
      </div>
    </form>
  </div>
</div>

<?= $this->endSection() ?>