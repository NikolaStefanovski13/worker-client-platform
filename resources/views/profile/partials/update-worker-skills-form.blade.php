<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Professional Skills') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Update your professional skills to help clients find you for appropriate jobs.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-4">{{ __('Select your skills:') }}</h3>

            @foreach($skillsByCategory as $category => $skills)
            <div class="mb-4">
                <h4 class="text-sm font-semibold mb-2">{{ $category }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                    @foreach($skills as $skill)
                    <div class="flex items-center">
                        <input type="checkbox"
                            id="skill-{{ $skill->id }}"
                            name="skills[]"
                            value="{{ $skill->id }}"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            {{ in_array($skill->id, $userSkills) ? 'checked' : '' }}>
                        <label for="skill-{{ $skill->id }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                            {{ $skill->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <!-- Service Radius -->
        <div>
            <x-input-label for="service_radius" :value="__('Service Radius (miles)')" />
            <x-text-input id="service_radius"
                name="service_radius"
                type="number"
                min="1"
                max="100"
                class="mt-1 block w-full"
                :value="old('service_radius', $user->profile->service_radius)" />
            <x-input-error :messages="$errors->get('service_radius')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('How far are you willing to travel for jobs? (1-100 miles)') }}</p>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Skills') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>