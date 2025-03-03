<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Additional Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Add more details about yourself or your business to help build trust with potential clients or workers.') }}
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <!-- Profile Image -->
        <div>
            <x-input-label for="profile_image" :value="__('Profile Image')" />
            <div class="mt-2 flex items-center">
                <div class="mr-4">
                    @if($user->profile_image)
                    <img src="{{ Storage::url($user->profile_image) }}" alt="{{ $user->name }}" class="h-20 w-20 object-cover rounded-full">
                    @else
                    <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-500 text-xl">{{ substr($user->name, 0, 1) }}</span>
                    </div>
                    @endif
                </div>
                <input id="profile_image" name="profile_image" type="file" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2 file:px-4
                    file:rounded-md file:border-0
                    file:text-sm file:font-semibold
                    file:bg-blue-50 file:text-blue-700
                    hover:file:bg-blue-100">
            </div>
            <x-input-error :messages="$errors->get('profile_image')" class="mt-2" />
        </div>

        <!-- Bio -->
        <div>
            <x-input-label for="bio" :value="__('Bio')" />
            <textarea id="bio" name="bio" rows="4" class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('bio', $user->profile->bio) }}</textarea>
            <x-input-error :messages="$errors->get('bio')" class="mt-2" />
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Brief description about yourself or your business. What makes you special?') }}
            </p>
        </div>

        <!-- Phone -->
        <div>
            <x-input-label for="phone" :value="__('Phone Number')" />
            <x-text-input id="phone" name="phone" type="tel" class="mt-1 block w-full" :value="old('phone', $user->profile->phone)" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Address -->
        <div>
            <x-input-label for="address" :value="__('Address')" />
            <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address', $user->profile->address)" />
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Save Information') }}</x-primary-button>

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