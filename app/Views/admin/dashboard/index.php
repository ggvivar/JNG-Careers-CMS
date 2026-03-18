<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h3 class="mb-0">Dashboard</h3>
    <div class="text-muted small">Overview of your Career CMS</div>
  </div>
</div>


<!-- STATS -->

<div class="row g-3 mb-4">

<?php
$icons = [
    'users' => 'bi-people',
    'roles' => 'bi-shield-lock',
    'jobs' => 'bi-briefcase',
    'job_posts' => 'bi-file-earmark-text',
    'applications' => 'bi-send',
    'contents' => 'bi-file-richtext'
];
?>

<?php foreach (($stats ?? []) as $label => $value): ?>

<div class="col-12 col-sm-6 col-xl-3">

<div class="dashboard-card">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="text-muted small text-uppercase">
<?= esc(str_replace('_',' ',$label)) ?>
</div>

<div class="fs-3 fw-semibold">
<?= esc((string)$value) ?>
</div>

</div>

<div class="dashboard-icon">

<i class="bi <?= $icons[$label] ?? 'bi-bar-chart' ?>"></i>

</div>

</div>

</div>

</div>

<?php endforeach; ?>

</div>
<!-- NEW APPLICATION and FOR APPROVAL-->

<div class="row g-3 mb-4">
<h5>For Review</h5>
<?php
$icons = [
    'applications' => 'bi-send',
    'contents' => 'bi-file-richtext'
];
?>

<?php foreach (($stats_for_approval ?? []) as $label => $value): ?>

<div class="col-12 col-sm-6 col-xl-3">

<div class="dashboard-card">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="text-muted small text-uppercase">
<?= esc(str_replace('_',' ',$label)) ?>
</div>

<div class="fs-3 fw-semibold">
<?= esc((string)$value) ?>
</div>

</div>

<div class="dashboard-icon">

<i class="bi <?= $icons[$label] ?? 'bi-bar-chart' ?>"></i>

</div>

</div>

</div>

</div>

<?php endforeach; ?>

</div>



<!-- QUICK LINKS -->

<div class="card">

<div class="card-body">

<h5 class="mb-3">Quick Links</h5>

<div class="row g-2">

<div class="col-6 col-md-4 col-lg-2">
<a href="<?= site_url('admin/users') ?>" class="btn btn-outline-primary w-100">
<i class="bi bi-people"></i><br>
<span class="small">Users</span>
</a>
</div>

<div class="col-6 col-md-4 col-lg-2">
<a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-primary w-100">
<i class="bi bi-shield-lock"></i><br>
<span class="small">Roles</span>
</a>
</div>

<div class="col-6 col-md-4 col-lg-2">
<a href="<?= site_url('admin/jobs') ?>" class="btn btn-outline-primary w-100">
<i class="bi bi-briefcase"></i><br>
<span class="small">Jobs</span>
</a>
</div>

<div class="col-6 col-md-4 col-lg-2">
<a href="<?= site_url('admin/job-posts') ?>" class="btn btn-outline-primary w-100">
<i class="bi bi-file-earmark-text"></i><br>
<span class="small">Job Posts</span>
</a>
</div>

<div class="col-6 col-md-4 col-lg-2">
<a href="<?= site_url('admin/applications') ?>" class="btn btn-outline-primary w-100">
<i class="bi bi-send"></i><br>
<span class="small">Applications</span>
</a>
</div>

<div class="col-6 col-md-4 col-lg-2">
<a href="<?= site_url('admin/contents') ?>" class="btn btn-outline-primary w-100">
<i class="bi bi-file-richtext"></i><br>
<span class="small">Contents</span>
</a>
</div>
</div>
</div>
</div>

<?= $this->endSection() ?>