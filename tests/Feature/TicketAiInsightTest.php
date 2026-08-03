<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAiInsightTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_creation_stores_ai_insight(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $response = $this->actingAs($customer)->post(route('tickets.store'), [
            'subject' => 'Cannot login',
            'description' => 'I cannot access my account and need urgent help.',
            'priority' => 'medium',
            'category' => 'General',
        ]);

        $response->assertRedirect(route('tickets.index'));

        $ticket = Ticket::query()->firstOrFail();

        $this->assertDatabaseHas('ticket_ai_insights', [
            'ticket_id' => $ticket->id,
            'suggested_priority' => 'urgent',
            'suggested_category' => 'Account Access',
            'sentiment' => 'frustrated',
            'summary' => 'AI summary: Cannot login',
            'recommended_action' => 'Verify user account status and reset credentials if required.',
            'confidence_score' => 90,
            'ai_model' => 'rule-based-v1',
        ]);

        $this->assertSame('local_rule_based_engine', $ticket->aiInsight->raw_response['source']);
    }
}
