<?php
$name       = $name ?? '';
$label      = $label ?? ucfirst($name);
$value      = $value ?? '';
$required   = !empty($required);
$attributes = $attributes ?? [];
$error      = $error ?? '';
?>

<div class="field-shell is-checkbox">
    <div class="form-check">
        <input
            class="form-check-input<?= $error ? ' is-invalid' : '' ?>"
            type="checkbox"
            name="<?= esc($name) ?>"
            value="1"
            <?= !empty($value) ? 'checked' : '' ?>
            <?= form_render_attributes($attributes) ?>
        >

        <label class="form-check-label" for="<?= esc($attributes['id'] ?? $name) ?>">
            <?= esc($label) ?>
            <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
        </label>
    </div>

    <?php if (!empty($help ?? '')): ?>
        <div class="form-text mt-2"><?= esc($help) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="invalid-feedback"><?= esc($error) ?></div>
    <?php endif; ?>
</div>