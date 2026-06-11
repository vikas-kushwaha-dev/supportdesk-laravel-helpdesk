<x-app-layout>
    <x-slot name="header">
        <h2>Ticket Details</h2>
    </x-slot>

    <div class="p-6">
        <p><strong>Ticket No:</strong> {{ $ticket->ticket_no }}</p>
        <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
        <p><strong>Description:</strong> {{ $ticket->description }}</p>
        <p><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
        <p><strong>Category:</strong> {{ $ticket->category ?? '-' }}</p>
        <p><strong>Created By:</strong> {{ $ticket->user->name }}</p>

        <a href="{{ route('tickets.index') }}">Back</a>
    </div>
</x-app-layout>