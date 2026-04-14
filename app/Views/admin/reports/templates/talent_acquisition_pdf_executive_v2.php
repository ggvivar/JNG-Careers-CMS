<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Talent Acquisition Operations Report</title>
    <style>
        @page {
            margin: 18px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            color: #1f2937;
            margin: 0;
            background: #ffffff;
        }

        .page {
            page-break-after: always;
            min-height: 100%;
        }

        .page:last-child {
            page-break-after: auto;
        }

        .hero {
            position: relative;
            overflow: hidden;
            min-height: 330px;
            border: 2px solid #153cba;
            background: linear-gradient(180deg, #ffffff 0%, #f9fbff 100%);
            padding: 30px 32px;
        }

        .hero:before {
            content: "";
            position: absolute;
            top: -70px;
            left: -60px;
            width: 210px;
            height: 210px;
            border-radius: 50%;
            background: #f2b400;
        }

        .hero:after {
            content: "";
            position: absolute;
            right: -95px;
            bottom: -95px;
            width: 290px;
            height: 290px;
            border-radius: 50%;
            background: #153cba;
        }

        .hero-inner {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 30px;
            font-weight: 800;
            line-height: 1.02;
            letter-spacing: .02em;
            color: #0f172a;
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }

        .hero-subtitle {
            font-size: 14px;
            color: #334155;
            margin: 0 0 18px 0;
            font-weight: 600;
        }

        .hero-note {
            font-size: 11px;
            color: #475569;
            line-height: 1.7;
            max-width: 440px;
        }

        .brand-box {
            margin-top: 26px;
            display: inline-block;
            background: #ffffff;
            border: 1.5px solid #d9e2f2;
            padding: 12px 14px;
            line-height: 1.5;
            font-size: 12px;
            color: #0f172a;
        }

        .brand-box strong {
            color: #153cba;
        }

        .section-band {
            background: #153cba;
            color: #ffffff;
            padding: 8px 12px;
            margin: 0 0 10px 0;
            font-size: 16px;
            font-weight: 800;
            letter-spacing: .02em;
            text-transform: uppercase;
        }

        .section-copy {
            font-size: 10.5px;
            color: #475569;
            line-height: 1.6;
            margin: 0 0 10px 0;
        }

        .kpi-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px;
            margin: 8px 0 10px 0;
        }

        .kpi-box {
            width: 25%;
            border: 1.5px solid #dbe2ea;
            background: #ffffff;
            padding: 10px 11px;
            vertical-align: top;
        }

        .kpi-label {
            font-size: 8.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: .04em;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }

        .kpi-meta {
            margin-top: 6px;
            font-size: 8.5px;
            color: #64748b;
            line-height: 1.45;
        }

        .success { color: #198754; }
        .danger { color: #dc3545; }
        .warning { color: #d97706; }
        .primary { color: #153cba; }
        .accent { color: #c89200; }

        .subsection-title {
            font-size: 13px;
            font-weight: 800;
            color: #153cba;
            margin: 16px 0 8px 0;
            text-transform: uppercase;
            letter-spacing: .03em;
        }

        table.report {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0 12px 0;
        }

        table.report th,
        table.report td {
            border: 1px solid #dbe2ea;
            padding: 6px 7px;
            vertical-align: top;
        }

        table.report th {
            background: #edf3ff;
            color: #153cba;
            font-size: 9px;
            text-transform: uppercase;
            font-weight: 800;
            letter-spacing: .03em;
        }

        table.report tr:nth-child(even) td {
            background: #fcfdff;
        }

        .right { text-align: right; }

        .note {
            border-left: 5px solid #f2b400;
            background: #fffaf0;
            padding: 9px 10px;
            font-size: 9.5px;
            color: #374151;
            line-height: 1.6;
            margin-top: 6px;
        }

        .footer-note {
            border: 1px solid #dbe2ea;
            background: #f8fafc;
            padding: 10px 12px;
            font-size: 9.5px;
            color: #374151;
            line-height: 1.6;
            margin-top: 10px;
        }

        ul.clean {
            margin: 6px 0 0 18px;
            padding: 0;
        }

        ul.clean li {
            margin-bottom: 5px;
        }

        .small-muted {
            font-size: 9px;
            color: #64748b;
        }

        .table-caption {
            font-size: 9px;
            color: #64748b;
            margin-top: -4px;
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
<div class="page">
    <div class="hero">
        <div class="hero-inner">
            <h1 class="hero-title">Talent Acquisition<br>Operations</h1>
            <div class="hero-subtitle">
                <?= esc(date('F Y', strtotime($dateFrom))) ?> | Sample Report Package
            </div>

            <div class="hero-note">
                Built from the uploaded report sample and ATS implementation notes.<br>
                Prepared for download and internal reference.
            </div>

            <div class="brand-box">
                <strong>Joy-Nostalg</strong><br>
                Talent Acquisition<br>
                Report Mockup + Build Guide
            </div>
        </div>
    </div>

    <div style="margin-top:14px;">
        <div class="section-band">Executive Summary</div>
        <p class="section-copy">
            This downloadable report mirrors the structure and design language of the uploaded sample report while aligning it to the ATS/CMS implementation discussed in the conversation. :contentReference[oaicite:1]{index=1}
        </p>

        <table class="kpi-grid">
            <tr>
                <td class="kpi-box">
                    <div class="kpi-label">Grand Total</div>
                    <div class="kpi-value"><?= number_format($totalApplications) ?></div>
                    <div class="kpi-meta">Total applications</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Hired</div>
                    <div class="kpi-value success"><?= number_format((int) $hiredCount) ?></div>
                    <div class="kpi-meta">Successful application outcomes</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Vacant</div>
                    <div class="kpi-value"><?= number_format($vacantCount) ?></div>
                    <div class="kpi-meta">Open / active pipeline</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">On Review</div>
                    <div class="kpi-value warning"><?= number_format($forReview) ?></div>
                    <div class="kpi-meta">Pending review</div>
                </td>
            </tr>
            <tr>
                <td class="kpi-box">
                    <div class="kpi-label">Offer Success</div>
                    <div class="kpi-value primary"><?= number_format((int) $offerSuccessRate) ?>%</div>
                    <div class="kpi-meta">
                        <?= number_format((int) $offerTotal) ?> offers |
                        <?= number_format((int) $offerAccepted) ?> accepted |
                        <?= number_format((int) $offerDeclined) ?> declined
                    </div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Avg Time to Fill</div>
                    <div class="kpi-value"><?= number_format((int) $averageTimeToFill) ?> days</div>
                    <div class="kpi-meta">Measured from job posted date</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Avg Days Open</div>
                    <div class="kpi-value"><?= number_format((int) $averageDaysOpen) ?> days</div>
                    <div class="kpi-meta">Open roles only</div>
                </td>
                <td class="kpi-box">
                    <div class="kpi-label">Applications Overdue</div>
                    <div class="kpi-value danger"><?= number_format($applicationsOverdue) ?></div>
                    <div class="kpi-meta">Past due date</div>
                </td>
            </tr>
        </table>

        <div class="subsection-title">Status per Level</div>
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

        <div class="small-muted">
            Intended ATS mapping: level = <strong>job_list.experience_range</strong>. :contentReference[oaicite:2]{index=2}
        </div>
    </div>
</div>

<!-- PAGE 2 -->
<div class="page">
    <div class="section-band">Status per Business Unit</div>
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

    <div class="note">
        ATS mapping discussed in the conversation: business unit = <strong>job.unit_group</strong>. :contentReference[oaicite:3]{index=3}
    </div>

    <div class="section-band" style="margin-top:18px;">Offer Success and Decline Reasons</div>
    <table class="report">
        <tr>
            <th>Decline Reason / Description</th>
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

    <div class="note">
        For the generated report, decline reasons should come from
        <strong>workflow_histories.remarks</strong>, filtered by the applications feature and declined statuses. :contentReference[oaicite:4]{index=4}
    </div>
</div>

<!-- PAGE 3 -->
<div class="page">
    <div class="section-band">Turnaround Time and Sourcing Channels</div>
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

    <div class="note">
        MRF date = <strong>job_list.job_posted_date</strong>; time-to-fill and days-open are measured from that field. :contentReference[oaicite:5]{index=5}
    </div>

    <div class="subsection-title">Top Sourcing Channels</div>
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

    <div class="subsection-title">TA Team Performance</div>
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
</div>

<!-- PAGE 4 -->
<div class="page">
    <div class="section-band">Operational Monitoring Tables</div>

    <div class="subsection-title">Overdue Applications</div>
    <table class="report">
        <tr>
            <th>ID</th>
            <th>Applicant</th>
            <th>Company</th>
            <th>Job</th>
            <th>Status</th>
            <th>Processor</th>
            <th>Applied At</th>
            <th>Due At</th>
        </tr>
        <?php if (!empty($overdueApplications)): foreach ($overdueApplications as $row): ?>
        <tr>
            <td>#<?= (int) $row['id'] ?></td>
            <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
            <td><?= esc($row['company_name'] ?: '-') ?></td>
            <td><?= esc($row['job_name'] ?: '-') ?></td>
            <td><?= esc($row['status_name'] ?: '-') ?></td>
            <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
            <td><?= esc($row['applied_at'] ?: '-') ?></td>
            <td><?= esc($row['due_at'] ?: '-') ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="8">No overdue applications found.</td></tr>
        <?php endif; ?>
    </table>

    <div class="subsection-title">Recent Applications</div>
    <table class="report">
        <tr>
            <th>ID</th>
            <th>Applicant</th>
            <th>Email</th>
            <th>Company</th>
            <th>Job</th>
            <th>Status</th>
            <th>Processor</th>
            <th>Applied At</th>
            <th>Due At</th>
            <th>Source</th>
        </tr>
        <?php if (!empty($recentApplications)): foreach ($recentApplications as $row): ?>
        <tr>
            <td>#<?= (int) $row['id'] ?></td>
            <td><?= esc(trim(($row['firstname'] ?? '') . ' ' . ($row['lastname'] ?? '')) ?: '-') ?></td>
            <td><?= esc($row['email'] ?: '-') ?></td>
            <td><?= esc($row['company_name'] ?: '-') ?></td>
            <td><?= esc($row['job_name'] ?: '-') ?></td>
            <td><?= esc($row['status_name'] ?: '-') ?></td>
            <td><?= esc($row['processor_name'] ?: 'Unassigned') ?></td>
            <td><?= esc($row['applied_at'] ?: '-') ?></td>
            <td><?= esc($row['due_at'] ?: '-') ?></td>
            <td><?= esc($row['source'] ?: '-') ?></td>
        </tr>
        <?php endforeach; else: ?>
        <tr><td colspan="10">No applications found.</td></tr>
        <?php endif; ?>
    </table>
</div>

<!-- PAGE 5 -->
<div class="page">
    <div class="section-band">ATS Implementation Notes</div>
    <ul class="clean">
        <li>Primary report filters: date range, company, source, status, assigned user.</li>
        <li>Company filter should use <strong>job_list.company_id</strong>.</li>
        <li>Level grouping should use <strong>job_list.experience_range</strong>.</li>
        <li>Business unit grouping should use <strong>job.unit_group</strong>.</li>
        <li>MRF date / turnaround start date should use <strong>job_list.job_posted_date</strong>.</li>
        <li>Workflow history source should use <strong>workflow_histories</strong> filtered by applications feature id.</li>
        <li>Decline reasons should use <strong>workflow_histories.remarks</strong> for records with declined status.</li>
        <li>Close date for time-to-fill should use <strong>MAX(workflow_histories.date_created)</strong> per application record.</li>
    </ul>

    <div class="section-band" style="margin-top:18px;">Controller / View Output Checklist</div>
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

    <div class="footer-note">
        This document is a packaged downloadable reference built from the uploaded PDF design pattern and implementation instructions shared in the conversation. :contentReference[oaicite:6]{index=6}
    </div>
</div>

</body>
</html>