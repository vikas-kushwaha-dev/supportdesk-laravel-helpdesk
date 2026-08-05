<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_tickets_by_status_priority_category_and_assigned_agent(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $agent = User::factory()->create([
            'name' => 'Assigned Agent',
            'role' => User::ROLE_AGENT,
        ]);

        $matchingTicket = $this->createTicket([
            'ticket_no' => 'TKT-MATCH',
            'subject' => 'Matching urgent billing ticket',
            'priority' => 'urgent',
            'status' => 'open',
            'category' => 'Billing',
            'assigned_to' => $agent->id,
        ]);

        $otherTicket = $this->createTicket([
            'ticket_no' => 'TKT-OTHER',
            'subject' => 'Other printer ticket',
            'priority' => 'low',
            'status' => 'closed',
            'category' => 'Hardware',
            'assigned_to' => null,
        ]);

        $response = $this->actingAs($admin)->get(route('tickets.index', [
            'assigned_to' => $agent->id,
            'category' => 'Billing',
            'priority' => 'urgent',
            'status' => 'open',
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingTicket->ticket_no)
            ->assertDontSee($otherTicket->ticket_no);
    }

    public function test_admin_can_search_tickets_by_keyword(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $matchingTicket = $this->createTicket([
            'ticket_no' => 'TKT-LOGIN',
            'subject' => 'Cannot access account',
            'description' => 'Customer login is failing after password reset.',
        ]);

        $otherTicket = $this->createTicket([
            'ticket_no' => 'TKT-PRINTER',
            'subject' => 'Printer offline',
            'description' => 'Office printer needs a new driver.',
        ]);

        $response = $this->actingAs($admin)->get(route('tickets.index', [
            'search' => 'password reset',
        ]));

        $response
            ->assertOk()
            ->assertSee($matchingTicket->ticket_no)
            ->assertDontSee($otherTicket->ticket_no);
    }

    public function test_customer_filters_only_include_their_own_tickets(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $ownTicket = $this->createTicket([
            'ticket_no' => 'TKT-OWN',
            'user_id' => $customer->id,
            'subject' => 'Own billing issue',
            'category' => 'Billing',
        ]);

        $otherTicket = $this->createTicket([
            'ticket_no' => 'TKT-HIDDEN',
            'subject' => 'Hidden billing issue',
            'category' => 'Billing',
        ]);

        $response = $this->actingAs($customer)->get(route('tickets.index', [
            'category' => 'Billing',
            'search' => 'billing',
        ]));

        $response
            ->assertOk()
            ->assertSee($ownTicket->ticket_no)
            ->assertDontSee($otherTicket->ticket_no);
    }

    private function createTicket(array $attributes = []): Ticket
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        return Ticket::query()->create(array_merge([
            'ticket_no' => 'TKT-'.fake()->unique()->numerify('#####'),
            'user_id' => $customer->id,
            'subject' => 'Printer issue',
            'description' => 'The office printer is not responding.',
            'priority' => 'low',
            'category' => 'Hardware',
            'status' => 'open',
        ], $attributes));
    }
}
