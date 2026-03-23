<?php
helper('rbac');

$title             = $title ?? 'List';
$subtitle          = $subtitle ?? '';
$featureCode       = $featureCode ?? null;
$rows              = $rows ?? [];
$columns           = $columns ?? [];
$actions           = $actions ?? [];
$createUrl         = $createUrl ?? null;
$createLabel       = $createLabel ?? 'Create';
$searchQuery       = $searchQuery ?? '';
$searchPlaceholder = $searchPlaceholder ?? 'Search...';
$paginationLinks   = $paginationLinks ?? '';
$currentUrl        = current_url();

$exportUrl         = $exportUrl ?? null;
$importUrl         = $importUrl ?? null;
$importModalId     = $importModalId ?? 'importModal';
$exportModalId     = $exportModalId ?? 'exportModal';
$exportFormats     = $exportFormats ?? ['csv', 'xlsx'];

$canAdd    = $featureCode ? rbac_can_feature($featureCode, 'can_add') : true;
$canExport = $featureCode ? rbac_can_feature($featureCode, 'can_export') : true;
$canImport = $featureCode ? rbac_can_feature($featureCode, 'can_import') : true;

$createLabelClean = ltrim((string) $createLabel, "+ ");

$exportEntity = null;
if ($exportUrl) {
    $parts = explode('/', trim($exportUrl, '/'));
    $exportEntity = end($parts);
}

$importEntity = null;
if ($importUrl) {
    $parts = explode('/', trim($importUrl, '/'));
    $importEntity = end($parts);
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1"><?= esc($title) ?></h3>
        <?php if ($subtitle): ?>
            <div class="text-muted small"><?= esc($subtitle) ?></div>
        <?php endif; ?>
    </div>

    <div class="d-flex gap-2">
        <?php if ($exportUrl && $canExport): ?>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#<?= esc($exportModalId) ?>"
            >
                <i class="bi bi-download me-1"></i> Export
            </button>
        <?php endif; ?>

        <?php if ($importUrl && $canImport): ?>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#<?= esc($importModalId) ?>"
            >
                <i class="bi bi-upload me-1"></i> Import
            </button>
        <?php endif; ?>

        <?php if ($createUrl && $canAdd): ?>
            <a class="btn btn-sm btn-primary" href="<?= site_url($createUrl) ?>">
                <i class="bi bi-plus-lg me-1"></i><?= esc($createLabelClean) ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">

        <form method="get" action="<?= esc($currentUrl) ?>" class="row g-2 mb-3">
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        value="<?= esc($searchQuery) ?>"
                        placeholder="<?= esc($searchPlaceholder) ?>"
                    >
                </div>
            </div>

            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-outline-secondary">Search</button>
            </div>

            <?php if ($searchQuery): ?>
                <div class="col-auto">
                    <a href="<?= esc($currentUrl) ?>" class="btn btn-sm btn-outline-secondary">Reset</a>
                </div>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light small">
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <th <?= !empty($col['style']) ? 'style="' . esc($col['style']) . '"' : '' ?>>
                                <?= esc($col['label']) ?>
                            </th>
                        <?php endforeach; ?>

                        <?php if (!empty($actions)): ?>
                            <th class="text-end">Actions</th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= count($columns) + (!empty($actions) ? 1 : 0) ?>" class="text-center text-muted py-4">
                                No records found
                            </td>
                        </tr>
                    <?php endif; ?>

                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <?php foreach ($columns as $col): ?>
                                <td>
                                    <?= is_callable($col['formatter'] ?? null)
                                        ? $col['formatter']($row, $row[$col['key']] ?? '')
                                        : esc($row[$col['key']] ?? '') ?>
                                </td>
                            <?php endforeach; ?>

                            <?php if (!empty($actions)): ?>
                                <td class="text-end">
                                    <div class="d-flex gap-1 justify-content-end flex-wrap">
                                        <?php foreach ($actions as $action): ?>
                                            <?php
                                            $perm = $action['permission'] ?? null;

                                            if ($featureCode && $perm && !rbac_can_feature($featureCode, $perm)) {
                                                continue;
                                            }

                                            $method  = strtolower($action['method'] ?? 'get');
                                            $class   = $action['class'] ?? 'btn btn-sm btn-outline-secondary';
                                            $icon    = $action['icon'] ?? 'bi-pencil';
                                            $label   = $action['label'] ?? 'Action';
                                            $confirm = $action['confirm'] ?? null;

                                            if (!empty($action['url_builder']) && is_callable($action['url_builder'])) {
                                                $url = site_url($action['url_builder']($row));
                                            } else {
                                                $url = site_url(($action['url'] ?? '') . ($row['id'] ?? ''));
                                            }
                                            ?>

                                            <?php if ($method === 'post'): ?>
                                                <form method="post" action="<?= esc($url) ?>" class="d-inline">
                                                    <?= csrf_field() ?>
                                                    <button
                                                        type="submit"
                                                        class="<?= esc($class) ?>"
                                                        title="<?= esc($label) ?>"
                                                        <?= $confirm ? 'onclick="return confirm(\'' . esc($confirm, 'js') . '\')"' : '' ?>
                                                    >
                                                        <i class="bi <?= esc($icon) ?>"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <a
                                                    href="<?= esc($url) ?>"
                                                    class="<?= esc($class) ?>"
                                                    title="<?= esc($label) ?>"
                                                >
                                                    <i class="bi <?= esc($icon) ?>"></i>
                                                </a>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($paginationLinks)): ?>
            <div class="mt-3 d-flex justify-content-end">
                <?= $paginationLinks ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($exportUrl && $canExport): ?>
    <?= view('admin/partials/export_modal', [
        'modalId' => $exportModalId,
        'exportUrl' => $exportUrl,
        'entity' => $exportEntity,
    ]) ?>
<?php endif; ?>

<?php if ($importUrl && $canImport): ?>
    <?= view('admin/partials/import_modal', [
        'modalId' => $importModalId,
        'importUrl' => $importUrl,
        'entity' => $importEntity,
    ]) ?>
<?php endif; ?>