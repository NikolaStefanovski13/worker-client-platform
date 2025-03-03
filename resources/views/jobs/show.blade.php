<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Job Details') }}
            </h2>
            <div>
                @if(auth()->user()->isClient() && auth()->id() === $job->client_id)
                <a href="{{ route('jobs.edit', $job) }}" class="inline-flex items-center px-4 py-2 mr-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600 focus:bg-gray-600 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Edit Job
                </a>
                @endif
                <a href="{{ route('jobs.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Back to Jobs
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                <p>{{ session('success') }}</p>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                <p>{{ session('error') }}</p>
            </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between flex-wrap">
                        <div>
                            <h1 class="text-2xl font-bold">{{ $job->title }}</h1>
                            <p class="text-sm text-gray-600 mt-1">Posted by {{ $job->client->name }} on {{ $job->created_at->format('M d, Y') }}</p>
                        </div>
                        <div class="mt-2 sm:mt-0">
                            <span class="px-3 py-1 text-sm rounded-full 
                                @if($job->status == 'posted') bg-green-100 text-green-800
                                @elseif($job->status == 'in_progress') bg-blue-100 text-blue-800
                                @elseif($job->status == 'completed') bg-gray-100 text-gray-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $job->status)) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-700">Budget</h3>
                            <p class="text-lg font-bold text-gray-900">${{ number_format($job->budget, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-700">Location</h3>
                            <p class="text-lg font-bold text-gray-900">{{ $job->location }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="font-semibold text-gray-700">Category</h3>
                            <p class="text-lg font-bold text-gray-900">{{ $job->category->name }} ({{ $job->category->category }})</p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-lg font-semibold">Description</h3>
                        <div class="mt-2 prose max-w-none">
                            {{ $job->description }}
                        </div>
                    </div>

                    @if(auth()->user()->isWorker() && $job->status === 'posted')
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold">Apply for this Job</h3>

                        @if($hasApplied)
                        <div class="mt-2 bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4">
                            <p>You have already applied for this job. Please wait for the client to review your application.</p>
                        </div>
                        @else
                        <form method="POST" action="{{ route('job-applications.store', $job) }}" class="mt-4">
                            @csrf
                            <div>
                                <x-input-label for="proposal" :value="__('Your Proposal')" />
                                <textarea id="proposal" name="proposal" rows="5" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('proposal') }}</textarea>
                                <x-input-error :messages="$errors->get('proposal')" class="mt-2" />
                            </div>

                            <div class="mt-4">
                                <x-input-label for="price_quoted" :value="__('Price Quote (USD)')" />
                                <x-text-input id="price_quoted" class="block mt-1 w-full" type="number" min="1" step="0.01" name="price_quoted" :value="old('price_quoted', $job->budget)" required />
                                <x-input-error :messages="$errors->get('price_quoted')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <x-primary-button>
                                    {{ __('Submit Application') }}
                                </x-primary-button>
                            </div>
                        </form>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isClient() && auth()->id() === $job->client_id)
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold">Applications ({{ $job->applications->count() }})</h3>

                        @if($job->applications->count() > 0)
                        <div class="mt-4 space-y-4">
                            @foreach($job->applications as $application)
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold">{{ $application->worker->name }}</h4>
                                        <p class="text-sm text-gray-600">Applied on {{ $application->created_at->format('M d, Y') }}</p>
                                        <p class="text-sm text-gray-600">Price Quote: ${{ number_format($application->price_quoted, 2) }}</p>
                                        <div class="mt-2 prose max-w-none text-sm">
                                            {{ $application->proposal }}
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                                    @if($application->status == 'pending') bg-yellow-100 text-yellow-800
                                                    @elseif($application->status == 'accepted') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </div>

                                @if($application->status === 'pending' && $job->status === 'posted')
                                <div class="mt-4 flex justify-end space-x-2">
                                    <form method="POST" action="{{ route('job-applications.accept', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <x-primary-button>
                                            {{ __('Accept') }}
                                        </x-primary-button>
                                    </form>

                                    <form method="POST" action="{{ route('job-applications.reject', $application) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-600 focus:bg-red-600 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('Reject') }}
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="mt-2 text-gray-500">No applications yet.</p>
                        @endif
                    </div>
                    @endif

                    @if(auth()->user()->isClient() && auth()->id() === $job->client_id && $job->status === 'in_progress')
                    <div class="mt-8 border-t border-gray-200 pt-6">
                        <form method="POST" action="{{ route('jobs.complete', $job) }}">
                            @csrf
                            @method('PATCH')
                            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4 mb-4">
                                <p>Is the job completed to your satisfaction? Mark as complete to release payment and leave a review.</p>
                            </div>
                            <div class="flex justify-end">
                                <x-primary-button>
                                    {{ __('Mark as Complete') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@if($job->status === 'completed')
<div class="mt-8 border-t border-gray-200 pt-6">
    <h3 class="text-lg font-semibold">Reviews</h3>

    @php
    $hasReviewed = $job->reviews()->where('reviewer_id', auth()->id())->exists();
    $reviews = $job->reviews;
    @endphp

    @if($reviews->count() > 0)
    <div class="mt-4 space-y-4">
        @foreach($reviews as $review)
        <div class="border border-gray-200 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="mr-4">
                    <p class="font-semibold">{{ $review->reviewer->name }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $review->created_at->format('M d, Y') }}
                    </p>
                </div>
                <div class="flex">
                    @for($i = 1; $i <= 5; $i++)
                        <svg class="w-5 h-5 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        @endfor
                </div>
            </div>
            <div class="prose max-w-none">
                <p>{{ $review->comment }}</p>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <p class="mt-2 text-gray-500">No reviews yet.</p>
    @endif

    @if(!$hasReviewed)
    <div class="mt-6">
        <h4 class="font-medium">Leave a Review</h4>
        <form method="POST" action="{{ route('reviews.store', $job) }}" class="mt-2">
            @csrf
            <div>
                <label for="rating" class="block text-sm font-medium text-gray-700">Rating</label>
                <div class="mt-1 flex items-center">
                    @for($i = 1; $i <= 5; $i++)
                        <input type="radio" id="rating{{ $i }}" name="rating" value="{{ $i }}" class="hidden peer" required {{ old('rating') == $i ? 'checked' : '' }}>
                        <label for="rating{{ $i }}" class="cursor-pointer p-1">
                            <svg class="w-8 h-8 text-gray-300 peer-checked:text-yellow-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                        </label>
                        @endfor
                </div>
                <x-input-error :messages="$errors->get('rating')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="comment" :value="__('Comment')" />
                <textarea id="comment" name="comment" rows="4" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('comment') }}</textarea>
                <x-input-error :messages="$errors->get('comment')" class="mt-2" />
            </div>

            <div class="mt-4 flex justify-end">
                <x-primary-button>
                    {{ __('Submit Review') }}
                </x-primary-button>
            </div>
        </form>
    </div>
    @endif
</div>
@endif