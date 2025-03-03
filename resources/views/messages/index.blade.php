<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Messages') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($jobsWithMessages->count() > 0)
                    <div class="space-y-4">
                        @foreach($jobsWithMessages as $job)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold">
                                        <a href="{{ route('messages.show', $job) }}" class="text-blue-600 hover:underline">
                                            {{ $job->title }}
                                        </a>
                                    </h3>
                                    <p class="text-sm text-gray-600">
                                        @if(auth()->user()->id === $job->client_id)
                                        Worker: {{ $job->applications->first()->worker->name ?? 'No worker assigned' }}
                                        @else
                                        Client: {{ $job->client->name }}
                                        @endif
                                    </p>
                                    <p class="text-sm text-gray-600">
                                        Status: {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                    </p>
                                </div>
                                <div>
                                    @php
                                    $unreadCount = $job->messages()
                                    ->where('receiver_id', auth()->id())
                                    ->whereNull('read_at')
                                    ->count();
                                    @endphp

                                    @if($unreadCount > 0)
                                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                        {{ $unreadCount }} new
                                    </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-center py-6">No message threads found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>