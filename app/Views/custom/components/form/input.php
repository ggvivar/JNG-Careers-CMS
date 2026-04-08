<?php
$name        = $name ?? '';
$type        = $type ?? 'text';
$label       = $label ?? ucfirst($name);
$value       = $value ?? '';
$required    = !empty($required);
$placeholder = $placeholder ?? ' ';
$attributes  = $attributes ?? [];
$error       = $error ?? '';
$useFloating = $useFloating ?? true;

$inputClass = 'form-control' . ($error ? ' is-invalid' : '');
?>

<div class="field-shell">
    <?php if ($useFloating): ?>
        <div class="form-floating">
            <input
                type="<?= esc($type) ?>"
                class="<?= esc($inputClass) ?>"
                name="<?= esc($name) ?>"
                value="<?= esc((string) $value) ?>"
                placeholder="<?= esc($placeholder) ?>"
                <?= $required ? 'required' : '' ?>
                <?= form_render_attributes($attributes) ?>
            >
            <label for="<?= esc($attributes['id'] ?? $name) ?>">
                <?= esc($label) ?>
                <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
            </label>
        </div>
    <?php else: ?>
        <label class="form-label" for="<?= esc($attributes['id'] ?? $name) ?>">
            <?= esc($label) ?>
            <?php if ($required): ?><span class="text-danger">*</span><?php endif; ?>
        </label>

        <input
            type="<?= esc($type) ?>"
            class="<?= esc($inputClass) ?>"
            name="<?= esc($name) ?>"
            value="<?= esc((string) $value) ?>"
            placeholder="<?= esc($placeholder) ?>"
            <?= $required ? 'required' : '' ?>
            <?= form_render_attributes($attributes) ?>
        >
    <?php endif; ?>

    <?php if (!empty($help ?? '')): ?>
        <div class="form-text mt-2"><?= esc($help) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="invalid-feedback"><?= esc($error) ?></div>
    <?php endif; ?>
</div>