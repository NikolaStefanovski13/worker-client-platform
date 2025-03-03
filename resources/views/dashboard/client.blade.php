<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Client Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Quick Actions -->
            <div class="mb-6 flex">
                <a href="{{ route('jobs.create') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Post a New Job</a>
            </div>

            <!-- Posted Jobs Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">My Posted Jobs</h3>

                    @if(isset($postedJobs['posted']) && $postedJobs['posted']->count() > 0)
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Open Jobs</h4>
                    <div class="space-y-4 mb-6">
                        @foreach($postedJobs['posted'] as $job)
                        <div class="border border-gray-200 dark:border-gray-700 rounded p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $job->title }}</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Budget: ${{ $job->budget }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Applications: {{ $job->applications->count() }}</p>
                                </div>
                                <span class="bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 text-xs px-2 py-1 rounded">Open</span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View Details</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if(isset($postedJobs['in_progress']) && $postedJobs['in_progress']->count() > 0)
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">In Progress Jobs</h4>
                    <div class="space-y-4">
                        @foreach($postedJobs['in_progress'] as $job)
                        <div class="border border-gray-200 dark:border-gray-700 rounded p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $job->title }}</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Budget: ${{ $job->budget }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Worker: {{ $job->applications->where('status', 'accepted')->first()->worker->name }}</p>
                                </div>
                                <span class="bg-blue-100 text-blue-800 dark:bg-blue-800 dark:text-blue-100 text-xs px-2 py-1 rounded">In Progress</span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">View Details</a>
                                <a href="{{ route('messages.show', $job) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm ml-4">Message Worker</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if((!isset($postedJobs['posted']) || $postedJobs['posted']->count() == 0) &&
                    (!isset($postedJobs['in_progress']) || $postedJobs['in_progress']->count() == 0))
                    <p class="text-gray-500 dark:text-gray-400">You haven't posted any jobs yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>