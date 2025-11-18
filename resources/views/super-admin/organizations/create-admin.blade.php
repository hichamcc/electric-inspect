<x-layouts.app :title="__('Add Administrator')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <x-heading>{{ __('Add Administrator') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Create a new administrator account for') }} <strong>{{ $organization->name }}</strong>
            </x-text>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <form method="POST" action="{{ route('super-admin.organizations.admins.store', $organization) }}" class="px-4 py-5 sm:p-6">
                @csrf

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-label for="name" :value="__('Administrator Name')" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                        <x-error for='name' />
                    </div>

                    <div>
                        <x-label for="email" :value="__('Email Address')" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                        <x-error for='email' />
                    </div>

                    <div>
                        <x-label for="password" :value="__('Password')" />
                        <x-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                        <x-error for='password' />
                    </div>

                    <div>
                        <x-label for="password_confirmation" :value="__('Confirm Password')" />
                        <x-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end mt-8 gap-3">
                    <a href="{{ route('super-admin.organizations.show', $organization) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <x-button type="submit">
                        {{ __('Create Administrator') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-container>
</x-layouts.app>
