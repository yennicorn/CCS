<?php

namespace App\Support;

final class ApplicationStatusReasoner
{
    public const PENDING = 'pending';
    public const REVIEWED = 'reviewed';
    public const APPROVED = 'approved';
    public const REJECTED = 'rejected';
    public const WAITLISTED = 'waitlisted';

    /**
     * @var array<string, array<int, string>>
     */
    private const FORWARD_TRANSITIONS = [
        self::PENDING => [self::REVIEWED],
        self::REVIEWED => [self::APPROVED, self::REJECTED, self::WAITLISTED],
        self::APPROVED => [],
        self::REJECTED => [],
        self::WAITLISTED => [],
    ];

    /**
     * @var array<int, string>
     */
    private const FINAL_DECISION_STATUSES = [
        self::APPROVED,
        self::REJECTED,
        self::WAITLISTED,
    ];

    /**
     * @return array<int, string>
     */
    public static function allowedNextStatuses(string $currentStatus): array
    {
        return self::FORWARD_TRANSITIONS[$currentStatus] ?? [];
    }

    public static function canTransition(string $currentStatus, string $nextStatus): bool
    {
        return in_array($nextStatus, self::allowedNextStatuses($currentStatus), true);
    }

    /**
     * @return array<int, string>
     */
    public static function allowedFinalDecisionStatuses(string $currentStatus): array
    {
        return array_values(array_filter(
            self::allowedNextStatuses($currentStatus),
            static fn (string $status): bool => in_array($status, self::FINAL_DECISION_STATUSES, true)
        ));
    }

    public static function canFinalizeTo(string $currentStatus, string $targetStatus): bool
    {
        return in_array($targetStatus, self::allowedFinalDecisionStatuses($currentStatus), true);
    }

    public static function canBeReviewedByAdmin(string $currentStatus): bool
    {
        return self::canTransition($currentStatus, self::REVIEWED);
    }

    public static function canBeEditedByApplicant(string $currentStatus): bool
    {
        return self::canBeReviewedByAdmin($currentStatus);
    }

    public static function invalidTransitionMessage(string $currentStatus, string $nextStatus): string
    {
        $allowed = self::allowedNextStatuses($currentStatus);

        if ($allowed === []) {
            return 'No forward transition is allowed from status "'.$currentStatus.'".';
        }

        return 'Status "'.$nextStatus.'" is not allowed from "'.$currentStatus.'". Allowed next status(es): '.implode(', ', $allowed).'.';
    }

    public static function isFinalStatus(string $status): bool
    {
        return self::allowedNextStatuses($status) === [];
    }

    public static function requiresRemarks(string $targetStatus): bool
    {
        return in_array($targetStatus, [self::REJECTED, self::WAITLISTED], true);
    }
}
