<x-app-layout>
    <x-slot name="header">
        <h2>Ticket Details</h2>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <p>{{ session('success') }}</p>
        @endif

        @if(session('error'))
            <p>{{ session('error') }}</p>
        @endif

        <p><strong>Ticket No:</strong> {{ $ticket->ticket_no }}</p>
        <p><strong>Subject:</strong> {{ $ticket->subject }}</p>
        <p><strong>Description:</strong> {{ $ticket->description }}</p>
        <p><strong>Priority:</strong> {{ ucfirst($ticket->priority) }}</p>
        <p><strong>Status:</strong> {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</p>
        <p><strong>Category:</strong> {{ $ticket->category ?? '-' }}</p>
        <p><strong>Created By:</strong> {{ $ticket->user->name }}</p>
        <p><strong>Assigned Agent:</strong> {{ $ticket->assignedAgent?->name ?? 'Unassigned' }}</p>

        @if(auth()->user()->isAdmin())
            <hr>

            <h3>Assign Ticket</h3>

            <form method="POST" action="{{ route('tickets.assignment.update', $ticket) }}">
                @csrf
                @method('PATCH')

                <label for="assigned_to">Agent</label>
                <select id="assigned_to" name="assigned_to">
                    <option value="">Unassigned</option>
                    @foreach($agents as $agent)
                        <option value="{{ $agent->id }}" @selected((int) old('assigned_to', $ticket->assigned_to) === $agent->id)>
                            {{ $agent->name }} ({{ $agent->email }})
                        </option>
                    @endforeach
                </select>
                @error('assigned_to') <p>{{ $message }}</p> @enderror

                <button type="submit">Update Assignment</button>
            </form>
        @endif

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

        @if($ticket->aiInsight)
        <hr>

        <h3>AI Ticket Insight</h3>

        <p><strong>Suggested Priority:</strong> {{ ucfirst($ticket->aiInsight->suggested_priority) }}</p>
        <p><strong>Suggested Category:</strong> {{ $ticket->aiInsight->suggested_category }}</p>
        <p><strong>Sentiment:</strong> {{ ucfirst($ticket->aiInsight->sentiment) }}</p>
        <p><strong>Summary:</strong> {{ $ticket->aiInsight->summary }}</p>
        <p><strong>Recommended Action:</strong> {{ $ticket->aiInsight->recommended_action }}</p>
        <p><strong>Confidence:</strong> {{ $ticket->aiInsight->confidence_score }}%</p>
        <p><strong>Model:</strong> {{ $ticket->aiInsight->ai_model }}</p>

        @if(auth()->user()->isAdmin())
            <form method="POST" action="{{ route('tickets.apply-ai-suggestion', $ticket) }}">
                @csrf
                @method('PATCH')

                <button type="submit">Apply AI Suggestion</button>
            </form>
        @endif
        @endif

        <a href="{{ route('tickets.index') }}">Back</a>
    </div>
</x-app-layout>
