<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TicketCommentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        $ticket->comments()->create([
            'user_id' => auth()->id(),
            'message' => $request->message,
        ]);

        return back()->with('success', 'Comment added successfully.');
    }
}
