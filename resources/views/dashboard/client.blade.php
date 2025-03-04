<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Client Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex justify-between items-center">
                <h3 class="text-lg font-semibold">{{ __('Your Jobs') }}</h3>
                <a href="{{ route('jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Post a New Job
                </a>
            </div>

            @if(isset($postedJobs) && $postedJobs->count() > 0)
            <!-- Posted Jobs Section -->
            @if(isset($postedJobs['posted']) && $postedJobs['posted']->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-4">Open Jobs</h4>
                    <div class="space-y-4">
                        @foreach($postedJobs['posted'] as $job)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between">
                                <div>
                                    <h5 class="font-medium">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $job->title }}
                                        </a>
                                    </h5>
                                    <p class="text-sm text-gray-600">Posted on {{ $job->created_at->format('M d, Y') }}</p>
                                    <p class="text-sm mt-1">
                                        <span class="font-medium">Budget:</span> ${{ number_format($job->budget, 2) }} |
                                        <span class="font-medium">Location:</span> {{ $job->location }} |
                                        <span class="font-medium">Applications:</span> {{ $job->applications->count() }}
                                    </p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Open</span>
                                </div>
                            </div>
                            <div class="mt-3 flex space-x-2">
                                <a href="{{ route('jobs.show', $job) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    View Details
                                </a>
                                <a href="{{ route('jobs.edit', $job) }}" class="text-sm text-gray-600 hover:text-gray-800">
                                    Edit
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- In-Progress Jobs Section -->
            @if(isset($postedJobs['in_progress']) && $postedJobs['in_progress']->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-4">Jobs In Progress</h4>
                    <div class="space-y-4">
                        @foreach($postedJobs['in_progress'] as $job)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between">
                                <div>
                                    <h5 class="font-medium">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $job->title }}
                                        </a>
                                    </h5>
                                    <p class="text-sm text-gray-600">Started on {{ $job->updated_at->format('M d, Y') }}</p>
                                    <p class="text-sm mt-1">
                                        <span class="font-medium">Worker:</span>
                                        @if($job->applications->where('status', 'accepted')->first())
                                        {{ $job->applications->where('status', 'accepted')->first()->worker->name }}
                                        @else
                                        No worker assigned
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In Progress</span>
                                </div>
                            </div>
                            <div class="mt-3 flex space-x-3">
                                <a href="{{ route('jobs.show', $job) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    View Details
                                </a>
                                <a href="{{ route('messages.show', $job) }}" class="text-sm text-green-600 hover:text-green-800">
                                    Message Worker
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Completed Jobs Section -->
            @if(isset($postedJobs['completed']) && $postedJobs['completed']->count() > 0)
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h4 class="font-medium text-gray-700 mb-4">Completed Jobs</h4>
                    <div class="space-y-4">
                        @foreach($postedJobs['completed'] as $job)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between">
                                <div>
                                    <h5 class="font-medium">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $job->title }}
                                        </a>
                                    </h5>
                                    <p class="text-sm text-gray-600">Completed on {{ $job->completed_at->format('M d, Y') }}</p>
                                    <p class="text-sm mt-1">
                                        <span class="font-medium">Worker:</span>
                                        @if($job->applications->where('status', 'accepted')->first())
                                        {{ $job->applications->where('status', 'accepted')->first()->worker->name }}
                                        @else
                                        No worker assigned
                                        @endif
                                    </p>
                                </div>
                                <div>
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Completed</span>
                                </div>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('jobs.show', $job) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <p class="text-gray-500 mb-4">You haven't posted any jobs yet.</p>
                    <a href="{{ route('jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Post Your First Job
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>