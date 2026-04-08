<?php
$name        = $name ?? '';
$label       = $label ?? ucfirst($name);
$value       = $value ?? '';
$required    = !empty($required);
$placeholder = $placeholder ?? ' ';
$attributes  = $attributes ?? [];
$error       = $error ?? '';
$useFloating = $useFloating ?? true;
$rows        = $rows ?? 4;

$inputClass = 'form-control' . ($error ? ' is-invalid' : '');
?>

<div class="field-shell">
    <?php if ($useFloating): ?>
        <div class="form-floating">
            <textarea
                class="<?= esc($inputClass) ?>"
                name="<?= esc($name) ?>"
                rows="<?= esc((string) $rows) ?>"
                placeholder="<?= esc($placeholder) ?>"
                <?= $required ? 'required' : '' ?>
                <?= form_render_attributes($attributes) ?>
            ><?= esc((string) $value) ?></textarea>

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

        <textarea
            class="<?= esc($inputClass) ?>"
            name="<?= esc($name) ?>"
            rows="<?= esc((string) $rows) ?>"
            placeholder="<?= esc($placeholder) ?>"
            <?= $required ? 'required' : '' ?>
            <?= form_render_attributes($attributes) ?>
        ><?= esc((string) $value) ?></textarea>
    <?php endif; ?>

    <?php if (!empty($help ?? '')): ?>
        <div class="form-text mt-2"><?= esc($help) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="invalid-feedback"><?= esc($error) ?></div>
    <?php endif; ?>
</div>