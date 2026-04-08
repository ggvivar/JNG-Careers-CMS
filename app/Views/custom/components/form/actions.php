<?php
$backUrl     = $backUrl ?? '#';
$submitLabel = $submitLabel ?? 'Save';
?>

<div class="sticky-actions">
    <div class="sticky-actions-inner">
        <?= view('custom/components/ui/button', [
            'href' => $backUrl === '#' ? '#' : site_url($backUrl),
            'label' => 'Cancel',
            'variant' => 'outline-secondary',
        ]) ?>

        <?= view('custom/components/ui/button', [
            'type' => 'submit',
            'label' => $submitLabel,
            'variant' => 'primary',
            'class' => 'px-4',
            'icon' => 'bi bi-check-lg',
        ]) ?>
    </div>
</div>