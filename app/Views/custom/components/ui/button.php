<?php
$type    = $type ?? 'button';
$label   = $label ?? 'Button';
$variant = $variant ?? 'primary';
$href    = $href ?? null;
$class   = $class ?? '';
$icon    = $icon ?? '';
?>

<?php if ($href): ?>
    <a href="<?= esc($href) ?>" class="btn btn-<?= esc($variant) ?> <?= esc($class) ?>">
        <?php if ($icon): ?><i class="<?= esc($icon) ?> me-1"></i><?php endif; ?>
        <?= esc($label) ?>
    </a>
<?php else: ?>
    <button type="<?= esc($type) ?>" class="btn btn-<?= esc($variant) ?> <?= esc($class) ?>">
        <?php if ($icon): ?><i class="<?= esc($icon) ?> me-1"></i><?php endif; ?>
        <?= esc($label) ?>
    </button>
<?php endif; ?>