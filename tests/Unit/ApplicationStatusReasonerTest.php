<?php

namespace Tests\Unit;

use App\Support\ApplicationStatusReasoner;
use PHPUnit\Framework\TestCase;

class ApplicationStatusReasonerTest extends TestCase
{
    public function test_forward_transitions_are_defined_for_reviewed(): void
    {
        $this->assertSame(
            [
                ApplicationStatusReasoner::APPROVED,
                ApplicationStatusReasoner::REJECTED,
                ApplicationStatusReasoner::WAITLISTED,
            ],
            ApplicationStatusReasoner::allowedNextStatuses(ApplicationStatusReasoner::REVIEWED)
        );
    }

    public function test_backward_validation_rejects_invalid_transition(): void
    {
        $this->assertFalse(
            ApplicationStatusReasoner::canTransition(
                ApplicationStatusReasoner::PENDING,
                ApplicationStatusReasoner::APPROVED
            )
        );
    }

    public function test_terminal_states_have_no_forward_transitions(): void
    {
        $this->assertTrue(ApplicationStatusReasoner::isFinalStatus(ApplicationStatusReasoner::APPROVED));
        $this->assertTrue(ApplicationStatusReasoner::isFinalStatus(ApplicationStatusReasoner::REJECTED));
        $this->assertTrue(ApplicationStatusReasoner::isFinalStatus(ApplicationStatusReasoner::WAITLISTED));
    }

    public function test_allowed_final_decisions_only_apply_after_review(): void
    {
        $this->assertSame([], ApplicationStatusReasoner::allowedFinalDecisionStatuses(ApplicationStatusReasoner::PENDING));
        $this->assertSame(
            [
                ApplicationStatusReasoner::APPROVED,
                ApplicationStatusReasoner::REJECTED,
                ApplicationStatusReasoner::WAITLISTED,
            ],
            ApplicationStatusReasoner::allowedFinalDecisionStatuses(ApplicationStatusReasoner::REVIEWED)
        );
    }

    public function test_applicant_editing_and_admin_review_share_same_precondition(): void
    {
        $this->assertTrue(ApplicationStatusReasoner::canBeEditedByApplicant(ApplicationStatusReasoner::PENDING));
        $this->assertTrue(ApplicationStatusReasoner::canBeReviewedByAdmin(ApplicationStatusReasoner::PENDING));

        $this->assertFalse(ApplicationStatusReasoner::canBeEditedByApplicant(ApplicationStatusReasoner::REVIEWED));
        $this->assertFalse(ApplicationStatusReasoner::canBeReviewedByAdmin(ApplicationStatusReasoner::REVIEWED));
    }
}
