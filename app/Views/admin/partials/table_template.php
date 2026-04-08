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

$filters           = $filters ?? [];
$currentFilters    = $currentFilters ?? [];
$sort              = $sort ?? '';
$dir               = strtolower($dir ?? 'desc') === 'asc' ? 'asc' : 'desc';

$canAdd    = $featureCode ? rbac_can_feature($featureCode, 'can_add') : true;
$canExport = $featureCode ? rbac_can_feature($featureCode, 'can_export') : true;
$canImport = $featureCode ? rbac_can_feature($featureCode, 'can_import') : true;

$createLabelClean = ltrim((string) $createLabel, '+ ');

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

if (! function_exists('table_sort_url')) {
    function table_sort_url(string $columnKey, string $currentUrl, string $currentSort, string $currentDir): string
    {
        $query = $_GET;
        $query['sort'] = $columnKey;
        $query['dir'] = ($currentSort === $columnKey && $currentDir === 'asc') ? 'desc' : 'asc';
        unset($query['page']);

        return $currentUrl . '?' . http_build_query($query);
    }
}

if (! function_exists('table_sort_icon')) {
    function table_sort_icon(string $columnKey, string $currentSort, string $currentDir): string
    {
        if ($currentSort !== $columnKey) {
            return 'bi-arrow-down-up';
        }

        return $currentDir === 'asc' ? 'bi-sort-down' : 'bi-sort-up';
    }
}
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h3 class="mb-1 fw-semibold"><?= esc($title) ?></h3>
        <?php if ($subtitle): ?>
            <div class="text-muted small"><?= esc($subtitle) ?></div>
        <?php endif; ?>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <?php if ($exportUrl && $canExport): ?>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#<?= esc($exportModalId) ?>"
            >
                <i class="bi bi-download me-1"></i>Export
            </button>
        <?php endif; ?>

        <?php if ($importUrl && $canImport): ?>
            <button
                type="button"
                class="btn btn-sm btn-outline-secondary"
                data-bs-toggle="modal"
                data-bs-target="#<?= esc($importModalId) ?>"
            >
                <i class="bi bi-upload me-1"></i>Import
            </button>
        <?php endif; ?>

        <?php if ($createUrl && $canAdd): ?>
            <a class="btn btn-sm btn-primary" href="<?= site_url($createUrl) ?>">
                <i class="bi bi-plus-lg me-1"></i><?= esc($createLabelClean) ?>
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-3 p-md-4">

        <form method="get" action="<?= esc($currentUrl) ?>" class="row g-2 align-items-end mb-3">
            <div class="col-12 col-md-6 col-lg-4">
                <label class="form-label small text-muted mb-1">Search</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input
                        type="text"
                        name="q"
                        class="form-control"
                        value="<?= esc($searchQuery) ?>"
                        placeholder="<?= esc($searchPlaceholder) ?>"
                    >
                </div>
            </div>

            <?php foreach ($filters as $filter): ?>
                <?php
                    $filterName = $filter['name'] ?? '';
                    $filterLabel = $filter['label'] ?? ucfirst($filterName);
                    $filterType = $filter['type'] ?? 'select';
                    $filterOptions = $filter['options'] ?? [];
                    $filterValue = $currentFilters[$filterName] ?? ($filter['value'] ?? '');
                    $filterClass = $filter['class'] ?? 'col-12 col-md-3 col-lg-2';
                ?>
                <div class="<?= esc($filterClass) ?>">
                    <label class="form-label small text-muted mb-1"><?= esc($filterLabel) ?></label>

                    <?php if ($filterType === 'select'): ?>
                        <select name="<?= esc($filterName) ?>" class="form-select form-select-sm">
                            <option value=""><?= esc($filter['placeholder'] ?? ('All ' . $filterLabel)) ?></option>
                            <?php foreach ($filterOptions as $optionValue => $optionLabel): ?>
                                <option value="<?= esc($optionValue) ?>" <?= (string) $optionValue === (string) $filterValue ? 'selected' : '' ?>>
                                    <?= esc($optionLabel) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php elseif ($filterType === 'checkbox'): ?>
                        <div class="form-check mt-2">
                            <input
                                class="form-check-input"
                                type="checkbox"
                                name="<?= esc($filterName) ?>"
                                value="1"
                                id="filter_<?= esc($filterName) ?>"
                                <?= !empty($filterValue) ? 'checked' : '' ?>
                            >
                            <label class="form-check-label" for="filter_<?= esc($filterName) ?>">
                                <?= esc($filter['checkbox_label'] ?? $filterLabel) ?>
                            </label>
                        </div>
                    <?php else: ?>
                        <input
                            type="text"
                            name="<?= esc($filterName) ?>"
                            class="form-control form-control-sm"
                            value="<?= esc((string) $filterValue) ?>"
                            placeholder="<?= esc($filter['placeholder'] ?? '') ?>"
                        >
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>

            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    Search
                </button>
            </div>

            <?php
                $hasFilters = $searchQuery !== '';
                foreach ($currentFilters as $v) {
                    if ((string) $v !== '' && $v !== null) {
                        $hasFilters = true;
                        break;
                    }
                }
            ?>

            <?php if ($hasFilters): ?>
                <div class="col-auto">
                    <a href="<?= esc($currentUrl) ?>" class="btn btn-sm btn-light border">
                        Reset
                    </a>
                </div>
            <?php endif; ?>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <?php foreach ($columns as $col): ?>
                            <?php
                                $isSortable = !empty($col['sortable']);
                                $sortKey = $col['sort_key'] ?? ($col['key'] ?? '');
                            ?>
                            <th
                                class="small text-uppercase text-muted fw-semibold"
                                <?= !empty($col['style']) ? 'style="' . esc($col['style']) . '"' : '' ?>
                            >
                                <?php if ($isSortable && $sortKey !== ''): ?>
                                    <a
                                        href="<?= esc(table_sort_url($sortKey, $currentUrl, $sort, $dir)) ?>"
                                        class="text-decoration-none text-muted"
                                    >
                                        <?= esc($col['label']) ?>
                                        <i class="bi <?= esc(table_sort_icon($sortKey, $sort, $dir)) ?>"></i>
                                    </a>
                                <?php else: ?>
                                    <?= esc($col['label']) ?>
                                <?php endif; ?>
                            </th>
                        <?php endforeach; ?>

                        <?php if (!empty($actions)): ?>
                            <th class="text-end small text-uppercase text-muted fw-semibold" style="width: 1%;">
                                Actions
                            </th>
                        <?php endif; ?>
                    </tr>
                </thead>

                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr>
                            <td colspan="<?= count($columns) + (!empty($actions) ? 1 : 0) ?>" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center text-muted">
                                    <i class="bi bi-inbox fs-1 mb-2 opacity-50"></i>
                                    <div class="fw-semibold mb-1">No records found</div>
                                    <div class="small">Try adjusting your search or filters.</div>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <?php foreach ($actions as $action): ?>
                                                <?php
                                                $perm = $action['permission'] ?? null;

                                                if ($featureCode && $perm && !rbac_can_feature($featureCode, $perm)) {
                                                    continue;
                                                }

                                                $method  = strtolower($action['method'] ?? 'get');
                                                $class   = $action['class'] ?? 'btn btn-outline-secondary';
                                                $icon    = $action['icon'] ?? 'bi-pencil';
                                                $label   = $action['label'] ?? 'Action';
                                                $confirm = $action['confirm'] ?? null;

                                                if (!empty($action['custom'])):
                                                    $dataAttr = '';
                                                    if (!empty($action['data']) && is_callable($action['data'])) {
                                                        $data = $action['data']($row);
                                                        foreach ($data as $k => $v) {
                                                            $dataAttr .= ' data-' . esc($k) . '="' . esc($v) . '"';
                                                        }
                                                    }
                                                ?>
                                                    <button
                                                        type="button"
                                                        class="<?= esc($class) ?>"
                                                        title="<?= esc($label) ?>"
                                                        <?= $dataAttr ?>
                                                    >
                                                        <i class="bi <?= esc($icon) ?>"></i>
                                                    </button>

                                                <?php else: ?>
                                                    <?php
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
                                                        <a href="<?= esc($url) ?>" class="<?= esc($class) ?>" title="<?= esc($label) ?>">
                                                            <i class="bi <?= esc($icon) ?>"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (!empty($paginationLinks)): ?>
            <div class="mt-3 pt-2 border-top">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="text-muted small">
                        <?= $hasFilters ? 'Filtered results' : 'Showing records' ?>
                    </div>
                    <div>
                        <?= $paginationLinks ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ($exportUrl && $canExport): ?>
    <?= view('admin/partials/export_modal', [
        'modalId'   => $exportModalId,
        'exportUrl' => $exportUrl,
        'entity'    => $exportEntity,
    ]) ?>
<?php endif; ?>

<?php if ($importUrl && $canImport): ?>
    <?= view('admin/partials/import_modal', [
        'modalId'   => $importModalId,
        'importUrl' => $importUrl,
        'entity'    => $importEntity,
    ]) ?>
<?php endif; ?>