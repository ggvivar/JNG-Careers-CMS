<?php
helper('rbac');

$title             = $title ?? 'List';
$subtitle          = $subtitle ?? '';
$featureCode       = $featureCode ?? null;
$rows              = $rows ?? [];
$columns           = $columns ?? [];
$actions           = $actions ?? [];
$createUrl         = $createUrl ?? null;
$createLabel       = $createLabel ?? '+ Create';
$searchQuery       = $searchQuery ?? '';
$searchPlaceholder = $searchPlaceholder ?? 'Search...';
$paginationLinks   = $paginationLinks ?? '';
$currentUrl        = current_url();

$exportUrl         = $exportUrl ?? null;
$importUrl         = $importUrl ?? null;
$importModalId     = $importModalId ?? 'importModal';
$importHelp        = $importHelp ?? '';
$exportFormats     = $exportFormats ?? ['csv', 'xlsx'];

$canAdd    = $featureCode ? rbac_can_feature($featureCode, 'can_add') : true;
$canExport = $featureCode ? rbac_can_feature($featureCode, 'can_export') : true;
$canImport = $featureCode ? rbac_can_feature($featureCode, 'can_import') : true;
?>

<div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-center mb-4 gap-3">

<div>

<h3 class="mb-1"><?= esc($title) ?></h3>

<?php if ($subtitle): ?>
<div class="text-muted small"><?= esc($subtitle) ?></div>
<?php endif; ?>

</div>


<div class="d-flex flex-wrap gap-2">

<?php if ($exportUrl && $canExport): ?>

<div class="dropdown">

<button class="btn btn-sm btn-outline-secondary dropdown-toggle"
data-bs-toggle="dropdown">

<i class="bi bi-download"></i> Export

</button>

<ul class="dropdown-menu dropdown-menu-end">

<?php foreach ($exportFormats as $fmt): ?>

<li>

<a class="dropdown-item"
href="<?= site_url($exportUrl . '?format=' . urlencode($fmt)) ?>">

<?= strtoupper(esc($fmt)) ?>

</a>

</li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>


<?php if ($importUrl && $canImport): ?>

<button class="btn btn-sm btn-outline-secondary"
data-bs-toggle="modal"
data-bs-target="#<?= esc($importModalId) ?>">

<i class="bi bi-upload"></i> Import

</button>

<?php endif; ?>


<?php if ($createUrl && $canAdd): ?>

<a class="btn btn-sm btn-primary"
href="<?= site_url($createUrl) ?>">

<i class="bi bi-plus-lg"></i>
<?= esc($createLabel) ?>

</a>

<?php endif; ?>

</div>

</div>


<div class="card">

<div class="card-body">


<form method="get"
action="<?= esc($currentUrl) ?>"
class="row g-2 mb-3">

<div class="col-12 col-md-5 col-lg-4">

<div class="input-group input-group-sm">

<span class="input-group-text">

<i class="bi bi-search"></i>

</span>

<input
type="text"
name="q"
class="form-control"
placeholder="<?= esc($searchPlaceholder) ?>"
value="<?= esc($searchQuery) ?>"
>

</div>

</div>


<div class="col-auto">

<button class="btn btn-sm btn-outline-secondary">

Search

</button>

</div>


<?php if ($searchQuery !== ''): ?>

<div class="col-auto">

<a class="btn btn-sm btn-outline-secondary"
href="<?= esc($currentUrl) ?>">

Reset

</a>

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


<?php if (! empty($actions)): ?>

<th class="text-end" style="width:200px">

Actions

</th>

<?php endif; ?>

</tr>

</thead>


<tbody>

<?php if (empty($rows)): ?>

<tr>

<td colspan="<?= count($columns) + (! empty($actions) ? 1 : 0) ?>"
class="text-center text-muted py-4">

No records found

</td>

</tr>

<?php endif; ?>


<?php foreach ($rows as $row): ?>

<tr>

<?php foreach ($columns as $col): ?>

<?php
$value = $row[$col['key']] ?? '';
$formatter = $col['formatter'] ?? null;
$cellValue = is_callable($formatter)
? $formatter($row, $value)
: esc((string)$value);
?>

<td>

<?= $cellValue ?>

</td>

<?php endforeach; ?>


<?php if (! empty($actions)): ?>

<td class="text-end">

<div class="btn-group btn-group-sm">

<?php foreach ($actions as $action): ?>

<?php

$perm = $action['permission'] ?? null;

if ($featureCode && $perm && ! rbac_can_feature($featureCode, $perm)) {
continue;
}

$label   = $action['label'] ?? 'Action';
$class   = $action['class'] ?? 'btn btn-outline-secondary';
$method  = strtolower($action['method'] ?? 'get');
$confirm = $action['confirm'] ?? null;

$icon = $action['icon'] ?? 'bi-pencil';

if (! empty($action['url_builder']) && is_callable($action['url_builder'])) {
$url = site_url($action['url_builder']($row));
} else {
$url = site_url(($action['url'] ?? '') . ($row['id'] ?? ''));
}

?>

<?php if ($method === 'post'): ?>

<form method="post"
action="<?= esc($url) ?>"
class="d-inline">

<?= csrf_field() ?>

<button
type="submit"
class="<?= esc($class) ?>"
<?= $confirm ? 'onclick="return confirm(\'' . esc($confirm, 'js') . '\')"' : '' ?>
>

<i class="bi <?= esc($icon) ?>"></i>

</button>

</form>

<?php else: ?>

<a href="<?= esc($url) ?>"
class="<?= esc($class) ?>">

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


<?php if (! empty($paginationLinks)): ?>

<div class="mt-3 d-flex justify-content-end">

<?= $paginationLinks ?>

</div>

<?php endif; ?>


</div>

</div>


<?php if ($importUrl && $canImport): ?>

<?= view('admin/partials/import_modal', [
'modalId' => $importModalId,
'importUrl' => $importUrl,
'importHelp' => $importHelp,
]) ?>

<?php endif; ?>