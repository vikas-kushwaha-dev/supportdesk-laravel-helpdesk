<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketAiInsight extends Model
{
    protected $fillable = [
        'ticket_id',
        'suggested_priority',
        'suggested_category',
        'sentiment',
        'summary',
        'recommended_action',
        'confidence_score',
        'ai_model',
        'raw_response',
    ];

    protected $casts = [
        'raw_response' => 'array',
        'confidence_score' => 'decimal:2',
    ];

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}
