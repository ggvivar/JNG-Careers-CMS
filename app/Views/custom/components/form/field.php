<?php
$type        = $field['type'] ?? 'text';
$name        = $field['name'] ?? '';
$label       = $field['label'] ?? ucfirst($name);
$value       = old($name, $field['value'] ?? '');
$required    = !empty($field['required']);
$col         = $field['col'] ?? 'col-12';
$placeholder = $field['placeholder'] ?? ' ';
$help        = $field['help'] ?? '';
$options     = $field['options'] ?? [];
$rows        = $field['rows'] ?? 4;
$attributes  = $field['attributes'] ?? [];
$multiple    = !empty($field['multiple']);
$disabled    = !empty($field['disabled']);
$useSelect2  = !empty($field['select2']);
$useFloating = array_key_exists('floating', $field)
    ? (bool) $field['floating']
    : !in_array($type, ['checkbox', 'file']);
$error = $name ? validation_show_error($name) : '';

$attributes['id'] = $attributes['id'] ?? $name;
if ($multiple) {
    $attributes['multiple'] = true;
}
if ($disabled) {
    $attributes['disabled'] = true;
}

$view = match (true) {
    $type === 'textarea'          => 'custom/components/form/textarea',
    $type === 'checkbox'          => 'custom/components/form/checkbox',
    $type === 'file'              => 'custom/components/form/file',
    $type === 'select' && $useSelect2 => 'custom/components/form/select2',
    $type === 'select'            => 'custom/components/form/select',
    default                       => 'custom/components/form/input',
};
?>

<div class="<?= esc($col) ?>">
    <?= view($view, [
        'type' => $type,
        'name' => $name,
        'label' => $label,
        'value' => $value,
        'required' => $required,
        'placeholder' => $placeholder,
        'help' => $help,
        'options' => $options,
        'rows' => $rows,
        'attributes' => $attributes,
        'multiple' => $multiple,
        'disabled' => $disabled,
        'useFloating' => $useFloating,
        'error' => $error,
    ]) ?>
</div>