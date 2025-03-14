<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Worker Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">{{ __('Your Job Applications') }}</h3>

                    @if(isset($appliedJobs['pending']) && $appliedJobs['pending']->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">{{ __('Pending Applications') }}</h4>
                        <div class="space-y-4">
                            @foreach($appliedJobs['pending'] as $application)
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between">
                                    <div>
                                        <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:underline text-lg font-medium">
                                            {{ $application->job->title }}
                                        </a>
                                        <p class="text-sm text-gray-600">
                                            {{ $application->job->location }} | ${{ number_format($application->job->budget, 2) }}
                                        </p>
                                        <p class="mt-2">{{ Str::limit($application->job->description, 100) }}</p>
                                    </div>
                                    <div class="text-sm">
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full">
                                            {{ __('Pending') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(isset($appliedJobs['accepted']) && $appliedJobs['accepted']->count() > 0)
                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-2">{{ __('Accepted Applications') }}</h4>
                        <div class="space-y-4">
                            @foreach($appliedJobs['accepted'] as $application)
                            <div class="p-4 border rounded-lg">
                                <div class="flex justify-between">
                                    <div>
                                        <a href="{{ route('jobs.show', $application->job) }}" class="text-blue-600 hover:underline text-lg font-medium">
                                            {{ $application->job->title }}
                                        </a>
                                        <p class="text-sm text-gray-600">
                                            {{ $application->job->location }} | ${{ number_format($application->job->budget, 2) }}
                                        </p>
                                        <p class="mt-2">{{ Str::limit($application->job->description, 100) }}</p>
                                    </div>
                                    <div class="text-sm">
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full">
                                            {{ __('Accepted') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if(!isset($appliedJobs['pending']) && !isset($appliedJobs['accepted']))
                    <p class="text-gray-500">{{ __('You have not applied to any jobs yet.') }}</p>
                    @endif

                    <div class="mt-8">
                        <h3 class="text-lg font-medium mb-4">{{ __('Jobs Matching Your Skills') }}</h3>

                        @if($matchingJobs->count() > 0)
                        <div class="space-y-4">
                            @foreach($matchingJobs as $job)
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
                                    </div>
                                    <div>
                                        <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600">
                                            {{ __('View Job') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-gray-500">{{ __('No jobs matching your skills currently.') }}</p>
                        @endif

                        <div class="mt-4">
                            <a href="{{ route('jobs.index') }}" class="text-blue-600 hover:underline">
                                {{ __('Browse all available jobs →') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>