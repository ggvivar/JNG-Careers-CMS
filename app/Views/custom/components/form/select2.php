<?php
$name       = $name ?? '';
$label      = $label ?? ucfirst($name);
$value      = $value ?? '';
$required   = !empty($required);
$attributes = $attributes ?? [];
$error      = $error ?? '';
$options    = $options ?? [];
$multiple   = !empty($multiple);

$nameAttr = $multiple && !str_ends_with($name, '[]') ? $name . '[]' : $name;
$hasValue = $multiple
    ? (!empty($value) && is_array($value))
    : ((string) $value !== '');

$inputClass = 'form-select js-select2' . ($error ? ' is-invalid' : '');
?>

<div class="field-shell">
    <div class="select2-float <?= $hasValue ? 'active' : '' ?> <?= $required ? 'required' : '' ?>">
        <label class="select2-float-label" for="<?= esc($attributes['id'] ?? $name) ?>">
            <?= esc($label) ?>
        </label>

        <select
            class="<?= esc($inputClass) ?>"
            name="<?= esc($nameAttr) ?>"
            <?= $required ? 'required' : '' ?>
            <?= form_render_attributes($attributes) ?>
        >
            <?php if (! $multiple): ?>
                <option value=""></option>
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
    </div>

    <?php if (!empty($help ?? '')): ?>
        <div class="form-text mt-2"><?= esc($help) ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="invalid-feedback"><?= esc($error) ?></div>
    <?php endif; ?>
</div>