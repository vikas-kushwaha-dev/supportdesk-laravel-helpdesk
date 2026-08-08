<?php

namespace Database\Seeders;

use App\Models\Ticket;
use App\Models\User;
use App\Services\TicketAiService;
use Illuminate\Database\Seeder;

class DemoTicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $agent = User::query()->where('email', 'agent@example.com')->firstOrFail();
        $customer = User::query()->where('email', 'customer@example.com')->firstOrFail();
        $ticketAiService = app(TicketAiService::class);

        $tickets = [
            [
                'ticket_no' => 'TKT-DEMO001',
                'subject' => 'Cannot login to account',
                'description' => 'I cannot access my account after resetting my password and need urgent help.',
                'priority' => 'medium',
                'status' => 'open',
                'category' => 'Account Access',
                'assigned_to' => $agent->id,
            ],
            [
                'ticket_no' => 'TKT-DEMO002',
                'subject' => 'Payment failed on invoice',
                'description' => 'The billing payment failed twice and the customer portal says the service may be suspended.',
                'priority' => 'high',
                'status' => 'in_progress',
                'category' => 'Billing',
                'assigned_to' => $agent->id,
            ],
            [
                'ticket_no' => 'TKT-DEMO003',
                'subject' => 'Office printer is offline',
                'description' => 'The shared office printer is not responding from any workstation.',
                'priority' => 'low',
                'status' => 'resolved',
                'category' => 'Hardware',
                'assigned_to' => null,
            ],
        ];

        foreach ($tickets as $ticketData) {
            $ticket = Ticket::updateOrCreate(
                ['ticket_no' => $ticketData['ticket_no']],
                array_merge($ticketData, [
                    'user_id' => $customer->id,
                ])
            );

            $ticket->aiInsight()->updateOrCreate(
                ['ticket_id' => $ticket->id],
                $ticketAiService->analyse($ticket)
            );
        }
    }
}
