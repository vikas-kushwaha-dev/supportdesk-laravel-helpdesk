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
        <hr>

        <h3>Comments</h3>

        <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
            @csrf

            <textarea name="message" rows="4" required></textarea>
            @error('message') <p>{{ $message }}</p> @enderror

            <button type="submit">Add Comment</button>
        </form>

        @foreach($ticket->comments as $comment)
        <div style="border: 1px solid #ccc; padding: 10px; margin-top: 10px;">
            <strong>{{ $comment->user->name }}</strong>
            <small>{{ $comment->created_at->diffForHumans() }}</small>
            <p>{{ $comment->message }}</p>
        </div>
        @endforeach

        <hr>

        <h3>Attachments</h3>

        <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data">
            @csrf

            <input type="file" name="attachment" required>
            @error('attachment') <p>{{ $message }}</p> @enderror

            <button type="submit">Upload</button>
        </form>

        @foreach($ticket->attachments as $attachment)
        <div style="margin-top: 10px;">
            <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank">
                {{ $attachment->file_name }}
            </a>
            <small>Uploaded by {{ $attachment->user->name }}</small>
        </div>
        @endforeach

        <a href="{{ route('tickets.index') }}">Back</a>
    </div>
</x-app-layout>