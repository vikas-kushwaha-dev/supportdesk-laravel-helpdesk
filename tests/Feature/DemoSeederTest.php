<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_demo_users_tickets_and_ai_insights(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'agent@example.com',
            'role' => 'agent',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => 'customer',
        ]);

        $this->assertDatabaseHas('tickets', [
            'ticket_no' => 'TKT-DEMO001',
            'subject' => 'Cannot login to account',
            'category' => 'Account Access',
            'status' => 'open',
        ]);

        $this->assertDatabaseHas('tickets', [
            'ticket_no' => 'TKT-DEMO002',
            'category' => 'Billing',
            'status' => 'in_progress',
        ]);

        $this->assertDatabaseHas('tickets', [
            'ticket_no' => 'TKT-DEMO003',
            'category' => 'Hardware',
            'status' => 'resolved',
        ]);

        $this->assertDatabaseHas('ticket_ai_insights', [
            'suggested_priority' => 'urgent',
            'suggested_category' => 'Account Access',
            'ai_model' => 'rule-based-v1',
        ]);

        $this->assertDatabaseCount('tickets', 3);
        $this->assertDatabaseCount('ticket_ai_insights', 3);
        $this->assertDatabaseCount('ticket_status_logs', 3);
    }
}
