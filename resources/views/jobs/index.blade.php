<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Available Jobs') }}
            </h2>
            @if(auth()->user()->isClient())
            <a href="{{ route('jobs.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Post a Job
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <form method="GET" action="{{ route('jobs.index') }}" class="flex flex-wrap gap-4">
                        <div class="flex-grow min-w-[200px]">
                            <x-input-label for="search" :value="__('Search')" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 w-full" placeholder="Job title or description" value="{{ request('search') }}" />
                        </div>
                        <div class="flex-grow min-w-[200px]">
                            <x-input-label for="category" :value="__('Category')" />
                            <select id="category" name="category" class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Categories</option>
                                @foreach(\App\Models\Skill::select('category')->distinct()->pluck('category') as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex-grow min-w-[200px]">
                            <x-input-label for="location" :value="__('Location')" />
                            <x-text-input id="location" name="location" type="text" class="mt-1 w-full" placeholder="City, state, or zip" value="{{ request('location') }}" />
                        </div>
                        <div class="w-full flex justify-end">
                            <x-primary-button>
                                {{ __('Filter Jobs') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($jobs->count() > 0)
                    <div class="space-y-6">
                        @foreach($jobs as $job)
                        <div class="border border-gray-200 rounded-lg p-6 hover:bg-gray-50 transition">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-xl font-semibold">
                                        <a href="{{ route('jobs.show', $job) }}" class="text-blue-600 hover:underline">{{ $job->title }}</a>
                                    </h2>
                                    <div class="text-sm text-gray-600 mt-1">
                                        <p>Posted by {{ $job->client->name }} on {{ $job->created_at->format('M d, Y') }}</p>
                                        <p class="mt-1">
                                            <span class="inline-block mr-4">
                                                <i class="fas fa-money-bill-wave mr-1"></i> ${{ number_format($job->budget, 2) }}
                                            </span>
                                            <span class="inline-block mr-4">
                                                <i class="fas fa-map-marker-alt mr-1"></i> {{ $job->location }}
                                            </span>
                                            <span class="inline-block">
                                                <i class="fas fa-tag mr-1"></i> {{ $job->category->name }} ({{ $job->category->category }})
                                            </span>
                                        </p>
                                    </div>
                                    <p class="mt-2 text-gray-700">{{ Str::limit($job->description, 150) }}</p>
                                </div>
                                <span class="px-2 py-1 text-xs rounded-full 
                                            @if($job->status == 'posted') bg-green-100 text-green-800
                                            @elseif($job->status == 'in_progress') bg-blue-100 text-blue-800
                                            @elseif($job->status == 'completed') bg-gray-100 text-gray-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                    {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                                </span>
                            </div>
                            <div class="mt-4 flex justify-end">
                                <a href="{{ route('jobs.show', $job) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    View Details
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="mt-6">
                        {{ $jobs->links() }}
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-gray-500">No jobs found matching your criteria.</p>
                        <p class="mt-2">
                            @if(auth()->user()->isClient())
                            <a href="{{ route('jobs.create') }}" class="text-blue-600 hover:underline">Post a new job</a>
                            @else
                            <a href="{{ route('jobs.index') }}" class="text-blue-600 hover:underline">Clear filters</a>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>