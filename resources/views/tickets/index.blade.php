<x-app-layout>
    <x-slot name="header">
        <h2>Tickets</h2>
    </x-slot>

    <div class="p-6">
        <a href="{{ route('tickets.create') }}">Create Ticket</a>

        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif

        <form method="GET" action="{{ route('tickets.index') }}" style="margin-top: 20px;">
            <div>
                <label for="search">Search</label>
                <input id="search" type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Ticket no, subject, or description">
            </div>

            <div>
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="">All statuses</option>
                    @foreach(['open', 'in_progress', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="priority">Priority</label>
                <select id="priority" name="priority">
                    <option value="">All priorities</option>
                    @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                        <option value="{{ $priority }}" @selected(($filters['priority'] ?? '') === $priority)>
                            {{ ucfirst($priority) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="category">Category</label>
                <select id="category" name="category">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>
                            {{ $category }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if(auth()->user()->isAdmin())
                <div>
                    <label for="assigned_to">Assigned agent</label>
                    <select id="assigned_to" name="assigned_to">
                        <option value="">All agents</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" @selected((string) ($filters['assigned_to'] ?? '') === (string) $agent->id)>
                                {{ $agent->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit">Filter Tickets</button>
            <a href="{{ route('tickets.index') }}">Clear Filters</a>
        </form>

        <table border="1" cellpadding="10" style="margin-top: 20px; width: 100%;">
            <thead>
                <tr>
                    <th>Ticket No</th>
                    <th>Subject</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>Assigned Agent</th>
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
                        <td>{{ $ticket->assignedAgent?->name ?? 'Unassigned' }}</td>
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
