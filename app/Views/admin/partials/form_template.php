<?php
$title       = $title ?? 'Form';
$subtitle    = $subtitle ?? '';
$backUrl     = $backUrl ?? '#';
$fields      = $fields ?? [];
$submitLabel = $submitLabel ?? 'Save';
?>

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">

  <div>
    <h3 class="mb-1"><?= esc($title) ?></h3>

    <?php if ($subtitle): ?>
      <div class="text-muted small"><?= esc($subtitle) ?></div>
    <?php endif; ?>
  </div>

  <a class="btn btn-sm btn-outline-secondary"
     href="<?= $backUrl === '#' ? '#' : site_url($backUrl) ?>">
     <i class="bi bi-arrow-left"></i> Back
  </a>

</div>


<div class="card">

<div class="card-body">

<form method="post" enctype="multipart/form-data">

<?= csrf_field() ?>

<div class="row g-3">

<?php foreach ($fields as $field): ?>

<?php
$type        = $field['type'] ?? 'text';
$name        = $field['name'] ?? '';
$label       = $field['label'] ?? ucfirst($name);
$value       = $field['value'] ?? '';
$required    = ! empty($field['required']);
$col         = $field['col'] ?? 'col-12';
$placeholder = $field['placeholder'] ?? '';
$help        = $field['help'] ?? '';
$options     = $field['options'] ?? [];
$rows        = $field['rows'] ?? 4;

if ($name !== '') {
    $value = old($name, $value);
}
?>

<div class="<?= esc($col) ?>">

<?php if ($type !== 'checkbox'): ?>

<label class="form-label small fw-semibold">
<?= esc($label) ?>

<?php if ($required): ?>
<span class="text-danger">*</span>
<?php endif; ?>

</label>

<?php endif; ?>


<?php if ($type === 'textarea'): ?>

<textarea
class="form-control form-control-sm"
name="<?= esc($name) ?>"
rows="<?= esc((string) $rows) ?>"
placeholder="<?= esc($placeholder) ?>"
<?= $required ? 'required' : '' ?>
><?= esc((string) $value) ?></textarea>


<?php elseif ($type === 'select'): ?>

<select
class="form-select form-select-sm"
name="<?= esc($name) ?>"
<?= $required ? 'required' : '' ?>
>

<option value="">Select...</option>

<?php foreach ($options as $optValue => $optLabel): ?>

<option
value="<?= esc((string) $optValue) ?>"
<?= (string)$optValue === (string)$value ? 'selected' : '' ?>
>

<?= esc((string)$optLabel) ?>

</option>

<?php endforeach; ?>

</select>


<?php elseif ($type === 'checkbox'): ?>

<div class="form-check mt-3">

<input
class="form-check-input"
type="checkbox"
name="<?= esc($name) ?>"
id="<?= esc($name) ?>"
value="1"
<?= ! empty($value) ? 'checked' : '' ?>
>

<label class="form-check-label small"
for="<?= esc($name) ?>">

<?= esc($label) ?>

</label>

</div>


<?php elseif ($type === 'file'): ?>

<input
type="file"
class="form-control form-control-sm"
name="<?= esc($name) ?>"
<?= $required ? 'required' : '' ?>
>


<?php else: ?>

<input
type="<?= esc($type) ?>"
class="form-control form-control-sm"
name="<?= esc($name) ?>"
value="<?= esc((string) $value) ?>"
placeholder="<?= esc($placeholder) ?>"
<?= $required ? 'required' : '' ?>
>

<?php endif; ?>


<?php if ($help): ?>

<div class="form-text small">
<?= esc($help) ?>
</div>

<?php endif; ?>

</div>

<?php endforeach; ?>

</div>


<div class="border-top mt-4 pt-3 d-flex gap-2">

<button class="btn btn-primary btn-sm" type="submit">
<i class="bi bi-check-lg"></i>
<?= esc($submitLabel) ?>
</button>

<a class="btn btn-outline-secondary btn-sm"
href="<?= $backUrl === '#' ? '#' : site_url($backUrl) ?>">

Cancel

</a>

</div>

</form>

</div>

</div>