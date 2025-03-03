<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Profile Completion Alert -->
            @if(!auth()->user()->profile->bio)
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 dark:bg-yellow-800 dark:text-yellow-100 dark:border-yellow-600" role="alert">
                <p class="font-bold">Profile Incomplete</p>
                <p>Complete your profile to increase your chances of getting hired. <a href="{{ route('profile.edit') }}" class="underline">Update now</a></p>
            </div>
            @endif

            <!-- Job Applications Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">My Job Applications</h3>

                    @if(isset($appliedJobs['pending']) && $appliedJobs['pending']->count() > 0)
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Pending Applications</h4>
                    <div class="space-y-4 mb-6">
                        @foreach($appliedJobs['pending'] as $application)
                        <div class="border border-gray-200 dark:border-gray-700 rounded p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $application->job->title }}</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Budget: ${{ $application->job->budget }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Location: {{ $application->job->location }}</p>
                                </div>
                                <span class="bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100 text-xs px-2 py-1 rounded">Pending</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if(isset($appliedJobs['accepted']) && $appliedJobs['accepted']->count() > 0)
                    <h4 class="font-medium text-gray-700 dark:text-gray-300 mb-2">Accepted Applications</h4>
                    <div class="space-y-4">
                        @foreach($appliedJobs['accepted'] as $application)
                        <div class="border border-gray-200 dark:border-gray-700 rounded p-4">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $application->job->title }}</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Budget: ${{ $application->job->budget }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Location: {{ $application->job->location }}</p>
                                </div>
                                <span class="bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100 text-xs px-2 py-1 rounded">Accepted</span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('messages.show', $application->job_id) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm">Message Client</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @if((!isset($appliedJobs['pending']) || $appliedJobs['pending']->count() == 0) &&
                    (!isset($appliedJobs['accepted']) || $appliedJobs['accepted']->count() == 0))
                    <p class="text-gray-500 dark:text-gray-400">You haven't applied to any jobs yet.</p>
                    @endif
                </div>
            </div>

            <!-- Matching Jobs Section -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800 dark:text-gray-200">Jobs Matching Your Skills</h3>

                    @if($matchingJobs->count() > 0)
                    <div class="space-y-4">
                        @foreach($matchingJobs as $job)
                        <div class="border border-gray-200 dark:border-gray-700 rounded p-4">
                            <div>
                                <h5 class="font-semibold text-gray-800 dark:text-gray-200">{{ $job->title }}</h5>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Budget: ${{ $job->budget }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400">Location: {{ $job->location }}</p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">{{ Str::limit($job->description, 100) }}</p>
                            </div>
                            <div class="mt-3">
                                <a href="{{ route('jobs.show', $job) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded text-sm">View Details</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('jobs.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">Browse all available jobs →</a>
                    </div>
                    @else
                    <p class="text-gray-500 dark:text-gray-400">No matching jobs found. Update your skills to see jobs that match your expertise.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>