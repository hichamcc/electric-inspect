<x-layouts.app :title="__('Create Organization')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <x-heading>{{ __('Create New Organization') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Create a new organization with an initial administrator account') }}
            </x-text>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <form method="POST" action="{{ route('super-admin.organizations.store') }}" class="px-4 py-5 sm:p-6">
                @csrf

                <!-- Organization Details Section -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Organization Details') }}</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-label for="name" :value="__('Organization Name')" />
                            <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                            <x-error for='name' />
                        </div>

                        <div>
                            <x-label for="slug" :value="__('Slug (URL Identifier)')" />
                            <x-input id="slug" class="block mt-1 w-full" type="text" name="slug" :value="old('slug')" required />
                            <x-text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Use lowercase letters, numbers, and hyphens only') }}
                            </x-text>
                            <x-error for='slug' />
                        </div>

                        <div>
                            <x-label for="email" :value="__('Organization Email')" />
                            <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" />
                            <x-error for='email' />
                        </div>

                        <div>
                            <x-label for="phone" :value="__('Phone Number')" />
                            <x-input id="phone" class="block mt-1 w-full" type="text" name="phone" :value="old('phone')" />
                            <x-error for='phone' />
                        </div>

                        <div>
                            <x-label for="address" :value="__('Address')" />
                            <x-textarea id="address" class="block mt-1 w-full" name="address" rows="3">{{ old('address') }}</x-textarea>
                            <x-error for='address' />
                        </div>

                        <div>
                            <x-label for="is_active" class="flex items-center">
                                <x-checkbox id="is_active" name="is_active" value="1" :checked="old('is_active', true)" />
                                <span class="ml-2">{{ __('Active') }}</span>
                            </x-label>
                            <x-text class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Inactive organizations cannot log in or access the system') }}
                            </x-text>
                            <x-error for='is_active' />
                        </div>
                    </div>
                </div>

                <!-- Administrator Account Section -->
                <div class="border-t border-gray-200 dark:border-gray-700 pt-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">{{ __('Administrator Account') }}</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <x-label for="admin_name" :value="__('Administrator Name')" />
                            <x-input id="admin_name" class="block mt-1 w-full" type="text" name="admin_name" :value="old('admin_name')" required />
                            <x-error for='admin_name' />
                        </div>

                        <div>
                            <x-label for="admin_email" :value="__('Administrator Email')" />
                            <x-input id="admin_email" class="block mt-1 w-full" type="email" name="admin_email" :value="old('admin_email')" required />
                            <x-error for='admin_email' />
                        </div>

                        <div>
                            <x-label for="admin_password" :value="__('Password')" />
                            <x-input id="admin_password" class="block mt-1 w-full" type="password" name="admin_password" required />
                            <x-error for='admin_password' />
                        </div>

                        <div>
                            <x-label for="admin_password_confirmation" :value="__('Confirm Password')" />
                            <x-input id="admin_password_confirmation" class="block mt-1 w-full" type="password" name="admin_password_confirmation" required />
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-end mt-8 gap-3">
                    <a href="{{ route('super-admin.organizations.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <x-button type="submit">
                        {{ __('Create Organization') }}
                    </x-button>
                </div>
            </form>
        </div>
    </x-container>
</x-layouts.app>
