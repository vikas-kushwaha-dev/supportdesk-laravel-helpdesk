<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketStatusLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_ticket_creation_records_initial_status_log(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $this->actingAs($customer);

        $ticket = $this->createTicket([
            'user_id' => $customer->id,
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('ticket_status_logs', [
            'ticket_id' => $ticket->id,
            'changed_by' => $customer->id,
            'old_status' => null,
            'new_status' => 'open',
            'note' => 'Ticket created with status open.',
        ]);
    }

    public function test_ticket_status_change_records_status_log(): void
    {
        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
        ]);

        $ticket = $this->createTicket([
            'status' => 'open',
        ]);

        $this->actingAs($agent);

        $ticket->update([
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('ticket_status_logs', [
            'ticket_id' => $ticket->id,
            'changed_by' => $agent->id,
            'old_status' => 'open',
            'new_status' => 'in_progress',
            'note' => 'Ticket status changed from open to in progress.',
        ]);
    }

    public function test_ticket_update_without_status_change_does_not_record_extra_status_log(): void
    {
        $ticket = $this->createTicket([
            'status' => 'open',
        ]);

        $ticket->update([
            'subject' => 'Updated printer issue',
        ]);

        $this->assertDatabaseCount('ticket_status_logs', 1);
    }

    public function test_ticket_detail_page_shows_status_history(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $ticket = $this->createTicket([
            'status' => 'open',
        ]);

        $this->actingAs($admin);

        $ticket->update([
            'status' => 'resolved',
        ]);

        $response = $this->actingAs($admin)->get(route('tickets.show', $ticket));

        $response
            ->assertOk()
            ->assertSee('Status History')
            ->assertSee('Ticket status changed from open to resolved.');
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
