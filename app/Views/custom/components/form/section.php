<?php
$sectionIndex    = $sectionIndex ?? 0;
$section         = $section ?? [];
$sectionId       = $section['id'] ?? ('section-' . $sectionIndex);
$sectionTitle    = $section['title'] ?? 'Form Details';
$sectionSubtitle = $section['subtitle'] ?? 'Fill in the required information below';
$sectionFields   = $section['fields'] ?? [];
?>

<div class="form-section <?= $sectionIndex === 0 ? 'active' : '' ?>" id="<?= esc($sectionId) ?>">
    <div class="modern-card">
        <div class="section-header">
            <h3 class="section-title"><?= esc($sectionTitle) ?></h3>
            <?php if ($sectionSubtitle): ?>
                <div class="section-subtitle"><?= esc($sectionSubtitle) ?></div>
            <?php endif; ?>
        </div>

        <div class="card-body">
            <div class="row g-4">
                <?php foreach ($sectionFields as $field): ?>
                    <?= view('custom/components/form/field', ['field' => $field]) ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>