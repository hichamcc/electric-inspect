<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="layout sidebar min-h-screen bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300">
        <x-sidebar sticky stashable class="border-r border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900">
            <x-sidebar.toggle class="lg:hidden w-10 p-0">
                <x-phosphor-x aria-hidden="true" width="20" height="20" />
            </x-sidebar.toggle>

            <a href="{{ route('dashboard') }}" class="mr-5 flex items-center space-x-2">
                <x-app-logo />
            </a>

            <x-navlist>
                <x-navlist.group :heading="__('Platform')">
                    <x-navlist.item before="phosphor-house-line" :href="route('dashboard')" :current="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-navlist.item>
                </x-navlist.group>

                @if(auth()->user()->isSuperAdmin())
                    <x-navlist.group :heading="__('Administration')">
                        <x-navlist.item before="phosphor-building-office" :href="route('super-admin.organizations.index')" :current="request()->routeIs('super-admin.organizations.*')">
                            {{ __('Organizations') }}
                        </x-navlist.item>
                    </x-navlist.group>
                @else
                    <x-navlist.group :heading="__('Management')">
                        @if(!auth()->user()->isTechnician())
                        <x-navlist.item before="phosphor-users" :href="route('customers.index')" :current="request()->routeIs('customers.*')">
                            {{ __('Customers') }}
                        </x-navlist.item>
                        @endif
                        <x-navlist.item before="phosphor-wrench" :href="route('equipment.index')" :current="request()->routeIs('equipment.index') || request()->routeIs('equipment.show') || request()->routeIs('equipment.edit') || request()->routeIs('equipment.create')">
                            {{ __('Equipment') }}
                        </x-navlist.item>
                        @if(!auth()->user()->isTechnician())
                        <x-navlist.item before="phosphor-stack" :href="route('equipment-types.index')" :current="request()->routeIs('equipment-types.*')">
                            {{ __('Equipment Types') }}
                        </x-navlist.item>
                        @endif
                        <x-navlist.item before="phosphor-clipboard-text" :href="route('inspections.index')" :current="request()->routeIs('inspections.index')">
                            {{ __('Inspections') }}
                        </x-navlist.item>
                        <x-navlist.item before="phosphor-calendar" :href="route('inspections.calendar')" :current="request()->routeIs('inspections.calendar')">
                            {{ __('Calendar') }}
                        </x-navlist.item>
                        @if(!auth()->user()->isTechnician())
                        <x-navlist.item before="phosphor-user-list" :href="route('users.index')" :current="request()->routeIs('users.*')">
                            {{ __('Team Members') }}
                        </x-navlist.item>
                        @endif
                    </x-navlist.group>
                @endif
            </x-navlist>

            <x-spacer />

            <x-navlist>
                @if(!auth()->user()->isSuperAdmin())
                <!-- Notifications Bell -->
                <div class="relative px-3 py-2" id="notificationBell">
                    <button type="button" class="relative flex items-center w-full rounded-lg p-2 hover:bg-gray-800/5 dark:hover:bg-white/10" onclick="toggleNotifications()">
                        <x-phosphor-bell width="20" height="20" class="text-gray-600 dark:text-gray-300" />
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Notifications') }}</span>
                        <span id="notificationBadge" class="absolute top-1 left-7 hidden w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                </div>
                @endif

                @if(!auth()->user()->isTechnician() && !auth()->user()->isSuperAdmin())
                <x-navlist.item before="phosphor-building-office" :href="route('settings.organization.edit')" :current="request()->routeIs('settings.organization.*')">
                    {{ __('My Organization') }}
                </x-navlist.item>
                @endif
            </x-navlist>

            <x-popover align="bottom" justify="left">
                <button type="button" class="w-full group flex items-center rounded-lg p-1 hover:bg-gray-800/5 dark:hover:bg-white/10">
                    <span class="shrink-0 size-8 bg-gray-200 rounded-sm overflow-hidden dark:bg-gray-700">
                        <span class="w-full h-full flex items-center justify-center text-sm">
                            {{ auth()->user()->initials() }}
                        </span>
                    </span>
                    <span class="ml-2 text-sm text-gray-500 dark:text-white/80 group-hover:text-gray-800 dark:group-hover:text-white font-medium truncate">
                        {{ auth()->user()->name }}
                    </span>
                    <span class="shrink-0 ml-auto size-8 flex justify-center items-center">
                        <x-phosphor-caret-up-down aria-hidden="true" width="16" height="16" class="text-gray-400 dark:text-white/80 group-hover:text-gray-800 dark:group-hover:text-white" />
                    </span>
                </button>
                <x-slot:menu class="w-max">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            <span class="flex h-full w-full items-center justify-center rounded-lg bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>

                        <div class="grid flex-1 text-left text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                    <x-popover.separator />
                    <x-popover.item before="phosphor-gear-fine" href="/settings/profile">{{ __('Settings') }}</x-popover.item>
                    <x-popover.separator />
                    <x-form method="post" action="{{ route('logout') }}" class="w-full flex">
                        <x-popover.item before="phosphor-sign-out">{{ __('Log Out') }}</x-popover.item>
                    </x-form>
                </x-slot:menu>
            </x-popover>
        </x-sidebar>

        <!-- Mobile User Menu -->
        <x-header class="lg:hidden">
            <x-container class="min-h-14 flex items-center">
                <x-sidebar.toggle class="lg:hidden w-10 p-0">
                    <x-phosphor-list aria-hidden="true" width="20" height="20" />
                </x-sidebar.toggle>

                <x-spacer />

                <x-popover align="top" justify="right">
                    <button type="button" class="w-full group flex items-center rounded-lg p-1 hover:bg-gray-800/5 dark:hover:bg-white/10">
                        <span class="shrink-0 size-8 bg-gray-200 rounded-sm overflow-hidden dark:bg-gray-700">
                            <span class="w-full h-full flex items-center justify-center text-sm">
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>
                        <span class="shrink-0 ml-auto size-8 flex justify-center items-center">
                            <x-phosphor-caret-down width="16" height="16" class="text-gray-400 dark:text-white/80 group-hover:text-gray-800 dark:group-hover:text-white" />
                        </span>
                    </button>
                    <x-slot:menu>
                        <div class="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span class="flex h-full w-full items-center justify-center rounded-lg bg-gray-200 text-black dark:bg-gray-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>
                            <div class="grid flex-1 text-left text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                        <x-popover.separator />
                        <x-popover.item before="phosphor-gear-fine" href="/settings/profile">{{ __('Settings') }}</x-popover.item>
                        <x-popover.separator />
                        <x-form method="post" action="{{ route('logout') }}" class="w-full flex">
                            <x-popover.item before="phosphor-sign-out">{{ __('Log Out') }}</x-popover.item>
                        </x-form>
                    </x-slot:menu>
                </x-popover>
            </x-container>
        </x-header>

        {{ $slot }}

        <!-- Notification System Script -->
        @if(!auth()->user()->isSuperAdmin())
        <script>
            // Function to toggle notifications panel
            function toggleNotifications() {
                window.location.href = '{{ route('notifications.index') }}';
            }

            // Fetch unread notifications count
            function updateNotificationBadge() {
                fetch('{{ route('notifications.unread') }}')
                    .then(response => response.json())
                    .then(data => {
                        const badge = document.getElementById('notificationBadge');
                        if (data.unreadCount > 0) {
                            badge.classList.remove('hidden');
                        } else {
                            badge.classList.add('hidden');
                        }
                    })
                    .catch(error => console.error('Error fetching notifications:', error));
            }

            // Update badge on page load
            document.addEventListener('DOMContentLoaded', function() {
                updateNotificationBadge();
                // Update every 60 seconds
                setInterval(updateNotificationBadge, 60000);
            });
        </script>
        @endif

        @stack('scripts')
    </body>
</html>
