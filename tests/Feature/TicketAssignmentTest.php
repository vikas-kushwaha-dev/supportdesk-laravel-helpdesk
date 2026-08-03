<?php

namespace Tests\Feature;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TicketAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_assign_ticket_to_agent(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
        ]);

        $ticket = $this->createTicket();

        $response = $this
            ->actingAs($admin)
            ->patch(route('tickets.assignment.update', $ticket), [
                'assigned_to' => $agent->id,
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('success', 'Ticket assignment updated successfully.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => $agent->id,
        ]);
    }

    public function test_admin_can_unassign_ticket(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
        ]);

        $ticket = $this->createTicket([
            'assigned_to' => $agent->id,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('tickets.assignment.update', $ticket), [
                'assigned_to' => null,
            ]);

        $response
            ->assertRedirect()
            ->assertSessionHas('success', 'Ticket assignment updated successfully.');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => null,
        ]);
    }

    public function test_admin_cannot_assign_ticket_to_customer(): void
    {
        $admin = User::factory()->create([
            'role' => User::ROLE_ADMIN,
        ]);

        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $ticket = $this->createTicket();

        $response = $this
            ->actingAs($admin)
            ->patch(route('tickets.assignment.update', $ticket), [
                'assigned_to' => $customer->id,
            ]);

        $response->assertSessionHasErrors('assigned_to');

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => null,
        ]);
    }

    public function test_customer_cannot_assign_ticket(): void
    {
        $customer = User::factory()->create([
            'role' => User::ROLE_CUSTOMER,
        ]);

        $agent = User::factory()->create([
            'role' => User::ROLE_AGENT,
        ]);

        $ticket = $this->createTicket([
            'user_id' => $customer->id,
        ]);

        $response = $this
            ->actingAs($customer)
            ->patch(route('tickets.assignment.update', $ticket), [
                'assigned_to' => $agent->id,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('tickets', [
            'id' => $ticket->id,
            'assigned_to' => null,
        ]);
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
