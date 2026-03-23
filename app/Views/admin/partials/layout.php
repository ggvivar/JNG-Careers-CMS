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

if (! function_exists('menu_icon')) {
    function menu_icon(string $module, string $code): string
    {
        $code = strtolower($code);

        return match ($module) {
            'Admin' => match (true) {
                str_contains($code, 'user')       => 'bi-people',
                str_contains($code, 'role')       => 'bi-person-gear',
                str_contains($code, 'permission') => 'bi-shield-lock',
                str_contains($code, 'setting')    => 'bi-sliders',
                default                           => 'bi-gear',
            },

            'Career' => match (true) {
                str_contains($code, 'job')       => 'bi-briefcase',
                str_contains($code, 'applicant') => 'bi-person-lines-fill',
                str_contains($code, 'resume')    => 'bi-file-earmark-person',
                default                          => 'bi-briefcase',
            },

            'CMS' => match (true) {
                str_contains($code, 'page')   => 'bi-file-earmark-text',
                str_contains($code, 'post')   => 'bi-journal-text',
                str_contains($code, 'banner') => 'bi-image',
                str_contains($code, 'media')  => 'bi-images',
                default                       => 'bi-file-earmark',
            },

            default => 'bi-circle',
        };
    }
}

if (! function_exists('module_has_active_feature')) {
    function module_has_active_feature(array $features, string $path): bool
    {
        foreach ($features as $feature) {
            if (admin_is_active('admin/' . $feature['code'], $path) === 'active') {
                return true;
            }
        }
        return false;
    }
}

$adminFeatures  = rbac_features_by_module('Admin');
$careerFeatures = rbac_features_by_module('Career');
$cmsFeatures    = rbac_features_by_module('CMS');

$adminOpen  = module_has_active_feature($adminFeatures, $path);
$careerOpen = module_has_active_feature($careerFeatures, $path);
$cmsOpen    = module_has_active_feature($cmsFeatures, $path);
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
    font-family:'Segoe UI', sans-serif;
}

/* NAVBAR */
.navbar{
    height:52px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.navbar-brand{
    font-size:16px;
    white-space:nowrap;
}

/* SIDEBAR */
.sidebar{
    min-height:calc(100vh - 52px);
    background:#fff;
    border-right:1px solid #eee;
}

.sidebar .list-group-item,
.offcanvas .list-group-item,
.sidebar .menu-toggle,
.offcanvas .menu-toggle{
    border:none;
    padding:10px 16px 10px 22px;
    color:#495057;
    display:flex;
    align-items:center;
    gap:10px;
    transition:all .2s ease;
    position:relative;
    text-decoration:none;
    background:#fff;
    width:100%;
}

.sidebar .list-group-item i,
.offcanvas .list-group-item i,
.sidebar .menu-toggle i,
.offcanvas .menu-toggle i{
    width:18px;
    text-align:center;
    font-size:16px;
    opacity:.85;
}

.sidebar .list-group-item::before,
.offcanvas .list-group-item::before,
.sidebar .menu-toggle::before,
.offcanvas .menu-toggle::before{
    content:"";
    position:absolute;
    left:10px;
    top:50%;
    transform:translateY(-50%);
    width:4px;
    height:4px;
    border-radius:50%;
    background:#d0d7ff;
    opacity:0;
    transition:.2s;
}

.sidebar .list-group-item:hover,
.offcanvas .list-group-item:hover,
.sidebar .menu-toggle:hover,
.offcanvas .menu-toggle:hover{
    background:#f2f5ff;
    transform:translateX(3px);
    color:#0d6efd;
}

.sidebar .list-group-item:hover::before,
.sidebar .list-group-item.active::before,
.sidebar .menu-toggle:hover::before,
.sidebar .menu-toggle.active::before,
.offcanvas .list-group-item:hover::before,
.offcanvas .list-group-item.active::before,
.offcanvas .menu-toggle:hover::before,
.offcanvas .menu-toggle.active::before{
    opacity:1;
}

.sidebar .list-group-item.active,
.offcanvas .list-group-item.active,
.sidebar .menu-toggle.active,
.offcanvas .menu-toggle.active{
    background:#e8f0ff;
    color:#0d6efd;
    font-weight:600;
}

.menu-toggle{
    cursor:pointer;
    justify-content:flex-start;
}

.menu-toggle .chevron{
    margin-left:auto;
    width:auto;
    font-size:12px;
    transition:transform .2s ease;
}

.menu-toggle[aria-expanded="true"] .chevron{
    transform:rotate(180deg);
}

/* SECTION BODY */
.submenu{
    background:#fff;
}

.submenu .list-group-item{
    padding-left:38px;
    font-size:13px;
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

/* BUTTONS */
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

    .navbar-brand{
        margin-left:auto;
        font-size:15px;
    }
}
</style>
</head>

<body>

<nav style="background:#0A0147" class="navbar navbar-dark">
    <div class="container-fluid d-flex align-items-center">

        <button class="navbar-toggler me-2 d-lg-none"
                type="button"
                data-bs-toggle="offcanvas"
                data-bs-target="#mobileSidebar"
                aria-controls="mobileSidebar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <a class="navbar-brand fw-bold text-warning ms-auto ms-lg-0"
           href="<?= site_url('admin') ?>">
            Career@Joy-Nostalg CMS
        </a>

        <div class="d-flex align-items-center gap-2 ms-lg-auto">
            <span class="text-white-50 small d-none d-md-inline">
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </span>

            <a class="btn btn-sm btn-light d-none d-lg-inline"
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
     id="mobileSidebar"
     aria-labelledby="mobileSidebarLabel">

    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="mobileSidebarLabel">Menu</h5>

        <button type="button"
                class="btn-close"
                data-bs-dismiss="offcanvas"
                aria-label="Close"></button>
    </div>

    <div class="offcanvas-body p-0 d-flex flex-column">
        <div class="list-group list-group-flush">

            <a class="list-group-item <?= ($path === 'admin' ? 'active' : '') ?>"
               href="<?= site_url('admin') ?>">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>

            <?php if (! empty($adminFeatures)): ?>
                <button class="menu-toggle <?= $adminOpen ? 'active' : '' ?>"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mobileAdminMenu"
                        aria-expanded="<?= $adminOpen ? 'true' : 'false' ?>"
                        aria-controls="mobileAdminMenu">
                    <i class="bi bi-gear"></i>
                    Admin
                    <i class="bi bi-chevron-down chevron"></i>
                </button>

                <div class="collapse <?= $adminOpen ? 'show' : '' ?>" id="mobileAdminMenu">
                    <div class="submenu">
                        <?php foreach ($adminFeatures as $feature): ?>
                            <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
                               href="<?= site_url('admin/' . $feature['code']) ?>">
                                <i class="bi <?= menu_icon('Admin', $feature['code']) ?>"></i>
                                <?= esc($feature['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (! empty($careerFeatures)): ?>
                <button class="menu-toggle <?= $careerOpen ? 'active' : '' ?>"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mobileCareerMenu"
                        aria-expanded="<?= $careerOpen ? 'true' : 'false' ?>"
                        aria-controls="mobileCareerMenu">
                    <i class="bi bi-briefcase"></i>
                    Career
                    <i class="bi bi-chevron-down chevron"></i>
                </button>

                <div class="collapse <?= $careerOpen ? 'show' : '' ?>" id="mobileCareerMenu">
                    <div class="submenu">
                        <?php foreach ($careerFeatures as $feature): ?>
                            <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
                               href="<?= site_url('admin/' . $feature['code']) ?>">
                                <i class="bi <?= menu_icon('Career', $feature['code']) ?>"></i>
                                <?= esc($feature['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <?php if (! empty($cmsFeatures)): ?>
                <button class="menu-toggle <?= $cmsOpen ? 'active' : '' ?>"
                        type="button"
                        data-bs-toggle="collapse"
                        data-bs-target="#mobileCmsMenu"
                        aria-expanded="<?= $cmsOpen ? 'true' : 'false' ?>"
                        aria-controls="mobileCmsMenu">
                    <i class="bi bi-file-earmark-text"></i>
                    CMS
                    <i class="bi bi-chevron-down chevron"></i>
                </button>

                <div class="collapse <?= $cmsOpen ? 'show' : '' ?>" id="mobileCmsMenu">
                    <div class="submenu">
                        <?php foreach ($cmsFeatures as $feature): ?>
                            <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
                               href="<?= site_url('admin/' . $feature['code']) ?>">
                                <i class="bi <?= menu_icon('CMS', $feature['code']) ?>"></i>
                                <?= esc($feature['name']) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

        </div>

        <div class="mt-auto border-top pt-3 px-3 pb-3">
            <div class="small text-muted mb-2">
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </div>

            <a class="btn btn-danger w-100"
               href="<?= site_url('admin/logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

        <!-- DESKTOP SIDEBAR -->
        <aside class="col-lg-2 sidebar p-0">
            <div class="list-group list-group-flush">

                <a class="list-group-item <?= ($path === 'admin' ? 'active' : '') ?>"
                   href="<?= site_url('admin') ?>">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>

                <?php if (! empty($adminFeatures)): ?>
                    <button class="menu-toggle <?= $adminOpen ? 'active' : '' ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#desktopAdminMenu"
                            aria-expanded="<?= $adminOpen ? 'true' : 'false' ?>"
                            aria-controls="desktopAdminMenu">
                        <i class="bi bi-gear"></i>
                        Admin
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>

                    <div class="collapse <?= $adminOpen ? 'show' : '' ?>" id="desktopAdminMenu">
                        <div class="submenu">
                            <?php foreach ($adminFeatures as $feature): ?>
                                <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
                                   href="<?= site_url('admin/' . $feature['code']) ?>">
                                    <i class="bi <?= menu_icon('Admin', $feature['code']) ?>"></i>
                                    <?= esc($feature['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (! empty($careerFeatures)): ?>
                    <button class="menu-toggle <?= $careerOpen ? 'active' : '' ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#desktopCareerMenu"
                            aria-expanded="<?= $careerOpen ? 'true' : 'false' ?>"
                            aria-controls="desktopCareerMenu">
                        <i class="bi bi-briefcase"></i>
                        Career
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>

                    <div class="collapse <?= $careerOpen ? 'show' : '' ?>" id="desktopCareerMenu">
                        <div class="submenu">
                            <?php foreach ($careerFeatures as $feature): ?>
                                <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
                                   href="<?= site_url('admin/' . $feature['code']) ?>">
                                    <i class="bi <?= menu_icon('Career', $feature['code']) ?>"></i>
                                    <?= esc($feature['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (! empty($cmsFeatures)): ?>
                    <button class="menu-toggle <?= $cmsOpen ? 'active' : '' ?>"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#desktopCmsMenu"
                            aria-expanded="<?= $cmsOpen ? 'true' : 'false' ?>"
                            aria-controls="desktopCmsMenu">
                        <i class="bi bi-file-earmark-text"></i>
                        CMS
                        <i class="bi bi-chevron-down chevron"></i>
                    </button>

                    <div class="collapse <?= $cmsOpen ? 'show' : '' ?>" id="desktopCmsMenu">
                        <div class="submenu">
                            <?php foreach ($cmsFeatures as $feature): ?>
                                <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
                                   href="<?= site_url('admin/' . $feature['code']) ?>">
                                    <i class="bi <?= menu_icon('CMS', $feature['code']) ?>"></i>
                                    <?= esc($feature['name']) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </aside>

        <!-- CONTENT -->
        <main class="col-lg-10 col-12 content-wrap">

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= esc(session()->getFlashdata('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= esc(session()->getFlashdata('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?= $this->renderSection('content') ?>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>