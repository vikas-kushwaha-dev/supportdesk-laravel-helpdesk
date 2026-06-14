<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketAttachmentController extends Controller
{
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $request->validate([
            'attachment' => ['required', 'file', 'max:2048'],
        ]);

        $file = $request->file('attachment');
        $path = $file->store('ticket-attachments', 'public');

        $ticket->attachments()->create([
            'user_id' => auth()->id(),
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return back()->with('success', 'Attachment uploaded successfully.');
    }
}
