<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Dompdf\Dompdf;
use Dompdf\Options;
class ReportController extends BaseController
{
    public function talentAcquisitionOld()
    {
        helper(['rbac', 'dropdown']);

        $db = db_connect();
        $adminId = (int) (session()->get('admin_id') ?? 0);
        $applicationsFeatureId = (int) (dd_feature_id('applications') ?? 0);

        $dateFrom   = trim((string) $this->request->getGet('date_from'));
        $dateTo     = trim((string) $this->request->getGet('date_to'));
        $companyId  = trim((string) $this->request->getGet('company_id'));
        $source     = trim((string) $this->request->getGet('source'));
        $statusId   = trim((string) $this->request->getGet('status_id'));
        $assignedTo = trim((string) $this->request->getGet('assigned_to'));

        if ($dateFrom === '') {
            $dateFrom = date('Y-m-01');
        }

        if ($dateTo === '') {
            $dateTo = date('Y-m-t');
        }

        $fromDateTime = $dateFrom . ' 00:00:00';
        $toDateTime   = $dateTo . ' 23:59:59';
        $now          = date('Y-m-d H:i:s');

        $statusOptions = dd_statuses_by_feature('applications');
        $statusIdMap = [];

        foreach ($statusOptions as $sid => $label) {
            $statusIdMap[strtolower(trim((string) $label))] = (int) $sid;
        }

        $hiredStatusNames     = ['hired', 'on-boarding', 'onboarding'];
        $declinedStatusNames  = ['decline', 'declined'];
        $failedStatusNames    = ['failed'];
        $withdrawnStatusNames = ['withdraw', 'withdrawn'];
        $noShowStatusNames    = ['no show'];
        $offerStatusNames     = ['job offer', 'offer'];

        $closedStatusNames = array_merge(
            $hiredStatusNames,
            $declinedStatusNames,
            $failedStatusNames,
            $withdrawnStatusNames,
            $noShowStatusNames
        );

        $closedStatusIds = [];
        $declinedStatusIds = [];
        $offerStatusIds = [];

        foreach ($statusIdMap as $name => $sid) {
            if (in_array($name, $closedStatusNames, true)) {
                $closedStatusIds[] = $sid;
            }

            if (in_array($name, $declinedStatusNames, true)) {
                $declinedStatusIds[] = $sid;
            }

            if (
                in_array($name, $offerStatusNames, true)
                || in_array($name, $hiredStatusNames, true)
                || in_array($name, $declinedStatusNames, true)
            ) {
                $offerStatusIds[] = $sid;
            }
        }

        $closedStatusIds = array_values(array_unique($closedStatusIds));
        $declinedStatusIds = array_values(array_unique($declinedStatusIds));
        $offerStatusIds = array_values(array_unique($offerStatusIds));

        $applyFilters = static function ($builder) use ($fromDateTime, $toDateTime, $companyId, $source, $statusId, $assignedTo) {
            $builder->where('ja.date_deleted', null)
                ->where('ja.applied_at >=', $fromDateTime)
                ->where('ja.applied_at <=', $toDateTime);

            if ($companyId !== '') {
                $builder->where('jl.company_id', (int) $companyId);
            }

            if ($source !== '') {
                $builder->where('ja.source', $source);
            }

            if ($statusId !== '') {
                $builder->where('ja.status_id', (int) $statusId);
            }

            if ($assignedTo !== '') {
                $builder->where('ja.assigned_to', (int) $assignedTo);
            }

            return $builder;
        };

        $stats = [
            'applications'  => 0,
            'my_processing' => 0,
        ];

        $statsForApproval = [
            'applications' => 0,
        ];

        $overdueStats = [
            'applications_overdue' => 0,
        ];

        $myProcessingOverdue = 0;
        $totalApplicants = 0;
        $totalJobs = 0;

        $hiredCount = 0;
        $declinedCount = 0;
        $failedCount = 0;
        $withdrawnCount = 0;
        $noShowCount = 0;

        $offerTotal = 0;
        $offerAccepted = 0;
        $offerDeclined = 0;
        $offerSuccessRate = 0;

        $averageTimeToFill = 0;
        $averageDaysOpen = 0;

        $statusBreakdown = [];
        $sourceBreakdown = [];
        $processorWorkload = [];
        $jobBreakdown = [];
        $recentApplications = [];
        $overdueApplications = [];
        $declineReasons = [];
        $levelBreakdown = [];
        $businessUnitBreakdown = [];
        $taTeamPerformance = [];

        if (rbac_can_feature('applications', 'can_view')) {
            $applicationsBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($applicationsBuilder);
            $stats['applications'] = $applicationsBuilder->countAllResults();

            $myProcessingBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($myProcessingBuilder);
            $myProcessingBuilder->where('ja.assigned_to', $adminId);
            $stats['my_processing'] = $myProcessingBuilder->countAllResults();

            $approvalBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($approvalBuilder);
            $approvalBuilder->where('ja.status_id', 9);
            $statsForApproval['applications'] = $approvalBuilder->countAllResults();

            $uniqueApplicantsRow = $applyFilters(
                $db->table('job_applications ja')
                    ->select('COUNT(DISTINCT ja.applicant_id) AS total_count', false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            )->get()->getRowArray();

            $totalApplicants = (int) ($uniqueApplicantsRow['total_count'] ?? 0);

            $jobsRow = $applyFilters(
                $db->table('job_applications ja')
                    ->select('COUNT(DISTINCT ja.job_list_id) AS total_count', false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            )->get()->getRowArray();

            $totalJobs = (int) ($jobsRow['total_count'] ?? 0);

            $overdueBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($overdueBuilder);
            $overdueBuilder
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', $now);

            $overdueStats['applications_overdue'] = $overdueBuilder->countAllResults();

            $myOverdueBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($myOverdueBuilder);
            $myOverdueBuilder
                ->where('ja.assigned_to', $adminId)
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', $now);

            $myProcessingOverdue = $myOverdueBuilder->countAllResults();

            $statusBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select('s.name AS status_name, ja.status_id, COUNT(ja.id) AS total_count')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
            )
                ->groupBy('ja.status_id, s.name')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($statusBreakdown as $row) {
                $statusName = strtolower(trim((string) ($row['status_name'] ?? '')));
                $count = (int) ($row['total_count'] ?? 0);

                if (in_array($statusName, $hiredStatusNames, true)) {
                    $hiredCount += $count;
                } elseif (in_array($statusName, $declinedStatusNames, true)) {
                    $declinedCount += $count;
                } elseif (in_array($statusName, $failedStatusNames, true)) {
                    $failedCount += $count;
                } elseif (in_array($statusName, $withdrawnStatusNames, true)) {
                    $withdrawnCount += $count;
                } elseif (in_array($statusName, $noShowStatusNames, true)) {
                    $noShowCount += $count;
                }
            }

            $sourceBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        CASE
                            WHEN ja.source IS NULL OR ja.source = '' THEN 'Unspecified'
                            ELSE ja.source
                        END AS source_name,
                        COUNT(ja.id) AS total_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            )
                ->groupBy('source_name')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $processorWorkload = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(u.name, 'Unassigned') AS processor_name,
                        ja.assigned_to,
                        COUNT(ja.id) AS total_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
            )
                ->groupBy('ja.assigned_to, u.name')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $jobBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select('j.name AS job_name, COUNT(ja.id) AS total_count')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
            )
                ->groupBy('ja.job_list_id, j.name')
                ->orderBy('total_count', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $recentApplications = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        ja.id,
                        ja.source,
                        ja.applied_at,
                        ja.due_at,
                        a.firstname,
                        a.lastname,
                        a.email,
                        c.name AS company_name,
                        j.name AS job_name,
                        s.name AS status_name,
                        u.name AS processor_name
                    ")
                    ->join('applicants a', 'a.id = ja.applicant_id', 'left')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('companies c', 'c.id = jl.company_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
            )
                ->orderBy('ja.applied_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $overdueApplications = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        ja.id,
                        ja.due_at,
                        ja.applied_at,
                        a.firstname,
                        a.lastname,
                        c.name AS company_name,
                        j.name AS job_name,
                        s.name AS status_name,
                        u.name AS processor_name
                    ")
                    ->join('applicants a', 'a.id = ja.applicant_id', 'left')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('companies c', 'c.id = jl.company_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
            )
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', $now)
                ->orderBy('ja.due_at', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $offerBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($offerBuilder);

            if (!empty($offerStatusIds)) {
                $offerBuilder->whereIn('ja.status_id', $offerStatusIds);
            } else {
                $offerBuilder->where('1 = 0', null, false);
            }

            $offerTotal = $offerBuilder->countAllResults();
            $offerAccepted = $hiredCount;
            $offerDeclined = $declinedCount;
            $offerSuccessRate = $offerTotal > 0 ? (int) round(($offerAccepted / $offerTotal) * 100) : 0;

            $closedTatBuilder = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        ja.id,
                        jl.job_posted_date,
                        MAX(wh.date_created) AS closed_at
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join(
                        'workflow_histories wh',
                        'wh.record_id = ja.id
                         AND wh.feature_id = ' . $applicationsFeatureId . '
                         AND wh.date_deleted IS NULL',
                        'left'
                    )
            );

            if (!empty($closedStatusIds)) {
                $closedTatBuilder->whereIn('ja.status_id', $closedStatusIds);
            } else {
                $closedTatBuilder->where('1 = 0', null, false);
            }

            $closedTatRows = $closedTatBuilder
                ->where('jl.job_posted_date IS NOT NULL', null, false)
                ->groupBy('ja.id, jl.job_posted_date')
                ->get()
                ->getResultArray();

            $tatDays = [];
            foreach ($closedTatRows as $row) {
                if (!empty($row['job_posted_date']) && !empty($row['closed_at'])) {
                    $diff = floor((strtotime($row['closed_at']) - strtotime($row['job_posted_date'])) / 86400);
                    if ($diff >= 0) {
                        $tatDays[] = $diff;
                    }
                }
            }

            $averageTimeToFill = !empty($tatDays)
                ? (int) round(array_sum($tatDays) / count($tatDays))
                : 0;

            $openBuilder = $applyFilters(
                $db->table('job_applications ja')
                    ->select('ja.id, jl.job_posted_date, ja.status_id')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            );

            if (!empty($closedStatusIds)) {
                $openBuilder->whereNotIn('ja.status_id', $closedStatusIds);
            }

            $openRows = $openBuilder
                ->where('jl.job_posted_date IS NOT NULL', null, false)
                ->get()
                ->getResultArray();

            $daysOpenValues = [];
            foreach ($openRows as $row) {
                if (!empty($row['job_posted_date'])) {
                    $diff = floor((strtotime($now) - strtotime($row['job_posted_date'])) / 86400);
                    if ($diff >= 0) {
                        $daysOpenValues[] = $diff;
                    }
                }
            }

            $averageDaysOpen = !empty($daysOpenValues)
                ? (int) round(array_sum($daysOpenValues) / count($daysOpenValues))
                : 0;

            $declineReasonBuilder = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        TRIM(COALESCE(NULLIF(wh.remarks, ''), 'No reason shared')) AS decline_reason,
                        COUNT(DISTINCT ja.id) AS total_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join(
                        'workflow_histories wh',
                        'wh.record_id = ja.id
                         AND wh.feature_id = ' . $applicationsFeatureId . '
                         AND wh.date_deleted IS NULL',
                        'left'
                    )
            );

            if (!empty($declinedStatusIds)) {
                $declineReasonBuilder->whereIn('ja.status_id', $declinedStatusIds);
            } else {
                $declineReasonBuilder->where('1 = 0', null, false);
            }

            $declineReasons = $declineReasonBuilder
                ->groupBy("TRIM(COALESCE(NULLIF(wh.remarks, ''), 'No reason shared'))")
                ->orderBy('total_count', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $levelBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(NULLIF(jl.experience_range, ''), 'Unspecified') AS level_name,
                        COUNT(ja.id) AS total_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('hired','on-boarding','onboarding') THEN 1 ELSE 0 END) AS hired_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS closed_unsuccessful_count,
                        SUM(CASE WHEN LOWER(s.name) NOT IN ('hired','on-boarding','onboarding','decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS open_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
            )
                ->groupBy("COALESCE(NULLIF(jl.experience_range, ''), 'Unspecified')")
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $businessUnitBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(NULLIF(j.group_id, ''), 'Unspecified') AS business_unit_name,
                        COUNT(ja.id) AS total_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('hired','on-boarding','onboarding') THEN 1 ELSE 0 END) AS hired_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS closed_unsuccessful_count,
                        SUM(CASE WHEN LOWER(s.name) NOT IN ('hired','on-boarding','onboarding','decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS open_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
            )
                ->groupBy("COALESCE(NULLIF(j.group_id, ''), 'Unspecified')")
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $taTeamPerformance = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(u.name, 'Unassigned') AS recruiter_name,
                        ja.assigned_to,
                        SUM(CASE WHEN LOWER(s.name) IN ('hired','on-boarding','onboarding') THEN 1 ELSE 0 END) AS hires_count,
                        COUNT(ja.id) AS total_count,
                        AVG(CASE
                            WHEN LOWER(s.name) IN ('hired','on-boarding','onboarding','decline','declined','failed','withdraw','withdrawn','no show')
                                 AND jl.job_posted_date IS NOT NULL
                            THEN GREATEST(DATEDIFF(COALESCE(whmax.closed_at, NOW()), jl.job_posted_date), 0)
                            ELSE NULL
                        END) AS avg_time_to_fill,
                        AVG(CASE
                            WHEN LOWER(s.name) NOT IN ('hired','on-boarding','onboarding','decline','declined','failed','withdraw','withdrawn','no show')
                                 AND jl.job_posted_date IS NOT NULL
                            THEN GREATEST(DATEDIFF(NOW(), jl.job_posted_date), 0)
                            ELSE NULL
                        END) AS avg_days_open
                    ", false)
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join("
                        (
                            SELECT record_id, MAX(date_created) AS closed_at
                            FROM workflow_histories
                            WHERE feature_id = {$applicationsFeatureId}
                              AND date_deleted IS NULL
                            GROUP BY record_id
                        ) whmax
                    ", 'whmax.record_id = ja.id', 'left')
            )
                ->groupBy('ja.assigned_to, u.name')
                ->orderBy('hires_count', 'DESC')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($taTeamPerformance as &$row) {
                $row['avg_time_to_fill'] = $row['avg_time_to_fill'] !== null ? (int) round((float) $row['avg_time_to_fill']) : 0;
                $row['avg_days_open'] = $row['avg_days_open'] !== null ? (int) round((float) $row['avg_days_open']) : 0;
            }
            unset($row);
        }

        return view('admin/reports/talent_acquisition', [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'selectedCompanyId' => $companyId,
            'selectedSource' => $source,
            'selectedStatusId' => $statusId,
            'selectedAssignedTo' => $assignedTo,

            'stats' => $stats,
            'statsForApproval' => $statsForApproval,
            'overdueStats' => $overdueStats,
            'myProcessingOverdue' => $myProcessingOverdue,

            'totalApplicants' => $totalApplicants,
            'totalJobs' => $totalJobs,

            'hiredCount' => $hiredCount,
            'declinedCount' => $declinedCount,
            'failedCount' => $failedCount,
            'withdrawnCount' => $withdrawnCount,
            'noShowCount' => $noShowCount,

            'offerTotal' => $offerTotal,
            'offerAccepted' => $offerAccepted,
            'offerDeclined' => $offerDeclined,
            'offerSuccessRate' => $offerSuccessRate,

            'averageTimeToFill' => $averageTimeToFill,
            'averageDaysOpen' => $averageDaysOpen,

            'statusBreakdown' => $statusBreakdown,
            'sourceBreakdown' => $sourceBreakdown,
            'processorWorkload' => $processorWorkload,
            'jobBreakdown' => $jobBreakdown,
            'recentApplications' => $recentApplications,
            'overdueApplications' => $overdueApplications,
            'declineReasons' => $declineReasons,
            'levelBreakdown' => $levelBreakdown,
            'businessUnitBreakdown' => $businessUnitBreakdown,
            'taTeamPerformance' => $taTeamPerformance,

            'companyOptions' => dd_options('companies', 'id', 'name', [], ['name' => 'ASC']),
            'sourceOptions' => dd_common_defaults('Source'),
            'statusOptions' => $statusOptions,
            'assignedUserOptions' => dd_options('users', 'id', 'name', [], ['name' => 'ASC']),
        ]);
    }

   public function downloadTalentAcquisitionPdf()
{
    $data = $this->talentAcquisitionData();

    $html = view('admin/reports/templates/talent_acquisition_pdf_executive', $data);

    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();

    $filename = 'Talent_Acquisition_Operations_Report_' . date('Ymd_His') . '.pdf';

    return $this->response
    ->setHeader('Content-Type', 'application/pdf')
    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
    ->setBody($dompdf->output());
}
private function talentAcquisitionData()
{
helper(['rbac', 'dropdown']);

        $db = db_connect();
        $adminId = (int) (session()->get('admin_id') ?? 0);
        $applicationsFeatureId = (int) (dd_feature_id('applications') ?? 0);

        $dateFrom   = trim((string) $this->request->getGet('date_from'));
        $dateTo     = trim((string) $this->request->getGet('date_to'));
        $companyId  = trim((string) $this->request->getGet('company_id'));
        $source     = trim((string) $this->request->getGet('source'));
        $statusId   = trim((string) $this->request->getGet('status_id'));
        $assignedTo = trim((string) $this->request->getGet('assigned_to'));

        if ($dateFrom === '') {
            $dateFrom = date('Y-m-01');
        }

        if ($dateTo === '') {
            $dateTo = date('Y-m-t');
        }

        $fromDateTime = $dateFrom . ' 00:00:00';
        $toDateTime   = $dateTo . ' 23:59:59';
        $now          = date('Y-m-d H:i:s');

        $statusOptions = dd_statuses_by_feature('applications');
        $statusIdMap = [];

        foreach ($statusOptions as $sid => $label) {
            $statusIdMap[strtolower(trim((string) $label))] = (int) $sid;
        }

        $hiredStatusNames     = ['hired', 'on-boarding', 'onboarding'];
        $declinedStatusNames  = ['decline', 'declined'];
        $failedStatusNames    = ['failed'];
        $withdrawnStatusNames = ['withdraw', 'withdrawn'];
        $noShowStatusNames    = ['no show'];
        $offerStatusNames     = ['job offer', 'offer'];

        $closedStatusNames = array_merge(
            $hiredStatusNames,
            $declinedStatusNames,
            $failedStatusNames,
            $withdrawnStatusNames,
            $noShowStatusNames
        );

        $closedStatusIds = [];
        $declinedStatusIds = [];
        $offerStatusIds = [];

        foreach ($statusIdMap as $name => $sid) {
            if (in_array($name, $closedStatusNames, true)) {
                $closedStatusIds[] = $sid;
            }

            if (in_array($name, $declinedStatusNames, true)) {
                $declinedStatusIds[] = $sid;
            }

            if (
                in_array($name, $offerStatusNames, true)
                || in_array($name, $hiredStatusNames, true)
                || in_array($name, $declinedStatusNames, true)
            ) {
                $offerStatusIds[] = $sid;
            }
        }

        $closedStatusIds = array_values(array_unique($closedStatusIds));
        $declinedStatusIds = array_values(array_unique($declinedStatusIds));
        $offerStatusIds = array_values(array_unique($offerStatusIds));

        $applyFilters = static function ($builder) use ($fromDateTime, $toDateTime, $companyId, $source, $statusId, $assignedTo) {
            $builder->where('ja.date_deleted', null)
                ->where('ja.applied_at >=', $fromDateTime)
                ->where('ja.applied_at <=', $toDateTime);

            if ($companyId !== '') {
                $builder->where('jl.company_id', (int) $companyId);
            }

            if ($source !== '') {
                $builder->where('ja.source', $source);
            }

            if ($statusId !== '') {
                $builder->where('ja.status_id', (int) $statusId);
            }

            if ($assignedTo !== '') {
                $builder->where('ja.assigned_to', (int) $assignedTo);
            }

            return $builder;
        };

        $stats = [
            'applications'  => 0,
            'my_processing' => 0,
        ];

        $statsForApproval = [
            'applications' => 0,
        ];

        $overdueStats = [
            'applications_overdue' => 0,
        ];

        $myProcessingOverdue = 0;
        $totalApplicants = 0;
        $totalJobs = 0;

        $hiredCount = 0;
        $declinedCount = 0;
        $failedCount = 0;
        $withdrawnCount = 0;
        $noShowCount = 0;

        $offerTotal = 0;
        $offerAccepted = 0;
        $offerDeclined = 0;
        $offerSuccessRate = 0;

        $averageTimeToFill = 0;
        $averageDaysOpen = 0;

        $statusBreakdown = [];
        $sourceBreakdown = [];
        $processorWorkload = [];
        $jobBreakdown = [];
        $recentApplications = [];
        $overdueApplications = [];
        $declineReasons = [];
        $levelBreakdown = [];
        $businessUnitBreakdown = [];
        $taTeamPerformance = [];

        if (rbac_can_feature('applications', 'can_view')) {
            $applicationsBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($applicationsBuilder);
            $stats['applications'] = $applicationsBuilder->countAllResults();

            $myProcessingBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($myProcessingBuilder);
            $myProcessingBuilder->where('ja.assigned_to', $adminId);
            $stats['my_processing'] = $myProcessingBuilder->countAllResults();

            $approvalBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($approvalBuilder);
            $approvalBuilder->where('ja.status_id', 9);
            $statsForApproval['applications'] = $approvalBuilder->countAllResults();

            $uniqueApplicantsRow = $applyFilters(
                $db->table('job_applications ja')
                    ->select('COUNT(DISTINCT ja.applicant_id) AS total_count', false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            )->get()->getRowArray();

            $totalApplicants = (int) ($uniqueApplicantsRow['total_count'] ?? 0);

            $jobsRow = $applyFilters(
                $db->table('job_applications ja')
                    ->select('COUNT(DISTINCT ja.job_list_id) AS total_count', false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            )->get()->getRowArray();

            $totalJobs = (int) ($jobsRow['total_count'] ?? 0);

            $overdueBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($overdueBuilder);
            $overdueBuilder
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', $now);

            $overdueStats['applications_overdue'] = $overdueBuilder->countAllResults();

            $myOverdueBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($myOverdueBuilder);
            $myOverdueBuilder
                ->where('ja.assigned_to', $adminId)
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', $now);

            $myProcessingOverdue = $myOverdueBuilder->countAllResults();

            $statusBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select('s.name AS status_name, ja.status_id, COUNT(ja.id) AS total_count')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
            )
                ->groupBy('ja.status_id, s.name')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            foreach ($statusBreakdown as $row) {
                $statusName = strtolower(trim((string) ($row['status_name'] ?? '')));
                $count = (int) ($row['total_count'] ?? 0);

                if (in_array($statusName, $hiredStatusNames, true)) {
                    $hiredCount += $count;
                } elseif (in_array($statusName, $declinedStatusNames, true)) {
                    $declinedCount += $count;
                } elseif (in_array($statusName, $failedStatusNames, true)) {
                    $failedCount += $count;
                } elseif (in_array($statusName, $withdrawnStatusNames, true)) {
                    $withdrawnCount += $count;
                } elseif (in_array($statusName, $noShowStatusNames, true)) {
                    $noShowCount += $count;
                }
            }

            $sourceBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        CASE
                            WHEN ja.source IS NULL OR ja.source = '' THEN 'Unspecified'
                            ELSE ja.source
                        END AS source_name,
                        COUNT(ja.id) AS total_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            )
                ->groupBy('source_name')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $processorWorkload = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(u.name, 'Unassigned') AS processor_name,
                        ja.assigned_to,
                        COUNT(ja.id) AS total_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
            )
                ->groupBy('ja.assigned_to, u.name')
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $jobBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select('j.name AS job_name, COUNT(ja.id) AS total_count')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
            )
                ->groupBy('ja.job_list_id, j.name')
                ->orderBy('total_count', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $recentApplications = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        ja.id,
                        ja.source,
                        ja.applied_at,
                        ja.due_at,
                        a.firstname,
                        a.lastname,
                        a.email,
                        c.name AS company_name,
                        j.name AS job_name,
                        s.name AS status_name,
                        u.name AS processor_name
                    ")
                    ->join('applicants a', 'a.id = ja.applicant_id', 'left')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('companies c', 'c.id = jl.company_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
            )
                ->orderBy('ja.applied_at', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $overdueApplications = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        ja.id,
                        ja.due_at,
                        ja.applied_at,
                        a.firstname,
                        a.lastname,
                        c.name AS company_name,
                        j.name AS job_name,
                        s.name AS status_name,
                        u.name AS processor_name
                    ")
                    ->join('applicants a', 'a.id = ja.applicant_id', 'left')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('companies c', 'c.id = jl.company_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
                    ->join('users u', 'u.id = ja.assigned_to', 'left')
            )
                ->where('ja.due_at IS NOT NULL', null, false)
                ->where('ja.due_at <', $now)
                ->orderBy('ja.due_at', 'ASC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $offerBuilder = $db->table('job_applications ja')
                ->join('job_list jl', 'jl.id = ja.job_list_id', 'left');
            $applyFilters($offerBuilder);

            if (!empty($offerStatusIds)) {
                $offerBuilder->whereIn('ja.status_id', $offerStatusIds);
            } else {
                $offerBuilder->where('1 = 0', null, false);
            }

            $offerTotal = $offerBuilder->countAllResults();
            $offerAccepted = $hiredCount;
            $offerDeclined = $declinedCount;
            $offerSuccessRate = $offerTotal > 0 ? (int) round(($offerAccepted / $offerTotal) * 100) : 0;

            $closedTatBuilder = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        ja.id,
                        jl.job_posted_date,
                        MAX(wh.date_created) AS closed_at
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join(
                        'workflow_histories wh',
                        'wh.record_id = ja.id
                         AND wh.feature_id = ' . $applicationsFeatureId . '
                         AND wh.date_deleted IS NULL',
                        'left'
                    )
            );

            if (!empty($closedStatusIds)) {
                $closedTatBuilder->whereIn('ja.status_id', $closedStatusIds);
            } else {
                $closedTatBuilder->where('1 = 0', null, false);
            }

            $closedTatRows = $closedTatBuilder
                ->where('jl.job_posted_date IS NOT NULL', null, false)
                ->groupBy('ja.id, jl.job_posted_date')
                ->get()
                ->getResultArray();

            $tatDays = [];
            foreach ($closedTatRows as $row) {
                if (!empty($row['job_posted_date']) && !empty($row['closed_at'])) {
                    $diff = floor((strtotime($row['closed_at']) - strtotime($row['job_posted_date'])) / 86400);
                    if ($diff >= 0) {
                        $tatDays[] = $diff;
                    }
                }
            }

            $averageTimeToFill = !empty($tatDays)
                ? (int) round(array_sum($tatDays) / count($tatDays))
                : 0;

            $openBuilder = $applyFilters(
                $db->table('job_applications ja')
                    ->select('ja.id, jl.job_posted_date, ja.status_id')
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
            );

            if (!empty($closedStatusIds)) {
                $openBuilder->whereNotIn('ja.status_id', $closedStatusIds);
            }

            $openRows = $openBuilder
                ->where('jl.job_posted_date IS NOT NULL', null, false)
                ->get()
                ->getResultArray();

            $daysOpenValues = [];
            foreach ($openRows as $row) {
                if (!empty($row['job_posted_date'])) {
                    $diff = floor((strtotime($now) - strtotime($row['job_posted_date'])) / 86400);
                    if ($diff >= 0) {
                        $daysOpenValues[] = $diff;
                    }
                }
            }

            $averageDaysOpen = !empty($daysOpenValues)
                ? (int) round(array_sum($daysOpenValues) / count($daysOpenValues))
                : 0;

            $declineReasonBuilder = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        TRIM(COALESCE(NULLIF(wh.remarks, ''), 'No reason shared')) AS decline_reason,
                        COUNT(DISTINCT ja.id) AS total_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join(
                        'workflow_histories wh',
                        'wh.record_id = ja.id
                         AND wh.feature_id = ' . $applicationsFeatureId . '
                         AND wh.date_deleted IS NULL',
                        'left'
                    )
            );

            if (!empty($declinedStatusIds)) {
                $declineReasonBuilder->whereIn('ja.status_id', $declinedStatusIds);
            } else {
                $declineReasonBuilder->where('1 = 0', null, false);
            }

            $declineReasons = $declineReasonBuilder
                ->groupBy("TRIM(COALESCE(NULLIF(wh.remarks, ''), 'No reason shared'))")
                ->orderBy('total_count', 'DESC')
                ->limit(10)
                ->get()
                ->getResultArray();

            $levelBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(NULLIF(jl.experience_range, ''), 'Unspecified') AS level_name,
                        COUNT(ja.id) AS total_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('hired','on-boarding','onboarding') THEN 1 ELSE 0 END) AS hired_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS closed_unsuccessful_count,
                        SUM(CASE WHEN LOWER(s.name) NOT IN ('hired','on-boarding','onboarding','decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS open_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
            )
                ->groupBy("COALESCE(NULLIF(jl.experience_range, ''), 'Unspecified')")
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();

            $businessUnitBreakdown = $applyFilters(
                $db->table('job_applications ja')
                    ->select("
                        COALESCE(NULLIF(j.group_id, ''), 'Unspecified') AS business_unit_name,
                        COUNT(ja.id) AS total_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('hired','on-boarding','onboarding') THEN 1 ELSE 0 END) AS hired_count,
                        SUM(CASE WHEN LOWER(s.name) IN ('decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS closed_unsuccessful_count,
                        SUM(CASE WHEN LOWER(s.name) NOT IN ('hired','on-boarding','onboarding','decline','declined','failed','withdraw','withdrawn','no show') THEN 1 ELSE 0 END) AS open_count
                    ", false)
                    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')
                    ->join('job j', 'j.id = jl.job_id', 'left')
                    ->join('status s', 's.id = ja.status_id', 'left')
            )
                ->groupBy("COALESCE(NULLIF(j.group_id, ''), 'Unspecified')")
                ->orderBy('total_count', 'DESC')
                ->get()
                ->getResultArray();
$hiredStatusId = (int) db_connect()
    ->table('status')
    ->where('name', 'Hired')
    ->get()
    ->getRow('id');
            $taTeamPerformance = $db->table('job_applications ja')
    ->select("
        u.name as recruiter_name,
        COUNT(ja.id) as total_count,

        SUM(CASE WHEN ja.status_id = {$hiredStatusId} THEN 1 ELSE 0 END) as hires_count,

        AVG(
            CASE 
                WHEN first_hired.first_hired_at IS NOT NULL 
                THEN TIMESTAMPDIFF(DAY, jl.job_posted_date, first_hired.first_hired_at)
            END
        ) as avg_time_to_fill,

        AVG(
            CASE 
                WHEN ja.status_id != {$hiredStatusId}
                THEN TIMESTAMPDIFF(DAY, jl.job_posted_date, '{$now}')
            END
        ) as avg_days_open
    ")
    ->join('users u', 'u.id = ja.assigned_to', 'left')
    ->join('job_list jl', 'jl.id = ja.job_list_id', 'left')

    // 🔥 THIS IS THE FIX
    ->join("(
        SELECT 
            wh.record_id,
            MIN(wh.date_created) as first_hired_at
        FROM workflow_histories wh
        WHERE wh.feature_id = {$applicationsFeatureId}
        AND wh.status_id_to = {$hiredStatusId}
        GROUP BY wh.record_id
    ) as first_hired", 'first_hired.record_id = ja.id', 'left')

    ->where('ja.date_deleted', null)

    // filters
    ->where('ja.applied_at >=', $fromDateTime)
    ->where('ja.applied_at <=', $toDateTime)

    ->groupBy('ja.assigned_to')
    ->get()
    ->getResultArray();

            foreach ($taTeamPerformance as &$row) {
                $row['avg_time_to_fill'] = $row['avg_time_to_fill'] !== null ? (int) round((float) $row['avg_time_to_fill']) : 0;
                $row['avg_days_open'] = $row['avg_days_open'] !== null ? (int) round((float) $row['avg_days_open']) : 0;
            }
            unset($row);
        }

   return [
        'dateFrom' => $dateFrom,
        'dateTo' => $dateTo,
        'stats' => $stats,
        'hiredCount' => $hiredCount,
        'declinedCount' => $declinedCount,
        'failedCount' => $failedCount,
        'withdrawnCount' => $withdrawnCount,
        'noShowCount' => $noShowCount,
        'offerTotal' => $offerTotal,
        'offerAccepted' => $offerAccepted,
        'offerDeclined' => $offerDeclined,
        'offerSuccessRate' => $offerSuccessRate,
        'averageTimeToFill' => $averageTimeToFill,
        'averageDaysOpen' => $averageDaysOpen,
        'statusBreakdown' => $statusBreakdown,
        'sourceBreakdown' => $sourceBreakdown,
        'jobBreakdown' => $jobBreakdown,
        'declineReasons' => $declineReasons,
        'levelBreakdown' => $levelBreakdown,
        'businessUnitBreakdown' => $businessUnitBreakdown,
        'taTeamPerformance' => $taTeamPerformance,
        'recentApplications' => $recentApplications,
        'overdueApplications' => $overdueApplications,
        
            'companyOptions' => dd_options('companies', 'id', 'name', [], ['name' => 'ASC']),
            'sourceOptions' => dd_common_defaults('Source'),
            'statusOptions' => $statusOptions,
            'assignedUserOptions' => dd_options('users', 'id', 'name', [], ['name' => 'ASC']),
    ];
 
}
public function talentAcquisition()
{
    return view('admin/reports/talent_acquisition', $this->talentAcquisitionData());
}
}