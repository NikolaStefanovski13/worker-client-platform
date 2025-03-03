<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Messages') }}: {{ $job->title }}
            </h2>
            <a href="{{ route('messages.index') }}" class="text-sm text-blue-600 hover:underline">
                Back to all messages
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-4 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <!-- Messages Display -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="border-b pb-4 mb-4 border-gray-200">
                        <h3 class="text-lg font-semibold">Conversation with {{ $otherUser->name }}</h3>
                        <p class="text-sm text-gray-600">
                            Job Status: {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                        </p>
                    </div>

                    <div class="space-y-4 max-h-96 overflow-y-auto mb-6 p-2">
                        @if($messages->count() > 0)
                        @foreach($messages as $message)
                        <div class="flex {{ $message->sender_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="rounded-lg px-4 py-2 max-w-md
                                        {{ $message->sender_id === auth()->id() 
                                            ? 'bg-blue-500 text-white'
                                            : 'bg-gray-100 text-gray-900' }}">
                                <p>{{ $message->content }}</p>
                                <p class="text-xs mt-1 {{ $message->sender_id === auth()->id() ? 'text-blue-200' : 'text-gray-500' }}">
                                    {{ $message->created_at->format('M d, Y g:i A') }}
                                </p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p class="text-center text-gray-500 py-4">No messages yet. Start the conversation below.</p>
                        @endif
                    </div>

                    <!-- Message Form -->
                    <form method="POST" action="{{ route('messages.store', $job) }}">
                        @csrf
                        <div>
                            <label for="content" class="sr-only">Message</label>
                            <textarea
                                id="content"
                                name="content"
                                rows="3"
                                class="block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                placeholder="Type your message here..."
                                required></textarea>
                            <x-input-error :messages="$errors->get('content')" class="mt-2" />
                        </div>
                        <div class="mt-3 flex justify-end">
                            <x-primary-button>
                                {{ __('Send Message') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Job Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-2">Job Details</h3>
                    <p class="text-sm text-gray-600 mb-4">
                        <span class="font-medium">Client:</span> {{ $job->client->name }}
                        <br>
                        <span class="font-medium">Worker:</span> {{ $job->applications->first()->worker->name ?? 'Not assigned' }}
                        <br>
                        <span class="font-medium">Budget:</span> ${{ number_format($job->budget, 2) }}
                        <br>
                        <span class="font-medium">Location:</span> {{ $job->location }}
                    </p>
                    <a href="{{ route('jobs.show', $job) }}" class="text-sm text-blue-600 hover:underline">
                        View full job details
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>