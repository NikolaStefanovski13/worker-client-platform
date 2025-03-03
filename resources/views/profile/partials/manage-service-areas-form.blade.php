<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Service Areas') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add locations where you are available to provide your services.') }}
        </p>
    </header>

    <!-- Current Service Areas -->
    <div class="mt-6 space-y-6">
        @if(count($user->serviceAreas) > 0)
        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Your Current Service Areas') }}</h3>
        <div class="space-y-2">
            @foreach($user->serviceAreas as $area)
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-md">
                <div>
                    <span class="font-medium">{{ $area->city }}, {{ $area->state }}</span>
                    <span class="text-sm text-gray-600 dark:text-gray-400 ml-2">{{ $area->postal_code }}</span>
                </div>
                <form method="POST" action="{{ route('profile.service-areas.destroy', $area) }}">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300 text-sm">
                        {{ __('Remove') }}
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('You have not added any service areas yet.') }}</p>
        @endif
    </div>

    <!-- Add New Service Area Form -->
    <form method="post" action="{{ route('profile.service-areas.store') }}" class="mt-6 space-y-6">
        @csrf

        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Add New Service Area') }}</h3>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <x-input-label for="city" :value="__('City')" />
                <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('city')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="state" :value="__('State/Province')" />
                <x-text-input id="state" name="state" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('state')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="postal_code" :value="__('Postal Code')" />
                <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" required />
                <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Add Service Area') }}</x-primary-button>

            @if (session('status') === 'service-area-added')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400">{{ __('Service area added.') }}</p>
            @endif
        </div>
    </form>
</section>