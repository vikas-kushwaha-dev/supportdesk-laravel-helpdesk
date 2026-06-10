<x-app-layout>
    <x-slot name="header">
        <h2>Create Ticket</h2>
    </x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf

            <div>
                <label>Subject</label>
                <input type="text" name="subject" value="{{ old('subject') }}">
                @error('subject') <p>{{ $message }}</p> @enderror
            </div>

            <div>
                <label>Description</label>
                <textarea name="description">{{ old('description') }}</textarea>
                @error('description') <p>{{ $message }}</p> @enderror
            </div>

            <div>
                <label>Priority</label>
                <select name="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="urgent">Urgent</option>
                </select>
                @error('priority') <p>{{ $message }}</p> @enderror
            </div>

            <div>
                <label>Category</label>
                <input type="text" name="category" value="{{ old('category') }}">
                @error('category') <p>{{ $message }}</p> @enderror
            </div>

            <button type="submit">Create Ticket</button>
        </form>
    </div>
</x-app-layout>