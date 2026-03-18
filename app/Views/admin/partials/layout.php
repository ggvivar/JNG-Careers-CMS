<?php
helper('rbac');

$uri  = service('uri');
$path = trim((string) $uri->getPath(), '/');

if (! function_exists('admin_is_active')) {
    function admin_is_active(string $needle, string $path): string
    {
        $needle = trim($needle, '/');
        if ($needle === '') return '';
        return str_starts_with($path, $needle) ? 'active' : '';
    }
}

$adminFeatures  = rbac_features_by_module('Admin');
$careerFeatures = rbac_features_by_module('Career');
$cmsFeatures    = rbac_features_by_module('CMS');
?>

<!doctype html>
<html lang="en">
<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= esc($title ?? 'Career@Joy-Nostalg CMS') ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

body{
background:#f4f6fb;
font-size:14px;
}

/* NAVBAR */

.navbar{
height:52px;
box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

/* SIDEBAR */

.sidebar{
min-height:calc(100vh - 52px);
background:#fff;
border-right:1px solid #eee;
}

.sidebar .list-group-item{
border:none;
padding:10px 16px;
color:#495057;
display:flex;
align-items:center;
gap:10px;
transition:all .2s ease;
}

.sidebar .list-group-item i{
font-size:16px;
}

.sidebar .list-group-item:hover{
background:#f2f5ff;
transform:translateX(3px);
}

.sidebar .list-group-item.active{
background:#e8f0ff;
color:#0d6efd;
font-weight:600;
}

/* section title */

.sidebar .menu-title{
font-size:11px;
padding:10px 16px;
text-transform:uppercase;
color:#6c757d;
background:#f8f9fa;
font-weight:600;
letter-spacing:.05em;
}

/* submenu */

.submenu{
padding-left:10px;
}

.submenu .list-group-item{
font-size:13px;
padding-left:32px;
}

/* CONTENT */

.content-wrap{
padding:22px;
}

/* DASHBOARD CARDS */

.dashboard-card{
border:none;
border-radius:12px;
padding:18px;
background:#fff;
box-shadow:0 3px 12px rgba(0,0,0,0.06);
transition:all .2s ease;
}

.dashboard-card:hover{
transform:translateY(-3px);
box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.dashboard-icon{
font-size:26px;
color:#0d6efd;
}

/* SMALL BUTTONS */

.btn{
font-size:13px;
padding:5px 10px;
}

.btn-sm{
font-size:12px;
padding:3px 8px;
}

/* TABLES */

.table{
background:#fff;
border-radius:10px;
overflow:hidden;
}

.table thead{
background:#f1f3f9;
font-size:13px;
}

.table td{
vertical-align:middle;
}

/* MOBILE */

@media (max-width:991px){

.sidebar{
display:none;
}

}

</style>

</head>

<body>

<nav class="navbar navbar-dark bg-primary">

<div class="container-fluid">

<button class="navbar-toggler me-2 d-lg-none"
type="button"
data-bs-toggle="offcanvas"
data-bs-target="#mobileSidebar">
<span class="navbar-toggler-icon"></span>
</button>

<a class="navbar-brand fw-bold text-warning" href="<?= site_url('admin') ?>">
Career@Joy-Nostalg CMS
</a>

<div class="ms-auto d-flex align-items-center gap-2">

<span class="text-white-50 small">
<?= esc(session()->get('admin_name') ?? 'Admin') ?>
</span>

<a class="btn btn-sm btn-light"
href="<?= site_url('admin/logout') ?>">
<i class="bi bi-box-arrow-right"></i>
Logout
</a>

</div>

</div>

</nav>

<!-- MOBILE SIDEBAR -->

<div class="offcanvas offcanvas-start"
tabindex="-1"
id="mobileSidebar">

<div class="offcanvas-header">

<h5 class="offcanvas-title">Menu</h5>

<button type="button"
class="btn-close"
data-bs-dismiss="offcanvas">
</button>

</div>

<div class="offcanvas-body p-0">

<div class="list-group list-group-flush">

<a class="list-group-item <?= ($path === 'admin' ? 'active' : '') ?>"
href="<?= site_url('admin') ?>">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>

</div>

</div>

</div>


<div class="container-fluid">

<div class="row">

<!-- SIDEBAR -->

<aside class="col-lg-2 sidebar p-0">

<div class="list-group list-group-flush">

<a class="list-group-item <?= ($path === 'admin' ? 'active' : '') ?>"
href="<?= site_url('admin') ?>">
<i class="bi bi-speedometer2"></i>
Dashboard
</a>


<?php if (! empty($adminFeatures)): ?>

<div class="menu-title">Admin</div>

<?php foreach ($adminFeatures as $feature): ?>

<a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
href="<?= site_url('admin/' . $feature['code']) ?>">
<i class="bi bi-gear"></i>
<?= esc($feature['name']) ?>
</a>

<?php endforeach; ?>

<?php endif; ?>


<?php if (! empty($careerFeatures)): ?>

<div class="menu-title">Career</div>

<?php foreach ($careerFeatures as $feature): ?>

<a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
href="<?= site_url('admin/' . $feature['code']) ?>">
<i class="bi bi-briefcase"></i>
<?= esc($feature['name']) ?>
</a>

<?php endforeach; ?>

<?php endif; ?>


<?php if (! empty($cmsFeatures)): ?>

<div class="menu-title">CMS</div>

<?php foreach ($cmsFeatures as $feature): ?>

<a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
href="<?= site_url('admin/' . $feature['code']) ?>">
<i class="bi bi-file-earmark-text"></i>
<?= esc($feature['name']) ?>
</a>

<?php endforeach; ?>

<?php endif; ?>


</div>

</aside>


<!-- CONTENT -->

<main class="col-lg-10 col-12 content-wrap">


<?php if (session()->getFlashdata('error')): ?>

<div class="alert alert-danger alert-dismissible fade show">

<?= esc(session()->getFlashdata('error')) ?>

<button type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>


<?php if (session()->getFlashdata('success')): ?>

<div class="alert alert-success alert-dismissible fade show">

<?= esc(session()->getFlashdata('success')) ?>

<button type="button"
class="btn-close"
data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>


<?= $this->renderSection('content') ?>


</main>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>