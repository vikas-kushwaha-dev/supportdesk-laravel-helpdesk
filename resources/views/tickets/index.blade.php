<x-app-layout>
    <x-slot name="header">
        <h2>Tickets</h2>
    </x-slot>

    <div class="p-6">
        <a href="{{ route('tickets.create') }}">Create Ticket</a>

        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <table border="1" cellpadding="10" style="margin-top: 20px; width: 100%;">
            <thead>
                <tr>
                    <th>Ticket No</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td>{{ $ticket->ticket_no }}</td>
                        <td>{{ $ticket->subject }}</td>
                        <td>{{ ucfirst($ticket->priority) }}</td>
                        <td>{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</td>
                        <td>{{ $ticket->category ?? '-' }}</td>
                        <td>{{ $ticket->user->name }}</td>
                        <td>
                            <a href="{{ route('tickets.show', $ticket) }}">View</a>
                            <a href="{{ route('tickets.edit', $ticket) }}">Edit</a>

                            <form action="{{ route('tickets.destroy', $ticket) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Delete this ticket?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $tickets->links() }}
    </div>
</x-app-layout>