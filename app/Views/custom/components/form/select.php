<?php
$name        = $name ?? '';
$label       = $label ?? ucfirst($name);
$value       = $value ?? '';
$required    = !empty($required);
$attributes  = $attributes ?? [];
$error       = $error ?? '';
$useFloating = $useFloating ?? true;
$options     = $options ?? [];
$multiple    = !empty($multiple);

$nameAttr = $multiple && !str_ends_with($name, '[]') ? $name . '[]' : $name;
$inputClass = 'form-select' . ($error ? ' is-invalid' : '');
?>

<div class="field-shell">
    <?php if ($useFloating): ?>
        <div class="form-floating">
            <select
                class="<?= esc($inputClass) ?>"
                name="<?= esc($nameAttr) ?>"
                <?= $required ? 'required' : '' ?>
                <?= form_render_attributes($attributes) ?>
            >
                <?php if (! $multiple): ?>
                    <option value="">Select...</option>
                <?php endif; ?>

                <?php foreach ($options as $optValue => $optLabel): ?>
                    <?php
                    $isSelected = $multiple && is_array($value)
                        ? in_array((string) $optValue, array_map('strval', $value), true)
                        : ((string) $optValue === (string) $value);
                    ?>
                    <option value="<?= esc((string) $optValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                        <?= esc((string) $optLabel) ?>
                    </option>
                <?php endforeach; ?>
            </select>

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

        <select
            class="<?= esc($inputClass) ?>"
            name="<?= esc($nameAttr) ?>"
            <?= $required ? 'required' : '' ?>
            <?= form_render_attributes($attributes) ?>
        >
            <?php if (! $multiple): ?>
                <option value="">Select...</option>
            <?php endif; ?>

            <?php foreach ($options as $optValue => $optLabel): ?>
                <?php
                $isSelected = $multiple && is_array($value)
                    ? in_array((string) $optValue, array_map('strval', $value), true)
                    : ((string) $optValue === (string) $value);
                ?>
                <option value="<?= esc((string) $optValue) ?>" <?= $isSelected ? 'selected' : '' ?>>
                    <?= esc((string) $optLabel) ?>
                </option>
            <?php endforeach; ?>
        </select>
    <?php endif; ?>

    <?php if (!empty($help ?? '')): ?>
        <div class="form-text mt-2"><?= esc($help) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="invalid-feedback"><?= esc($error) ?></div>
    <?php endif; ?>
</div>