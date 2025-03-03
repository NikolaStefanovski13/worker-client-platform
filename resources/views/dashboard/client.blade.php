<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Client Dashboard') }}
            </h2>
            <a href="{{ route('jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                {{ __('Post a Job') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">{{ __('Your Posted Jobs') }}</h3>

                    @if(isset($postedJobs['posted']) && $postedJobs['posted']->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">{{ __('Open Jobs') }}</h4>
                        <div class="space-y-4">
                            @foreach($postedJobs['posted'] as $job)
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between">
                                    <div>
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:underline text-lg font-medium">
                                            {{ $job->title }}
                                        </a>
                                        <p class="text-sm text-gray-600">
                                            {{ $job->location }} | ${{ number_format($job->budget, 2) }}
                                        </p>
                                        <p class="mt-2">{{ Str::limit($job->description, 100) }}</p>
                                        <p class="text-sm mt-2">
                                            <span class="font-medium">{{ $job->applications->count() }}</span> application(s) received
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                                            {{ __('View Applications') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($postedJobs['in_progress']) && $postedJobs['in_progress']->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">{{ __('Jobs In Progress') }}</h4>
                        <div class="space-y-4">
                            @foreach($postedJobs['in_progress'] as $job)
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between">
                                    <div>
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:underline text-lg font-medium">
                                            {{ $job->title }}
                                        </a>
                                        <p class="text-sm text-gray-600">
                                            {{ $job->location }} | ${{ number_format($job->budget, 2) }}
                                        </p>
                                        <p class="mt-2">{{ Str::limit($job->description, 100) }}</p>
                                        <p class="text-sm mt-2">
                                            Worker:
                                            <span class="font-medium">
                                                {{ $job->applications->where('status', 'accepted')->first()->worker->name ?? 'No worker assigned' }}
                                            </span>
                                        </p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('messages.show', $job) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                                            {{ __('Messages') }}
                                        </a>
                                        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                                            {{ __('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($postedJobs['completed']) && $postedJobs['completed']->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">{{ __('Completed Jobs') }}</h4>
                        <div class="space-y-4">
                            @foreach($postedJobs['completed'] as $job)
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between">
                                    <div>
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:underline text-lg font-medium">
                                            {{ $job->title }}
                                        </a>
                                        <p class="text-sm text-gray-600">
                                            {{ $job->location }} | ${{ number_format($job->budget, 2) }}
                                        </p>
                                        <p class="mt-2">{{ Str::limit($job->description, 100) }}</p>
                                        <p class="text-sm mt-2">
                                            Completed: {{ $job->completed_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                    <div>
                                        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600">
                                            {{ __('View Details') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!isset($postedJobs['posted']) && !isset($postedJobs['in_progress']) && !isset($postedJobs['completed']))
                    <p class="text-gray-500">{{ __('You have not posted any jobs yet.') }}</p>
                    <div class="mt-4">
                        <a href="{{ route('jobs.create') }}" class="text-blue-600 hover:underline">
                            {{ __('Post your first job →') }}
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>