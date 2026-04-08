<?php

namespace App\Libraries;

use App\Controllers\Api\NotificationApiController;

class Workflow
{
    protected string $featureCode;
    protected ?int $featureId = null;

    public function __construct(string $featureCode)
    {
        helper('dropdown');
        $this->featureCode = $featureCode;
        $this->featureId = dd_feature_id($featureCode);
    }

    public function getFeatureCode(): string
    {
        return $this->featureCode;
    }

    public function getFeatureId(): ?int
    {
        return $this->featureId;
    }

    public function getStatuses(): array
    {
        return dd_statuses_by_feature($this->featureCode);
    }

    public function getNextStatuses(?int $fromStatusId): array
    {
        return dd_next_statuses($this->featureCode, $fromStatusId);
    }

    public function getTransition(?int $fromStatusId, int $toStatusId): ?array
    {
        return dd_workflow_transition($this->featureCode, $fromStatusId, $toStatusId);
    }

    public function getDueAt(?int $days): ?string
    {
        return dd_workflow_due_at($days);
    }

    // public function validateTransition(int $fromStatusId, int $toStatusId, string $remarks = ''): array
    // {
    //     $transition = $this->getTransition($fromStatusId, $toStatusId);

    //     if (! $transition) {
    //         return [
    //             'ok' => false,
    //             'message' => 'Invalid workflow transition.',
    //             'transition' => null,
    //         ];
    //     }

    //     if (! empty($transition['require_remarks']) && trim($remarks) === '') {
    //         return [
    //             'ok' => false,
    //             'message' => 'Remarks are required for this transition.',
    //             'transition' => $transition,
    //         ];
    //     }

    //     if (! empty($transition['send_email']) && empty($transition['email_template_key'])) {
    //         return [
    //             'ok' => false,
    //             'message' => 'Email template is required for this transition.',
    //             'transition' => $transition,
    //         ];
    //     }

    //     return [
    //         'ok' => true,
    //         'message' => null,
    //         'transition' => $transition,
    //     ];
    // }
    public function validateTransition(?int $fromStatusId, int $toStatusId, string $remarks = ''): array
    {
        $transition = $this->getTransition($fromStatusId, $toStatusId);

        if (! $transition) {
            return [
                'ok' => false,
                'message' => 'Invalid workflow transition.',
                'transition' => null,
            ];
        }

        if (! empty($transition['require_remarks']) && trim($remarks) === '') {
            return [
                'ok' => false,
                'message' => 'Remarks are required for this transition.',
                'transition' => $transition,
            ];
        }

        if (! empty($transition['send_email']) && empty($transition['email_template_key'])) {
            return [
                'ok' => false,
                'message' => 'Email template is required for this transition.',
                'transition' => $transition,
            ];
        }

        return [
            'ok' => true,
            'message' => null,
            'transition' => $transition,
        ];
    }
    public function log(
        int $recordId,
        ?int $fromStatusId,
        ?int $toStatusId,
        ?int $assignedFromUserId,
        ?int $assignedToUserId,
        ?string $remarks,
        ?int $changedBy,
        ?string $dueAt
    ): void {
        if (! $this->featureId || $recordId <= 0) {
            return;
        }

        db_connect()->table('workflow_histories')->insert([
            'feature_id' => $this->featureId,
            'record_id' => $recordId,
            'status_id_from' => $fromStatusId,
            'status_id_to' => $toStatusId,
            'assigned_from' => $assignedFromUserId,
            'assigned_to' => $assignedToUserId,
            'remarks' => $remarks,
            'change_by' => $changedBy,
            'due_at' => $dueAt,
            'date_created' => date('Y-m-d H:i:s'),
        ]);
    }

    public function history(int $recordId): array
    {
        if (! $this->featureId || $recordId <= 0) {
            return [];
        }

        return db_connect()->table('workflow_histories wh')
            ->select('
                wh.*,
                fs.name as from_status_name,
                ts.name as to_status_name,
                af.name as assigned_from_name,
                at.name as assigned_to_name,
                cb.name as changed_by_name
            ')
            ->join('status fs', 'fs.id = wh.status_id_from', 'left')
            ->join('status ts', 'ts.id = wh.status_id_to', 'left')
            ->join('users af', 'af.id = wh.assigned_from', 'left')
            ->join('users at', 'at.id = wh.assigned_to', 'left')
            ->join('users cb', 'cb.id = wh.change_by', 'left')
            ->where('wh.feature_id', $this->featureId)
            ->where('wh.record_id', $recordId)
            ->orderBy('wh.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    public function sendTransitionEmail(?string $to, array $transition, array $params = []): array
    {
        if (empty($to)) {
            return [
                'status' => 'skipped',
                'message' => 'No recipient email.',
            ];
        }

        if (empty($transition['send_email'])) {
            return [
                'status' => 'skipped',
                'message' => 'Email sending disabled for this transition.',
            ];
        }

        if (empty($transition['email_template_key'])) {
            return [
                'status' => 'skipped',
                'message' => 'No email template configured for this transition.',
            ];
        }

        $payload = array_merge($params, [
            'template' => $transition['email_template_key'],
        ]);

        return NotificationApiController::send($to, $payload);
    }

    public function processTransition(array $options): array
    {
        $recordId = (int) ($options['record_id'] ?? 0);
        $fromStatusId = isset($options['status_id_from']) ? (int) $options['status_id_from'] : 0;
        $toStatusId = isset($options['status_id_to']) ? (int) $options['status_id_to'] : 0;
        $remarks = trim((string) ($options['remarks'] ?? ''));
        $assignedFromUserId = isset($options['assigned_from_user_id']) ? (int) $options['assigned_from_user_id'] : null;
        $assignedToUserId = isset($options['assigned_to_user_id']) ? (int) $options['assigned_to_user_id'] : null;
        $changedBy = isset($options['changed_by']) ? (int) $options['changed_by'] : null;
        $emailTo = $options['email_to'] ?? null;
        $emailParams = is_array($options['email_params'] ?? null) ? $options['email_params'] : [];

        $validation = $this->validateTransition($fromStatusId, $toStatusId, $remarks);

        if (! $validation['ok']) {
            return [
                'ok' => false,
                'message' => $validation['message'],
                'transition' => null,
                'due_at' => null,
                'email' => null,
            ];
        }

        $transition = $validation['transition'];
        $dueAt = $this->getDueAt(
            isset($transition['grace_period']) ? (int) $transition['grace_period'] : null
        );

        $this->log(
            $recordId,
            $fromStatusId,
            $toStatusId,
            $assignedFromUserId,
            $assignedToUserId,
            $remarks !== '' ? $remarks : 'Workflow updated',
            $changedBy,
            $dueAt
        );

        $emailResult = $this->sendTransitionEmail($emailTo, $transition, $emailParams);

        return [
            'ok' => true,
            'message' => null,
            'transition' => $transition,
            'due_at' => $dueAt,
            'email' => $emailResult,
        ];
    }
}