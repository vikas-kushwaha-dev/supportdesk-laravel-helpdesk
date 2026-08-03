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

    public function test_admin_can_apply_ai_suggestion_to_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $ticket = Ticket::query()->create([
            'ticket_no' => 'TKT-ADMIN01',
            'user_id' => $customer->id,
            'subject' => 'Payment failed',
            'description' => 'My payment is not working and I need urgent help.',
            'priority' => 'medium',
            'category' => 'General',
            'status' => 'open',
        ]);

        $ticket->aiInsight()->create([
            'suggested_priority' => 'urgent',
            'suggested_category' => 'Billing',
            'sentiment' => 'frustrated',
            'summary' => 'AI summary: Payment failed',
            'recommended_action' => 'Check payment records and verify customer billing status.',
            'confidence_score' => 90,
            'ai_model' => 'rule-based-v1',
            'raw_response' => [
                'source' => 'local_rule_based_engine',
            ],
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('tickets.apply-ai-suggestion', $ticket));

        $response
            ->assertRedirect()
            ->assertSessionHas('success', 'AI suggestion applied successfully.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => 'urgent',
            'category' => 'Billing',
        ]);
    }

    public function test_customer_cannot_apply_ai_suggestion_to_ticket(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $ticket = Ticket::query()->create([
            'ticket_no' => 'TKT-CUST01',
            'user_id' => $customer->id,
            'subject' => 'Payment failed',
            'description' => 'My payment is not working and I need urgent help.',
            'priority' => 'medium',
            'category' => 'General',
            'status' => 'open',
        ]);

        $ticket->aiInsight()->create([
            'suggested_priority' => 'urgent',
            'suggested_category' => 'Billing',
        ]);

        $response = $this
            ->actingAs($customer)
            ->patch(route('tickets.apply-ai-suggestion', $ticket));

        $response->assertForbidden();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => 'medium',
            'category' => 'General',
        ]);
    }

    public function test_admin_sees_error_when_applying_missing_ai_suggestion(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $ticket = Ticket::query()->create([
            'ticket_no' => 'TKT-NOAI01',
            'user_id' => $customer->id,
            'subject' => 'Printer issue',
            'description' => 'The office printer is not responding.',
            'priority' => 'low',
            'category' => 'Hardware',
            'status' => 'open',
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('tickets.apply-ai-suggestion', $ticket));

        $response
            ->assertRedirect()
            ->assertSessionHas('error', 'No AI insight available for this ticket.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'priority' => 'low',
            'category' => 'Hardware',
        ]);
    }
}
