<?php

helper('form_builder');

$title       = $title ?? 'Form';
$subtitle    = $subtitle ?? '';
$backUrl     = $backUrl ?? '#';
$fields      = $fields ?? [];
$sections    = $sections ?? [];
$submitLabel = $submitLabel ?? 'Save';

if (empty($sections)) {
    $sections = [
        [
            'id' => 'main',
            'title' => 'Form Details',
            'subtitle' => 'Fill in the required information below',
            'fields' => $fields,
        ],
    ];
}
?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

<style>
    .generic-form-page {
        max-width: 1200px;
        margin: 0 auto;
    }

    .modern-card {
        border: 1px solid #e9ecef;
        border-radius: 18px;
        box-shadow: 0 8px 24px rgba(16, 24, 40, 0.06);
        overflow: hidden;
        background: #fff;
    }

    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f3f5;
        background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #212529;
        margin: 0;
    }

    .section-subtitle {
        font-size: .85rem;
        color: #6c757d;
        margin-top: .2rem;
    }

    .page-hero {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .page-title {
        font-size: 1.6rem;
        font-weight: 800;
        letter-spacing: -.02em;
        margin-bottom: .15rem;
    }

    .page-subtitle {
        color: #6c757d;
        margin: 0;
    }

    .badge-soft {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .75rem;
        font-weight: 600;
        color: #0d6efd;
        background: rgba(13, 110, 253, 0.08);
        border: 1px solid rgba(13, 110, 253, 0.12);
        padding: .4rem .7rem;
        border-radius: 999px;
    }

    .modern-card .card-body {
        padding: 1.25rem;
    }

    .form-label {
        font-weight: 600;
        color: #344054;
        margin-bottom: .45rem;
    }

    .field-shell {
        padding: .1rem 0;
    }

    .field-shell.is-checkbox {
        padding-top: .4rem;
    }

    .form-check {
        padding: .85rem 1rem .85rem 2.4rem;
        border: 1px solid #e4e7ec;
        border-radius: 14px;
        background: #f9fafb;
        min-height: 52px;
        display: flex;
        align-items: center;
    }

    .form-check-input {
        margin-top: 0;
    }

    .form-check-label {
        font-weight: 600;
        color: #344054;
    }

    .meta-note,
    .form-text {
        font-size: .8rem;
        color: #98a2b3;
    }

    .sticky-actions {
        position: sticky;
        bottom: 16px;
        z-index: 10;
        margin-top: 1.25rem;
    }

    .sticky-actions-inner {
        display: flex;
        gap: .75rem;
        justify-content: flex-end;
        align-items: center;
        background: rgba(255, 255, 255, 0.92);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(222, 226, 230, 0.8);
        box-shadow: 0 10px 30px rgba(16, 24, 40, 0.08);
        border-radius: 18px;
        padding: .9rem 1rem;
    }

    .btn {
        border-radius: 12px;
    }

    .btn-primary {
        box-shadow: 0 8px 18px rgba(13, 110, 253, 0.18);
    }

    .form-floating > .form-control,
    .form-floating > .form-select {
        height: calc(3.5rem + 2px);
        min-height: calc(3.5rem + 2px);
        border-radius: 12px;
        border-color: #dbe1e7;
        box-shadow: none !important;
        padding-top: 1.625rem;
        padding-bottom: .625rem;
    }

    .form-floating > textarea.form-control {
        height: auto;
        min-height: 120px;
        padding-top: 1.4rem;
    }

    .form-floating > label {
        color: #667085;
        padding: 1rem .85rem;
    }

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe1e7;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12) !important;
    }

    .is-invalid,
    .was-validated .form-control:invalid,
    .was-validated .form-select:invalid {
        border-color: #dc3545 !important;
    }

    .invalid-feedback {
        display: block;
        font-size: .8rem;
        margin-top: .4rem;
    }

    .form-section {
        display: none;
    }

    .form-section.active {
        display: block;
    }

    .form-tabs {
        display: flex;
        gap: .75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .form-tab-btn {
        min-width: 190px;
        height: 52px;
        border: 1px solid #dbe1e7;
        background: #fff;
        color: #344054;
        border-radius: 999px;
        padding: 0 1rem;
        font-size: .95rem;
        font-weight: 600;
        cursor: pointer;
        transition: .2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        white-space: nowrap;
    }

    .form-tab-btn.active {
        background: #e8f0ff;
        border-color: #bfd4ff;
        color: #0d6efd;
    }

    .select2-float {
        position: relative;
    }

    .select2-float-label {
        position: absolute;
        top: 1rem;
        left: .85rem;
        font-size: 1rem;
        color: #667085;
        line-height: 1;
        background: #fff;
        padding: 0 .25rem;
        transition: all .18s ease;
        pointer-events: none;
        z-index: 5;
    }

    .select2-float.active .select2-float-label,
    .select2-float.focused .select2-float-label {
        top: 0;
        transform: translateY(-50%);
        font-size: .75rem;
        color: #667085;
        font-weight: 400;
    }

    .select2-float.required .select2-float-label::after {
        content: " *";
        color: #dc3545;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container .select2-selection--single {
        height: calc(3.5rem + 2px) !important;
        min-height: calc(3.5rem + 2px) !important;
        border-radius: 12px !important;
        border: 1px solid #dbe1e7 !important;
        background: #fff !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: flex-end !important;
        padding-top: 1.625rem !important;
        padding-bottom: .625rem !important;
    }

    .select2-container .select2-selection__rendered {
        width: 100% !important;
        padding-left: .85rem !important;
        padding-right: 2.25rem !important;
        line-height: 1.25 !important;
        font-size: 1rem !important;
        color: #212529 !important;
        display: block !important;
        text-align: left !important;
    }

    .select2-container .select2-selection__placeholder {
        color: transparent !important;
    }

    .select2-container .select2-selection__arrow {
        height: 100% !important;
        right: 10px !important;
        top: 0 !important;
    }

    .select2-container .select2-selection__clear {
        position: absolute;
        right: 28px;
        top: 50%;
        transform: translateY(-50%);
        margin: 0 !important;
    }

    .select2-container .select2-selection--multiple {
        min-height: calc(3.5rem + 2px) !important;
        border-radius: 12px !important;
        border: 1px solid #dbe1e7 !important;
        padding: 10px 10px 6px 10px !important;
    }

    .select2-container--default.select2-container--focus .select2-selection,
    .select2-container--default.select2-container--open .select2-selection {
        border-color: #86b7fe !important;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,.12) !important;
    }

    @media (max-width: 768px) {
        .page-hero {
            flex-direction: column;
            align-items: stretch;
        }

        .sticky-actions-inner {
            justify-content: stretch;
        }

        .sticky-actions-inner .btn {
            flex: 1;
        }

        .form-tabs {
            display: grid;
            grid-template-columns: 1fr;
        }

        .form-tab-btn {
            width: 100%;
            min-width: 0;
        }
    }
</style>

<div class="generic-form-page">
    <div class="page-hero">
        <div>
            <div class="badge-soft mb-2">
                <span>Form</span>
                <span><?= esc($title) ?></span>
            </div>

            <h2 class="page-title"><?= esc($title) ?></h2>

            <?php if ($subtitle): ?>
                <p class="page-subtitle"><?= esc($subtitle) ?></p>
            <?php endif; ?>
        </div>

        <div class="d-flex gap-2">
            <?= view('custom/components/ui/button', [
                'href' => $backUrl === '#' ? '#' : site_url($backUrl),
                'label' => 'Back',
                'variant' => 'outline-secondary',
                'icon' => 'bi bi-arrow-left',
            ]) ?>
        </div>
    </div>

    <?php if (count($sections) > 1): ?>
        <?= view('custom/components/ui/tabs', ['sections' => $sections]) ?>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <?php foreach ($sections as $sectionIndex => $section): ?>
            <?= view('custom/components/form/section', [
                'section' => $section,
                'sectionIndex' => $sectionIndex,
            ]) ?>
        <?php endforeach; ?>

        <?= view('custom/components/form/actions', [
            'backUrl' => $backUrl,
            'submitLabel' => $submitLabel,
        ]) ?>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.form-tab-btn').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const target = this.getAttribute('data-tab-target');

            document.querySelectorAll('.form-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));

            this.classList.add('active');

            const targetSection = document.getElementById(target);
            if (targetSection) {
                targetSection.classList.add('active');
            }
        });
    });

    if (window.jQuery && $.fn.select2) {
        $('.js-select2').each(function () {
            const $el = $(this);
            const $wrap = $el.closest('.select2-float');

            $el.select2({
                width: '100%',
                placeholder: '',
                allowClear: !$el.prop('multiple'),
                dropdownAutoWidth: false
            });

            function syncFloatingState() {
                const val = $el.val();
                const hasValue = Array.isArray(val) ? val.length > 0 : !!val;

                if (hasValue) {
                    $wrap.addClass('active');
                } else {
                    $wrap.removeClass('active');
                }
            }

            syncFloatingState();

            $el.on('change', syncFloatingState);

            $el.on('select2:open', function () {
                $wrap.addClass('focused');
            });

            $el.on('select2:close', function () {
                $wrap.removeClass('focused');
                syncFloatingState();
            });
        });
    }

    const firstInvalid = document.querySelector('.is-invalid');
    if (firstInvalid) {
        const section = firstInvalid.closest('.form-section');
        if (section) {
            document.querySelectorAll('.form-section').forEach(s => s.classList.remove('active'));
            section.classList.add('active');

            const sectionId = section.getAttribute('id');
            document.querySelectorAll('.form-tab-btn').forEach(btn => {
                btn.classList.toggle('active', btn.getAttribute('data-tab-target') === sectionId);
            });
        }
    }
});
</script>