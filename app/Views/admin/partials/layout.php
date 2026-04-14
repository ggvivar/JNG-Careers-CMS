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
                str_contains($code, 'default')    => 'bi-ui-checks-grid',
                default                           => 'bi-gear',
            },

            'Career' => match (true) {
                str_contains($code, 'job')        => 'bi-briefcase',
                str_contains($code, 'applicant')  => 'bi-person-lines-fill',
                str_contains($code, 'resume')     => 'bi-file-earmark-person',
                str_contains($code, 'processing') => 'bi-person-workspace',
                default                           => 'bi-briefcase',
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

        if (admin_is_active('admin/applications/assigned-to-me', $path) === 'active') {
            return true;
        }

        if (admin_is_active('admin/reports/talent-acquisition', $path) === 'active') {
            return true;
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

<title><?= esc($title ?? 'Career@Joy-Nostalg') ?></title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    background:#f4f6fb;
    font-size:14px;
    font-family:'Segoe UI', sans-serif;
}

.navbar{
    height:52px;
    box-shadow:0 2px 8px rgba(0,0,0,0.08);
}

.navbar-brand{
    font-size:16px;
    white-space:nowrap;
}

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

.submenu{
    background:#fff;
}

.submenu .list-group-item{
    padding-left:38px;
    font-size:13px;
}

.content-wrap{
    padding:22px;
}

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

.btn{
    font-size:13px;
    padding:5px 10px;
}

.btn-sm{
    font-size:12px;
    padding:3px 8px;
}

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


#toast-container {
    position: fixed;
    top: 70px;
    right: 20px;
    width: 350px;
    max-width: calc(100vw - 40px);
    z-index: 9999;
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* 🔥 STACK ANIMATION */
.app-toast {
    width: 100%;
    opacity: 0;
    transform: translateY(-12px) scale(.98);
    transition:
        opacity .28s ease,
        transform .28s ease,
        max-height .28s ease,
        margin .28s ease,
        padding .28s ease;
    border-radius: 12px;
    max-height: 220px;
    overflow: hidden;
    will-change: transform, opacity;
}

.app-toast.show {
    opacity: 1;
}

.app-toast.removing {
    opacity: 0;
    transform: translateY(-8px) scale(.96);
    max-height: 0;
    margin: 0;
    padding-top: 0;
    padding-bottom: 0;
    border-width: 0;
}

.app-toast .toast-body-wrap {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.app-toast .toast-text {
    flex: 1;
    min-width: 0;
    padding-top: 2px;
}

.app-toast .toast-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.app-toast .toast-progress {
    position: relative;
    width: 26px;
    height: 26px;
    flex-shrink: 0;
}

.app-toast .toast-progress svg {
    width: 26px;
    height: 26px;
    display: block;
    transform: rotate(-90deg);
}

.app-toast .toast-progress .track {
    fill: none;
    stroke: rgba(0,0,0,.12);
    stroke-width: 3;
}

.app-toast .toast-progress .bar {
    fill: none;
    stroke: currentColor;
    stroke-width: 3;
    stroke-linecap: round;
    stroke-dasharray: 69.12;
    stroke-dashoffset: 0;
    transition: stroke-dashoffset .1s linear;
}

.app-toast .toast-progress .time-label {
    position: absolute;
    inset: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
    line-height: 1;
    color: currentColor;
    pointer-events: none;
}

.app-toast.alert-success .toast-progress,
.app-toast.alert-success .time-label { color: #198754; }

.app-toast.alert-danger .toast-progress,
.app-toast.alert-danger .time-label { color: #dc3545; }

.app-toast.alert-warning .toast-progress,
.app-toast.alert-warning .time-label { color: #fd7e14; }

.app-toast.alert-info .toast-progress,
.app-toast.alert-info .time-label { color: #0dcaf0; }

@media (max-width:991px){
    .sidebar{
        display:none;
    }

    .navbar-brand{
        margin-left:auto;
        font-size:15px;
    }

    #toast-container {
        right: 50%;
        transform: translateX(50%);
        width: min(95vw, 400px);
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
            Career@Joy-Nostalg
        </a>

        <div class="d-flex align-items-center gap-2 ms-lg-auto">
            <span class="text-white-50 small d-none d-md-inline">
                <?= esc(session()->get('admin_name') ?? 'Admin') ?>
            </span>

            <a class="btn btn-sm btn-outline-light d-none d-lg-inline"
            href="<?= site_url('admin/profile') ?>">
                <i class="bi bi-person-circle"></i>
                Profile
            </a>

            <a class="btn btn-sm btn-light d-none d-lg-inline"
            href="<?= site_url('admin/logout') ?>">
                <i class="bi bi-box-arrow-right"></i>
                Logout
            </a>
        </div>

    </div>
</nav>

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

            <?php foreach ($careerFeatures as $feature): ?>
    <?php if (($feature['code'] ?? '') === 'applications' && rbac_can_feature('applications', 'can_view')): ?>
        <?php
        $applicationsOpen =
            admin_is_active('admin/applications', $path) === 'active' ||
            admin_is_active('admin/applications/assigned-to-me', $path) === 'active' ||
            admin_is_active('admin/reports/talent-acquisition', $path) === 'active';
        ?>
        <button class="menu-toggle <?= $applicationsOpen ? 'active' : '' ?>"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mobileApplicationsSubmenu"
                aria-expanded="<?= $applicationsOpen ? 'true' : 'false' ?>"
                aria-controls="mobileApplicationsSubmenu">
            <i class="bi <?= menu_icon('Career', $feature['code']) ?>"></i>
            <?= esc($feature['name']) ?>
            <i class="bi bi-chevron-down chevron"></i>
        </button>

        <div class="collapse <?= $applicationsOpen ? 'show' : '' ?>" id="mobileApplicationsSubmenu">
            <div class="submenu">
                <a class="list-group-item <?= admin_is_active('admin/applications', $path) ?>"
                href="<?= site_url('admin/applications') ?>">
                    <i class="bi bi-list-ul"></i>
                    All Applications
                </a>

                <a class="list-group-item <?= admin_is_active('admin/applications/assigned-to-me', $path) ?>"
                href="<?= site_url('admin/applications/assigned-to-me') ?>">
                    <i class="bi bi-person-workspace"></i>
                    My Processing
                </a>

                <a class="list-group-item <?= admin_is_active('admin/reports/talent-acquisition', $path) ?>"
                href="<?= site_url('admin/reports/talent-acquisition') ?>">
                    <i class="bi bi-bar-chart-line"></i>
                    Talent Acquisition Report
                </a>
            </div>
        </div>
    <?php else: ?>
        <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
           href="<?= site_url('admin/' . $feature['code']) ?>">
            <i class="bi <?= menu_icon('Career', $feature['code']) ?>"></i>
            <?= esc($feature['name']) ?>
        </a>
    <?php endif; ?>
<?php endforeach; ?>

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

            <div class="d-grid gap-2">
                <a class="btn btn-outline-primary"
                href="<?= site_url('admin/profile') ?>">
                    <i class="bi bi-person-circle"></i>
                    Profile
                </a>

                <a class="btn btn-danger"
                href="<?= site_url('admin/logout') ?>">
                    <i class="bi bi-box-arrow-right"></i>
                    Logout
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row">

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

                <?php foreach ($careerFeatures as $feature): ?>
    <?php if (($feature['code'] ?? '') === 'applications' && rbac_can_feature('applications', 'can_view')): ?>
        <?php
$applicationsOpen =
    admin_is_active('admin/applications', $path) === 'active' ||
    admin_is_active('admin/applications/assigned-to-me', $path) === 'active' ||
    admin_is_active('admin/reports/talent-acquisition', $path) === 'active';
?>

        <button class="menu-toggle <?= $applicationsOpen ? 'active' : '' ?>"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#desktopApplicationsSubmenu"
                aria-expanded="<?= $applicationsOpen ? 'true' : 'false' ?>"
                aria-controls="desktopApplicationsSubmenu">
            <i class="bi <?= menu_icon('Career', $feature['code']) ?>"></i>
            <?= esc($feature['name']) ?>
            <i class="bi bi-chevron-down chevron"></i>
        </button>

        <div class="collapse <?= $applicationsOpen ? 'show' : '' ?>" id="desktopApplicationsSubmenu">
    <div class="submenu">
        <a class="list-group-item <?= admin_is_active('admin/applications', $path) ?>"
           href="<?= site_url('admin/applications') ?>">
            <i class="bi bi-list-ul"></i>
            All Applications
        </a>

        <a class="list-group-item <?= admin_is_active('admin/applications/assigned-to-me', $path) ?>"
           href="<?= site_url('admin/applications/assigned-to-me') ?>">
            <i class="bi bi-person-workspace"></i>
            My Processing
        </a>

        <a class="list-group-item <?= admin_is_active('admin/reports/talent-acquisition', $path) ?>"
           href="<?= site_url('admin/reports/talent-acquisition') ?>">
            <i class="bi bi-bar-chart-line"></i>
            Talent Acquisition Report
        </a>
    </div>
</div>
    <?php else: ?>
        <a class="list-group-item <?= admin_is_active('admin/' . $feature['code'], $path) ?>"
           href="<?= site_url('admin/' . $feature['code']) ?>">
            <i class="bi <?= menu_icon('Career', $feature['code']) ?>"></i>
            <?= esc($feature['name']) ?>
        </a>
    <?php endif; ?>
<?php endforeach; ?>

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

        <main class="col-lg-10 col-12 content-wrap">

            <?php if (session()->getFlashdata('success')): ?>
            <script>
            window.appAlerts = window.appAlerts || [];
            window.appAlerts.push({
                type: 'success',
                message: <?= json_encode(session()->getFlashdata('success')) ?>
            });
            </script>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <script>
            window.appAlerts = window.appAlerts || [];
            window.appAlerts.push({
                type: 'danger',
                message: <?= json_encode(session()->getFlashdata('error')) ?>
            });
            </script>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
            <script>
            window.appAlerts = window.appAlerts || [];
            window.appAlerts.push({
                type: 'warning',
                message: <?= json_encode(session()->getFlashdata('warning')) ?>
            });
            </script>
            <?php endif; ?>

            <?php if (session()->getFlashdata('info')): ?>
            <script>
            window.appAlerts = window.appAlerts || [];
            window.appAlerts.push({
                type: 'info',
                message: <?= json_encode(session()->getFlashdata('info')) ?>
            });
            </script>
            <?php endif; ?>

            <div id="toast-container"></div>

            <?= $this->renderSection('content') ?>
            <?= $this->renderSection('scripts') ?>

        </main>

    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('toast-container');
    if (!container) return;

    function restackToasts() {
        const toasts = Array.from(container.querySelectorAll('.app-toast:not(.removing)'));

        toasts.forEach((toast, index) => {
            toast.style.zIndex = String(1000 - index);
            toast.style.transform = toast.classList.contains('show')
                ? `translateY(${index * 4}px) scale(${1 - (index * 0.02)})`
                : 'translateY(-12px) scale(.98)';
        });
    }

    function removeToast(toast) {
        if (!toast || toast.classList.contains('removing')) return;

        if (toast._animationFrame) cancelAnimationFrame(toast._animationFrame);
        if (toast._timeout) clearTimeout(toast._timeout);

        toast.classList.remove('show');
        toast.classList.add('removing');

        restackToasts();

        setTimeout(() => {
            toast.remove();
            restackToasts();
        }, 280);
    }

    function startCircularTimer(toast, duration) {
        const progressBar = toast.querySelector('.bar');
        const timeLabel = toast.querySelector('.time-label');
        if (!progressBar || !timeLabel) return;

        const radius = 11;
        const circumference = 2 * Math.PI * radius;
        const start = performance.now();
        let pausedElapsed = 0;
        let pauseStartedAt = null;

        progressBar.style.strokeDasharray = circumference;
        progressBar.style.strokeDashoffset = 0;

        function update(now) {
            if (pauseStartedAt !== null) {
                toast._animationFrame = requestAnimationFrame(update);
                return;
            }

            const elapsed = now - start - pausedElapsed;
            const remaining = Math.max(0, duration - elapsed);
            const progress = remaining / duration;
            const offset = circumference * (1 - progress);

            progressBar.style.strokeDashoffset = offset;
            timeLabel.textContent = Math.ceil(remaining / 1000);

            if (remaining > 0 && !toast.classList.contains('removing')) {
                toast._animationFrame = requestAnimationFrame(update);
            }
        }

        toast.addEventListener('mouseenter', () => {
            if (pauseStartedAt === null) {
                pauseStartedAt = performance.now();
            }
            if (toast._timeout) {
                clearTimeout(toast._timeout);
                toast._timeout = null;
            }
        });

        toast.addEventListener('mouseleave', () => {
            if (pauseStartedAt !== null) {
                pausedElapsed += performance.now() - pauseStartedAt;
                pauseStartedAt = null;
            }

            const currentRemaining = Math.max(
                0,
                duration - ((performance.now() - start) - pausedElapsed)
            );

            toast._timeout = setTimeout(() => removeToast(toast), currentRemaining);
        });

        toast._animationFrame = requestAnimationFrame(update);
        toast._timeout = setTimeout(() => removeToast(toast), duration);
    }

    function createToast(type, message) {
        const toast = document.createElement('div');
        toast.className = `app-toast alert alert-${type} alert-dismissible shadow-sm mb-0`;
        toast.setAttribute('role', 'alert');

        toast.innerHTML = `
            <div class="toast-body-wrap">
                <div class="toast-text pe-1">${message}</div>

                <div class="toast-actions">
                    <div class="toast-progress" aria-hidden="true">
                        <svg viewBox="0 0 26 26">
                            <circle class="track" cx="13" cy="13" r="11"></circle>
                            <circle class="bar" cx="13" cy="13" r="11"></circle>
                        </svg>
                        <span class="time-label">5</span>
                    </div>

                    <button type="button" class="btn-close" aria-label="Close"></button>
                </div>
            </div>
        `;

        const closeBtn = toast.querySelector('.btn-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => removeToast(toast));
        }

        container.prepend(toast);
        restackToasts();

        requestAnimationFrame(() => {
            toast.classList.add('show');
            restackToasts();
        });

        startCircularTimer(toast, 5000);
    }

    if (Array.isArray(window.appAlerts)) {
        window.appAlerts.forEach(alert => {
            if (alert && alert.message) {
                createToast(alert.type || 'info', alert.message);
            }
        });
    }
});
</script>
</body>
</html>