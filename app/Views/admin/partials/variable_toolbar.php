variable_toolbar<?php
$variablesRaw = $variablesRaw ?? '';
$targetIds = $targetIds ?? [];
$title = $title ?? 'Available Variables';

$vars = [];

if (!empty($variablesRaw)) {
    $decoded = json_decode($variablesRaw, true);

    if (is_array($decoded)) {
        $vars = $decoded;
    } else {
        $vars = array_values(array_filter(array_map('trim', explode(',', $variablesRaw))));
    }
}

$vars = array_unique($vars);
?>

<?php if (!empty($vars)): ?>
  <div class="card shadow-sm mb-3">
    <div class="card-body">
      <div class="fw-semibold mb-2"><?= esc($title) ?></div>
      <div class="d-flex flex-wrap gap-2">
        <?php foreach ($vars as $var): ?>
          <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
              <?= esc($var) ?>
            </button>
            <ul class="dropdown-menu">
              <?php foreach ($targetIds as $target): ?>
                <li>
                  <button
                    type="button"
                    class="dropdown-item insert-variable-btn"
                    data-variable="<?= esc($var) ?>"
                    data-target="<?= esc($target['id']) ?>"
                    data-editor="<?= !empty($target['editor']) ? '1' : '0' ?>"
                  >
                    Insert into <?= esc($target['label']) ?>
                  </button>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
<?php endif; ?>