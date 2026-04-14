<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1, h2 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        th, td { border: 1px solid #ccc; padding: 6px; }
        th { background: #f5f5f5; }
        .text-right { text-align: right; }
    </style>
</head>
<body>

<h1>Talent Acquisition Report</h1>
<p>Period: <?= esc($dateFrom) ?> to <?= esc($dateTo) ?></p>

<h2>Summary</h2>
<table>
<tr><th>Total Applications</th><td><?= $stats['applications'] ?></td></tr>
<tr><th>Hired</th><td><?= $hiredCount ?></td></tr>
<tr><th>Declined</th><td><?= $declinedCount ?></td></tr>
<tr><th>Failed</th><td><?= $failedCount ?></td></tr>
<tr><th>Withdrawn</th><td><?= $withdrawnCount ?></td></tr>
<tr><th>No Show</th><td><?= $noShowCount ?></td></tr>
</table>

<h2>Offer Metrics</h2>
<table>
<tr><th>Offers</th><td><?= $offerTotal ?></td></tr>
<tr><th>Accepted</th><td><?= $offerAccepted ?></td></tr>
<tr><th>Declined</th><td><?= $offerDeclined ?></td></tr>
<tr><th>Success Rate</th><td><?= $offerSuccessRate ?>%</td></tr>
</table>

<h2>Turnaround</h2>
<table>
<tr><th>Avg Time to Fill</th><td><?= $averageTimeToFill ?> days</td></tr>
<tr><th>Avg Days Open</th><td><?= $averageDaysOpen ?> days</td></tr>
</table>

<h2>Status Breakdown</h2>
<table>
<tr><th>Status</th><th>Count</th></tr>
<?php foreach ($statusBreakdown as $row): ?>
<tr>
<td><?= esc($row['status_name']) ?></td>
<td class="text-right"><?= $row['total_count'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Decline Reasons</h2>
<table>
<tr><th>Reason</th><th>Count</th></tr>
<?php foreach ($declineReasons as $row): ?>
<tr>
<td><?= esc($row['decline_reason']) ?></td>
<td><?= $row['total_count'] ?></td>
</tr>
<?php endforeach; ?>
</table>

<h2>Top Jobs</h2>
<table>
<tr><th>Job</th><th>Applications</th></tr>
<?php foreach ($jobBreakdown as $row): ?>
<tr>
<td><?= esc($row['job_name']) ?></td>
<td><?= $row['total_count'] ?></td>
</tr>
<?php endforeach; ?>
</table>

</body>
</html>