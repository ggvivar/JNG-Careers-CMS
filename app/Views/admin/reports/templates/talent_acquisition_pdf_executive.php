<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Talent Acquisition Operations Report</title>
    <style>
        @page {
            margin: 22px 24px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #1f2937;
            margin: 0;
        }

        .page-break {
            page-break-before: always;
        }

        .cover {
            position: relative;
            min-height: 720px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            padding: 34px 32px;
        }

        .cover:before {
            content: "";
            position: absolute;
            top: -70px;
            left: -60px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: #f3b400;
        }

        .cover:after {
            content: "";
            position: absolute;
            right: -90px;
            bottom: -90px;
            width: 280px;
            height: 280px;
            border-radius: 50%;
            background: #183dbf;
        }

        .cover-inner {
            position: relative;
            z-index: 2;
        }

        .cover-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.05;
            margin: 0 0 8px 0;
            color: #111827;
        }

        .cover-sub {
            font-size: 15px;
            margin: 0 0 18px 0;
            color: #374151;
        }

        .cover-note {
            font-size: 12px;
            color: #4b5563;
            line-height: 1.6;
            max-width: 420px;
        }

        .brand-box {
            margin-top: 28px;
            display: inline-block;
            background: #fff;
            border: 1px solid #e5e7eb;
            padding: 14px 16px;
            font-size: 13px;
            line-height: 1.5;
        }

        .section-title {
            font-size: 18px;
            font-weight: 800;
            margin: 0 0 10px 0;
            color: #111827;
        }

        .section-subtitle {
            font-size: 11px;
            color: #4b5563;
            margin: 0 0 10px 0;
            line-height: 1.6;
        }

        .kpi-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin-bottom: 12px;
        }

        .kpi-box {
            border: 1px solid #dbe2ea;
            padding: 12px;
            vertical-align: top;
            width: 25%;
        }

        .kpi-label {
            font-size: 9px;
            text-transform: uppercase;
            color: #6b7280;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 800;
            color: #111827;
            line-height: 1.1;
        }

        .kpi-sub {
            margin-top: 6px;
            font-size: 9px;
            color: #6b7280;
            line-height: 1.5;
        }

        .success { color: #198754; }
        .danger { color: #dc3545; }
        .warning { color: #d97706; }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            margin-bottom: 14px;
        }

        table.report th,
        table.report td {
            border: 1px solid #dbe2ea;
            padding: 7px 8px;
            vertical-align: top;
        }

        table.report th {
            background: #f8fafc;
            color: #475569;
            font-size: 10px;
            text-transform: uppercase;
            font-weight: 700;
        }

        .right {
            text-align: right;
        }

        .muted {
            color: #6b7280;
        }

        .note-box {
            border: 1px solid #dbe2ea;
            background: #fafbfc;
            padding: 10px 12px;
            font-size: 10px;
            line-height: 1.6;
            color: #374151;
            margin-top: 8px;
        }

        ul.clean {
            margin: 6px 0 0 18px;
            padding: 0;
        }

        ul.clean li {
            margin-bottom: 6px;
        }
    </style>
</head>
<body>

<?php
$generatedAt = date('F d, Y h:i A');

$totalApplications   = (int) ($stats['applications'] ?? 0);
$forReview           = (int) ($statsForApproval['applications'] ?? 0);
$applicationsOverdue = (int) ($overdueStats['applications_overdue'] ?? 0);

$vacantCount = max(
    0,
    $totalApplications
    - (int) $hiredCount
    - (int) $declinedCount
    - (int) $failedCount
    - (int) $withdrawnCount
    - (int) $noShowCount
);
?>

<!-- PAGE 1 -->
<div class="cover">
    <div class="cover-inner">
        <div class="cover-title">TALENT ACQUISITION<br>OPERATIONS</div>
        <div class="cover-sub">
            <?= esc(date('F Y', strtotime($dateFrom))) ?> | Sample Report Package
        </div>

        <div class="cover-note">
            Built from the uploaded report sample and ATS implementation notes.<br>
            Prepared for download and internal reference.<br><br>
            Generated: <?= esc($generatedAt) ?>
        </div>

        <div class="brand-box">
            <strong>Joy-Nostalg</strong><br>
            Talent Acquisition<br>
            Report Mockup + Build Guide
        </div>
    </div>
</div>

<!-- PAGE 2 -->
<div class="page-break"></div>

<h1 class="section-title">Executive Summary</h1>
<p class="section-subtitle">
    This downloadable report mirrors the uploaded template while using your current ATS implementation:
    company filter via <strong>job_list.company_id</strong>, level via <strong>job_list.experience_range</strong>,
    business unit via <strong>job.unit_group</strong>, and turnaround start date via
    <strong>job_list.job_posted_date</strong>. :contentReference[oaicite:1]{index=1}
</p>

<table class="kpi-grid">
    <tr>
        <td class="kpi-box">
            <div class="kpi-label">Grand Total</div>
            <div class="kpi-value"><?= number_format($totalApplications) ?></div>
            <div class="kpi-sub">
                Total applications<br>
                Selected period
            </div>
        </td>
        <td class="kpi-box">
            <div class="kpi-label">Hired</div>
            <div class="kpi-value success"><?= number_format((int) $hiredCount) ?></div>
            <div class="kpi-sub">
                Accepted / hired<br>
                workflow outcomes
            </div>
        </td>
        <td class="kpi-box">
            <div class="kpi-label">Vacant</div>
            <div class="kpi-value"><?= number_format($vacantCount) ?></div>
            <div class="kpi-sub">
                Open / active<br>
                pipeline records
            </div>
        </td>
        <td class="kpi-box">
            <div class="kpi-label">On Review</div>
            <div class="kpi-value warning"><?= number_format($forReview) ?></div>
            <div class="kpi-sub">
                For review / pending<br>
                action items
            </div>
        </td>
    </tr>
    <tr>
        <td class="kpi-box">
            <div class="kpi-label">Offer Success</div>
            <div class="kpi-value"><?= number_format((int) $offerSuccessRate) ?>%</div>
            <div class="kpi-sub">
                <?= number_format((int) $offerTotal) ?> offers |
                <?= number_format((int) $offerAccepted) ?> accepted |
                <?= number_format((int) $offerDeclined) ?> declined
            </div>
        </td>
        <td class="kpi-box">
            <div class="kpi-label">Avg Time to Fill</div>
            <div class="kpi-value"><?= number_format((int) $averageTimeToFill) ?> days</div>
            <div class="kpi-sub">
                Measured from job_posted_date
            </div>
        </td>
        <td class="kpi-box">
            <div class="kpi-label">Avg Days Open</div>
            <div class="kpi-value"><?= number_format((int) $averageDaysOpen) ?> days</div>
            <div class="kpi-sub">
                Open roles only
            </div>
        </td>
        <td class="kpi-box">
            <div class="kpi-label">Applications Overdue</div>
            <div class="kpi-value danger"><?= number_format($applicationsOverdue) ?></div>
            <div class="kpi-sub">
                Past due date
            </div>
        </td>
    </tr>
</table>

<h2 class="section-title" style="font-size:15px;">Status per Level</h2>
<table class="report">
    <tr>
        <th>Level</th>
        <th class="right">Total</th>
        <th class="right">Hired</th>
        <th class="right">Open</th>
    </tr>
    <?php if (!empty($levelBreakdown)): foreach ($levelBreakdown as $row): ?>
    <tr>
        <td><?= esc($row['level_name'] ?: '-') ?></td>
        <td class="right"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
        <td class="right"><?= number_format((int) ($row['hired_count'] ?? 0)) ?></td>
        <td class="right"><?= number_format((int) ($row['open_count'] ?? 0)) ?></td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td colspan="4">No data found.</td></tr>
    <?php endif; ?>
</table>

<div class="note-box">
    Intended ATS mapping: <strong>level = job_list.experience_range</strong>. This follows the uploaded template guidance. :contentReference[oaicite:2]{index=2}
</div>

<!-- PAGE 3 -->
<div class="page-break"></div>

<h1 class="section-title">Status per Business Unit</h1>
<table class="report">
    <tr>
        <th>Business Unit</th>
        <th class="right">Total</th>
        <th class="right">Hired</th>
        <th class="right">Open</th>
    </tr>
    <?php if (!empty($businessUnitBreakdown)): foreach ($businessUnitBreakdown as $row): ?>
    <tr>
        <td><?= esc($row['business_unit_name'] ?: '-') ?></td>
        <td class="right"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
        <td class="right"><?= number_format((int) ($row['hired_count'] ?? 0)) ?></td>
        <td class="right"><?= number_format((int) ($row['open_count'] ?? 0)) ?></td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td colspan="4">No data found.</td></tr>
    <?php endif; ?>
</table>

<div class="note-box">
    ATS mapping used here: <strong>business unit = job.unit_group</strong>. :contentReference[oaicite:3]{index=3}
</div>

<h1 class="section-title" style="margin-top:20px;">Offer Success and Decline Reasons</h1>
<table class="report">
    <tr>
        <th>Reason / Description</th>
        <th class="right">Count</th>
    </tr>
    <?php if (!empty($declineReasons)): foreach ($declineReasons as $row): ?>
    <tr>
        <td><?= esc($row['decline_reason'] ?: '-') ?></td>
        <td class="right"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td colspan="2">No decline reasons found.</td></tr>
    <?php endif; ?>
</table>

<div class="note-box">
    Decline reasons are sourced from <strong>workflow_histories.remarks</strong>, filtered by
    the applications feature and declined statuses. :contentReference[oaicite:4]{index=4}
</div>

<!-- PAGE 4 -->
<div class="page-break"></div>

<h1 class="section-title">Turnaround Time and Sourcing Channels</h1>

<table class="report">
    <tr>
        <th>Dimension</th>
        <th class="right">Avg Time to Fill</th>
        <th class="right">Avg Days Open</th>
    </tr>
    <?php if (!empty($levelBreakdown)): foreach ($levelBreakdown as $row): ?>
    <tr>
        <td><?= esc($row['level_name'] ?: '-') ?></td>
        <td class="right"><?= number_format((int) $averageTimeToFill) ?> days</td>
        <td class="right"><?= number_format((int) $averageDaysOpen) ?> days</td>
    </tr>
    <?php endforeach; endif; ?>
    <?php if (!empty($businessUnitBreakdown)): foreach (array_slice($businessUnitBreakdown, 0, 5) as $row): ?>
    <tr>
        <td><?= esc($row['business_unit_name'] ?: '-') ?></td>
        <td class="right"><?= number_format((int) $averageTimeToFill) ?> days</td>
        <td class="right"><?= number_format((int) $averageDaysOpen) ?> days</td>
    </tr>
    <?php endforeach; endif; ?>
</table>

<div class="note-box">
    MRF date / turnaround start date is mapped to <strong>job_list.job_posted_date</strong>. :contentReference[oaicite:5]{index=5}
</div>

<h2 class="section-title" style="font-size:15px;">Top Sourcing Channels</h2>
<table class="report">
    <tr>
        <th>Source</th>
        <th class="right">Applications</th>
    </tr>
    <?php if (!empty($sourceBreakdown)): foreach ($sourceBreakdown as $row): ?>
    <tr>
        <td><?= esc($row['source_name'] ?: '-') ?></td>
        <td class="right"><?= number_format((int) ($row['total_count'] ?? 0)) ?></td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td colspan="2">No data found.</td></tr>
    <?php endif; ?>
</table>

<h2 class="section-title" style="font-size:15px;">TA Team Performance</h2>
<table class="report">
    <tr>
        <th>Recruiter</th>
        <th class="right">Hires</th>
        <th class="right">Avg Time to Fill</th>
        <th class="right">Avg Days Open</th>
    </tr>
    <?php if (!empty($taTeamPerformance)): foreach ($taTeamPerformance as $row): ?>
    <tr>
        <td><?= esc($row['recruiter_name'] ?: 'Unassigned') ?></td>
        <td class="right"><?= number_format((int) ($row['hires_count'] ?? 0)) ?></td>
        <td class="right"><?= number_format((int) ($row['avg_time_to_fill'] ?? 0)) ?> days</td>
        <td class="right"><?= number_format((int) ($row['avg_days_open'] ?? 0)) ?> days</td>
    </tr>
    <?php endforeach; else: ?>
    <tr><td colspan="4">No data found.</td></tr>
    <?php endif; ?>
</table>

<!-- PAGE 5 -->
<div class="page-break"></div>

<h1 class="section-title">ATS Implementation Notes</h1>
<ul class="clean">
    <li>Primary report filters: date range, company, source, status, assigned user.</li>
    <li>Company filter uses <strong>job_list.company_id</strong>.</li>
    <li>Level grouping uses <strong>job_list.experience_range</strong>.</li>
    <li>Business unit grouping uses <strong>job.unit_group</strong>.</li>
    <li>MRF date / turnaround start date uses <strong>job_list.job_posted_date</strong>.</li>
    <li>Workflow history source uses <strong>workflow_histories</strong> filtered by applications feature id.</li>
    <li>Decline reasons use <strong>workflow_histories.remarks</strong> for declined records.</li>
    <li>Close date for time-to-fill uses <strong>MAX(workflow_histories.date_created)</strong> per application record.</li>
</ul>

<h2 class="section-title" style="font-size:15px;">Controller / View Output Checklist</h2>
<table class="report">
    <tr>
        <th>Area</th>
        <th>Expected Content</th>
    </tr>
    <tr>
        <td>Summary KPIs</td>
        <td>Grand total, hired, vacant/open, for review, overdue, offer success, time to fill, days open</td>
    </tr>
    <tr>
        <td>Breakdowns</td>
        <td>Status breakdown, source breakdown, level breakdown, business unit breakdown</td>
    </tr>
    <tr>
        <td>Operations Tables</td>
        <td>Decline reasons, top jobs by applications, TA team performance</td>
    </tr>
    <tr>
        <td>Monitoring Tables</td>
        <td>Overdue applications and recent applications</td>
    </tr>
</table>

<div class="note-box">
    This template is based directly on the uploaded packaged sample report and should be used as the PDF download layout. :contentReference[oaicite:6]{index=6}
</div>

</body>
</html>