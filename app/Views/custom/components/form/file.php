<?php
$name       = $name ?? '';
$label      = $label ?? ucfirst($name);
$required   = !empty($required);
$attributes = $attributes ?? [];
$error      = $error ?? '';

$inputClass = 'form-control' . ($error ? ' is-invalid' : '');
?>

<div class="field-shell">
    <label class="form-label" for="<?= esc($attributes['id'] ?? $name) ?>">
        <?= esc($label) ?>
        <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
    </label>

    <input
        type="file"
        class="<?= esc($inputClass) ?>"
        name="<?= esc($name) ?>"
        <?= $required ? 'required' : '' ?>
        <?= form_render_attributes($attributes) ?>
    >

    <?php if (!empty($help ?? '')): ?>
        <div class="form-text mt-2"><?= esc($help) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="invalid-feedback"><?= esc($error) ?></div>
    <?php endif; ?>
</div>