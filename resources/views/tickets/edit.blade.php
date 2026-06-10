<x-app-layout>
    <x-slot name="header">
        <h2>Edit Ticket</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf
            @method('PUT')

            <div>
                <label>Subject</label>
                <input type="text" name="subject" value="{{ old('subject', $ticket->subject) }}">
                @error('subject') <p>{{ $message }}</p> @enderror
            </div>

            <div>
                <label>Description</label>
                <textarea name="description">{{ old('description', $ticket->description) }}</textarea>
                @error('description') <p>{{ $message }}</p> @enderror
            </div>

            <div>
                <label>Priority</label>
                <select name="priority">
                    @foreach(['low', 'medium', 'high', 'urgent'] as $priority)
                        <option value="{{ $priority }}" @selected($ticket->priority === $priority)>
                            {{ ucfirst($priority) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Status</label>
                <select name="status">
                    @foreach(['open', 'in_progress', 'resolved', 'closed'] as $status)
                        <option value="{{ $status }}" @selected($ticket->status === $status)>
                            {{ ucfirst(str_replace('_', ' ', $status)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Category</label>
                <input type="text" name="category" value="{{ old('category', $ticket->category) }}">
            </div>

            <button type="submit">Update Ticket</button>
        </form>
    </div>
</x-app-layout>