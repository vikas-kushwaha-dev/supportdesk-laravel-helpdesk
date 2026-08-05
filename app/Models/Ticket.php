<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Ticket extends Model
{
    protected $fillable = [
        'ticket_no',
        'user_id',
        'subject',
        'description',
        'priority',
        'status',
        'category',
        'assigned_to',
        'resolved_at',
        'closed_at',
    ];

    protected static function booted(): void
    {
        static::created(function (Ticket $ticket): void {
            $ticket->statusLogs()->create([
                'changed_by' => Auth::id(),
                'old_status' => null,
                'new_status' => $ticket->status,
                'note' => 'Ticket created with status '.str_replace('_', ' ', $ticket->status).'.',
            ]);
        });

        static::updated(function (Ticket $ticket): void {
            if (! $ticket->wasChanged('status')) {
                return;
            }

            $ticket->statusLogs()->create([
                'changed_by' => Auth::id(),
                'old_status' => $ticket->getOriginal('status'),
                'new_status' => $ticket->status,
                'note' => 'Ticket status changed from '
                    .str_replace('_', ' ', $ticket->getOriginal('status'))
                    .' to '
                    .str_replace('_', ' ', $ticket->status)
                    .'.',
            ]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedAgent()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    public function attachments()
    {
        return $this->hasMany(TicketAttachment::class);
    }

    public function aiInsight()
    {
        return $this->hasOne(TicketAiInsight::class);
    }

    public function statusLogs()
    {
        return $this->hasMany(TicketStatusLog::class);
    }
}
