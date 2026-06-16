<?php

namespace App\Services;

use App\Models\Ticket;

class TicketAiService
{
    public function analyse(Ticket $ticket): array
    {
        $text = strtolower($ticket->subject . ' ' . $ticket->description);

        $priority = 'medium';
        $category = 'General';
        $sentiment = 'neutral';
        $confidence = 70;

        if (str_contains($text, 'payment') || str_contains($text, 'billing')) {
            $category = 'Billing';
            $priority = 'high';
            $confidence = 85;
        }

        if (str_contains($text, 'login') || str_contains($text, 'password') || str_contains($text, 'access')) {
            $category = 'Account Access';
            $priority = 'high';
            $confidence = 82;
        }

        if (str_contains($text, 'urgent') || str_contains($text, 'critical') || str_contains($text, 'down')) {
            $priority = 'urgent';
            $sentiment = 'frustrated';
            $confidence = 90;
        }

        return [
            'suggested_priority' => $priority,
            'suggested_category' => $category,
            'sentiment' => $sentiment,
            'summary' => 'AI summary: ' . $ticket->subject,
            'recommended_action' => $this->recommendedAction($category),
            'confidence_score' => $confidence,
            'ai_model' => 'rule-based-v1',
            'raw_response' => [
                'source' => 'local_rule_based_engine',
                'matched_text' => $text,
            ],
        ];
    }

    private function recommendedAction(string $category): string
    {
        return match ($category) {
            'Billing' => 'Check payment records and verify customer billing status.',
            'Account Access' => 'Verify user account status and reset credentials if required.',
            default => 'Review ticket details and assign it to the correct support agent.',
        };
    }
}
