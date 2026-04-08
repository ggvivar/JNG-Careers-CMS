<?php
$title       = $title ?? 'Form';
$subtitle    = $subtitle ?? '';
$backUrl     = $backUrl ?? '#';
$fields      = $fields ?? [];
$submitLabel = $submitLabel ?? 'Save';
?>

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

    .form-control,
    .form-select {
        border-radius: 12px;
        border-color: #dbe1e7;
        padding-top: .6rem;
        padding-bottom: .6rem;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.12) !important;
    }

    textarea.form-control {
        min-height: 110px;
    }

    .meta-note,
    .form-text {
        font-size: .8rem;
        color: #98a2b3;
    }

    .field-shell {
        padding: .1rem 0;
    }

    .field-shell.is-checkbox {
        padding-top: 1.9rem;
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
            <a class="btn btn-outline-secondary"
               href="<?= $backUrl === '#' ? '#' : site_url($backUrl) ?>">
                <i class="bi bi-arrow-left me-1"></i> Back
            </a>
        </div>
    </div>

    <form method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="modern-card">
            <div class="section-header">
                <h3 class="section-title">Form Details</h3>
                <div class="section-subtitle">Fill in the required information below</div>
            </div>

            <div class="card-body">
                <div class="row g-4">
                    <?php foreach ($fields as $field): ?>
                        <?php
                        $type        = $field['type'] ?? 'text';
                        $name        = $field['name'] ?? '';
                        $label       = $field['label'] ?? ucfirst($name);
                        $value       = $field['value'] ?? '';
                        $required    = !empty($field['required']);
                        $col         = $field['col'] ?? 'col-12';
                        $placeholder = $field['placeholder'] ?? '';
                        $help        = $field['help'] ?? '';
                        $options     = $field['options'] ?? [];
                        $rows        = $field['rows'] ?? 4;

                        if ($name !== '') {
                            $value = old($name, $value);
                        }
                        ?>

                        <div class="<?= esc($col) ?>">
                            <div class="field-shell <?= $type === 'checkbox' ? 'is-checkbox' : '' ?>">
                                <?php if ($type !== 'checkbox'): ?>
                                    <label class="form-label">
                                        <?= esc($label) ?>
                                        <?php if ($required): ?>
                                            <span class="text-danger">*</span>
                                        <?php endif; ?>
                                    </label>
                                <?php endif; ?>

                                <?php if ($type === 'textarea'): ?>
                                    <textarea
                                        class="form-control"
                                        name="<?= esc($name) ?>"
                                        rows="<?= esc((string) $rows) ?>"
                                        placeholder="<?= esc($placeholder) ?>"
                                        <?= $required ? 'required' : '' ?>
                                    ><?= esc((string) $value) ?></textarea>

                                <?php elseif ($type === 'select'): ?>
                                    <select
                                        class="form-select"
                                        name="<?= esc($name) ?>"
                                        <?= $required ? 'required' : '' ?>
                                    >
                                        <option value="">Select...</option>

                                        <?php foreach ($options as $optValue => $optLabel): ?>
                                            <option
                                                value="<?= esc((string) $optValue) ?>"
                                                <?= (string) $optValue === (string) $value ? 'selected' : '' ?>
                                            >
                                                <?= esc((string) $optLabel) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($type === 'checkbox'): ?>
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="<?= esc($name) ?>"
                                            id="<?= esc($name) ?>"
                                            value="1"
                                            <?= !empty($value) ? 'checked' : '' ?>
                                        >

                                        <label class="form-check-label" for="<?= esc($name) ?>">
                                            <?= esc($label) ?>
                                        </label>
                                    </div>

                                <?php elseif ($type === 'file'): ?>
                                    <input
                                        type="file"
                                        class="form-control"
                                        name="<?= esc($name) ?>"
                                        <?= $required ? 'required' : '' ?>
                                    >

                                <?php else: ?>
                                    <input
                                        type="<?= esc($type) ?>"
                                        class="form-control"
                                        name="<?= esc($name) ?>"
                                        value="<?= esc((string) $value) ?>"
                                        placeholder="<?= esc($placeholder) ?>"
                                        <?= $required ? 'required' : '' ?>
                                    >
                                <?php endif; ?>

                                <?php if ($help): ?>
                                    <div class="form-text mt-2">
                                        <?= esc($help) ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <div class="sticky-actions">
            <div class="sticky-actions-inner">
                <a class="btn btn-outline-secondary"
                   href="<?= $backUrl === '#' ? '#' : site_url($backUrl) ?>">
                    Cancel
                </a>

                <button class="btn btn-primary px-4" type="submit">
                    <i class="bi bi-check-lg me-1"></i>
                    <?= esc($submitLabel) ?>
                </button>
            </div>
        </div>
    </form>
</div>