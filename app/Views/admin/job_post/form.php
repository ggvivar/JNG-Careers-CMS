<?= $this->extend('admin/partials/layout') ?>
<?= $this->section('content') ?>

<?php
$jobPostedDate = !empty($post['job_posted_date']) ? date('Y-m-d\TH:i', strtotime($post['job_posted_date'])) : '';
$validFrom = !empty($post['valid_from']) ? date('Y-m-d\TH:i', strtotime($post['valid_from'])) : '';
$validTo = !empty($post['valid_to']) ? date('Y-m-d\TH:i', strtotime($post['valid_to'])) : '';
?>

<?= view('admin/partials/form_template', [
    'title' => $mode === 'edit' ? 'Edit Job Post' : 'Create Job Post',
    'subtitle' => 'Job posting details',
    'backUrl' => 'admin/job-posts',
    'fields' => [
        ['type' => 'select', 'name' => 'job_id', 'label' => 'Job', 'required' => true, 'col' => 'col-12 col-lg-6', 'options' => $jobOptions ?? [], 'value' => $post['job_id'] ?? ''],
        ['type' => 'select', 'name' => 'company_id', 'label' => 'Company', 'col' => 'col-12 col-lg-6', 'options' => $companyOptions ?? [], 'value' => $post['company_id'] ?? ''],
        ['type' => 'select', 'name' => 'department_id', 'label' => 'Department', 'col' => 'col-12 col-lg-6', 'options' => $departmentOptions ?? [], 'value' => $post['department_id'] ?? ''],
        ['type' => 'text', 'name' => 'location', 'label' => 'Location', 'col' => 'col-12 col-lg-6', 'value' => $post['location'] ?? ''],
        ['type' => 'text', 'name' => 'salary_range', 'label' => 'Salary Range', 'col' => 'col-12 col-lg-4', 'value' => $post['salary_range'] ?? ''],
        ['type' => 'text', 'name' => 'experience_range', 'label' => 'Experience Range', 'col' => 'col-12 col-lg-4', 'value' => $post['experience_range'] ?? ''],
        ['type' => 'number', 'name' => 'rank_hiring', 'label' => 'Rank Hiring', 'col' => 'col-12 col-lg-4', 'value' => $post['rank_hiring'] ?? ''],
        ['type' => 'select', 'name' => 'status_id', 'label' => 'Status', 'col' => 'col-12 col-lg-6', 'options' => $statusOptions ?? [], 'value' => $post['status_id'] ?? ''],
        ['type' => 'datetime-local', 'name' => 'job_posted_date', 'label' => 'Posted Date', 'col' => 'col-12 col-lg-4', 'value' => $jobPostedDate],
        ['type' => 'datetime-local', 'name' => 'valid_from', 'label' => 'Valid From', 'col' => 'col-12 col-lg-4', 'value' => $validFrom],
        ['type' => 'datetime-local', 'name' => 'valid_to', 'label' => 'Valid To', 'col' => 'col-12 col-lg-4', 'value' => $validTo],
    ],
]) ?>

<?= $this->endSection() ?>