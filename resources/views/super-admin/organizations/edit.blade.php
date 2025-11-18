<x-layouts.app :title="__('Edit Organization')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <x-heading>{{ __('Edit Organization') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Update organization information') }}
            </x-text>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <form method="POST" action="{{ route('super-admin.organizations.update', $organization) }}" class="px-4 py-5 sm:p-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <x-label for="name" :value="__('Organization Name')" />
                        <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $organization->name)" required autofocus />
                        <x-error for='name' />
                    </div>

                    <div>
                        <x-label for="slug" :value="__('Slug (URL Identifier)')" />
                        <x-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug', $organization->slug)" required />
                        <x-text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Use lowercase letters, numbers, and hyphens only') }}
                        </x-text>
                        <x-error for='slug' />
                    </div>

                    <div>
                        <x-label for="email" :value="__('Organization Email')" />
                        <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $organization->email)" />
                        <x-error for='email' />
                    </div>

                    <div>
                        <x-label for="phone" :value="__('Phone Number')" />
                        <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone', $organization->phone)" />
                        <x-error for='phone' />
                    </div>

                    <div>
                        <x-label for="address" :value="__('Address')" />
                        <x-textarea id="address" class="block mt-1 w-full" name="address" rows="3">{{ old('address', $organization->address) }}</x-textarea>
                        <x-error for='address' />
                    </div>

                    <div>
                        <x-label for="is_active" class="flex items-center">
                            <x-checkbox id="is_active" name="is_active" value="1" :checked="old('is_active', $organization->is_active)" />
                            <span class="ml-2">{{ __('Active') }}</span>
                        </x-label>
                        <x-text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            {{ __('Inactive organizations cannot log in or access the system') }}
                        </x-text>
                        <x-error for='is_active' />
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between mt-8">
                    <form method="POST" action="{{ route('super-admin.organizations.destroy', $organization) }}" onsubmit="return confirm('{{ __('Are you sure you want to deactivate this organization?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Deactivate Organization') }}
                        </button>
                    </form>

                    <div class="flex gap-3">
                        <a href="{{ route('super-admin.organizations.show', $organization) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <x-button type="submit">
                            {{ __('Update Organization') }}
                        </x-button>
                    </div>
                </div>
            </form>
        </div>
    </x-container>
</x-layouts.app>
