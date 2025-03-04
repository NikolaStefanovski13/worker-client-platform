<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Matching Jobs Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Jobs Matching Your Skills') }}</h3>

                    @if(isset($matchingJobs) && $matchingJobs->count() > 0)
                    <div class="space-y-4">
                        @foreach($matchingJobs as $job)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                            <div class="flex justify-between">
                                <div>
                                    <h4 class="font-medium text-blue-600 hover:text-blue-800">
                                        <a href="{{ route('jobs.show', $job) }}">{{ $job->title }}</a>
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ Str::limit($job->description, 100) }}</p>
                                    <p class="text-sm mt-2">
                                        <span class="inline-block mr-3">
                                            <span class="font-medium">Budget:</span> ${{ number_format($job->budget, 2) }}
                                        </span>
                                        <span class="inline-block">
                                            <span class="font-medium">Location:</span> {{ $job->location }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <span class="inline-flex px-2 text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        {{ $job->category->name }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('jobs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Browse all available jobs →
                        </a>
                    </div>
                    @else
                    <p class="text-gray-500">No jobs matching your skills were found.</p>
                    <div class="mt-4">
                        <a href="{{ route('jobs.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                            Browse all available jobs →
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Applications Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">{{ __('Your Job Applications') }}</h3>

                    @if(isset($appliedJobs) && $appliedJobs->count() > 0)
                    <div class="mb-6">
                        @if(isset($appliedJobs['pending']))
                        <h4 class="font-medium text-gray-700 mb-2">Pending Applications</h4>
                        <div class="space-y-3 mb-6">
                            @foreach($appliedJobs['pending'] as $application)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h5 class="font-medium">
                                            <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $application->job->title }}
                                            </a>
                                        </h5>
                                        <p class="text-sm text-gray-600">Applied on {{ $application->created_at->format('M d, Y') }}</p>
                                        <p class="text-sm mt-1">
                                            <span class="font-medium">Quoted:</span> ${{ number_format($application->price_quoted, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($appliedJobs['accepted']))
                        <h4 class="font-medium text-gray-700 mb-2">Accepted Applications</h4>
                        <div class="space-y-3 mb-6">
                            @foreach($appliedJobs['accepted'] as $application)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h5 class="font-medium">
                                            <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $application->job->title }}
                                            </a>
                                        </h5>
                                        <p class="text-sm text-gray-600">Accepted on {{ $application->updated_at->format('M d, Y') }}</p>
                                        <p class="text-sm mt-1">
                                            <span class="font-medium">Quoted:</span> ${{ number_format($application->price_quoted, 2) }}
                                        </p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Accepted</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <a href="{{ route('messages.show', $application->job) }}" class="text-sm text-blue-600 hover:text-blue-800">
                                        Message client
                                    </a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @if(isset($appliedJobs['rejected']))
                        <h4 class="font-medium text-gray-700 mb-2">Rejected Applications</h4>
                        <div class="space-y-3">
                            @foreach($appliedJobs['rejected'] as $application)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between">
                                    <div>
                                        <h5 class="font-medium">
                                            <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:text-blue-800">
                                                {{ $application->job->title }}
                                            </a>
                                        </h5>
                                        <p class="text-sm text-gray-600">Rejected on {{ $application->updated_at->format('M d, Y') }}</p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                    @else
                    <p class="text-gray-500">You haven't applied to any jobs yet.</p>
                    @endif

                    <div class="mt-4">
                        <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Find Jobs to Apply
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>