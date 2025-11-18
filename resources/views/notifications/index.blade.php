<x-layouts.app :title="__('Notifications')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <x-heading>{{ __('Notifications') }}</x-heading>
                <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Stay updated with your inspection alerts and system notifications') }}
                </x-text>
            </div>
            <button onclick="markAllAsRead()" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <x-phosphor-check-circle width="16" height="16" class="mr-1" />
                {{ __('Mark All as Read') }}
            </button>
        </div>

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            @forelse($notifications as $notification)
                <div class="notification-item border-b border-gray-200 dark:border-gray-700 last:border-b-0 px-6 py-4 {{ !$notification->is_read ? 'bg-blue-50 dark:bg-blue-900/10' : '' }}" id="notification-{{ $notification->id }}">
                    <div class="flex items-start gap-4">
                        <!-- Icon based on type -->
                        <div class="flex-shrink-0 p-2 rounded-full {{ $notification->type === 'inspection_reminder' ? 'bg-blue-100 dark:bg-blue-900/20' : 'bg-gray-100 dark:bg-gray-800' }}">
                            @if($notification->type === 'inspection_reminder' || $notification->type === 'inspection_upcoming')
                                <x-phosphor-calendar width="20" height="20" class="text-blue-600 dark:text-blue-400" />
                            @elseif($notification->type === 'inspection_overdue')
                                <x-phosphor-warning width="20" height="20" class="text-orange-600 dark:text-orange-400" />
                            @else
                                <x-phosphor-info width="20" height="20" class="text-gray-600 dark:text-gray-400" />
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $notification->title }}
                                    </h3>
                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $notification->message }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-500">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2 ml-4">
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 text-xs">
                                            {{ __('View') }}
                                        </a>
                                    @endif
                                    @if(!$notification->is_read)
                                        <button onclick="markAsRead({{ $notification->id }})" class="text-green-600 hover:text-green-700 dark:text-green-400 dark:hover:text-green-300 text-xs">
                                            {{ __('Mark as Read') }}
                                        </button>
                                    @endif
                                    <button onclick="deleteNotification({{ $notification->id }})" class="text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 text-xs">
                                        {{ __('Delete') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <x-phosphor-bell-slash width="48" height="48" class="mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No notifications yet') }}</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-container>

    @push('scripts')
    <script>
        // Mark a single notification as read
        function markAsRead(notificationId) {
            fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                const notificationEl = document.getElementById(`notification-${notificationId}`);
                notificationEl.classList.remove('bg-blue-50', 'dark:bg-blue-900/10');
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }

        // Mark all notifications as read
        function markAllAsRead() {
            fetch('/notifications/read-all', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                location.reload();
            })
            .catch(error => console.error('Error:', error));
        }

        // Delete a notification
        function deleteNotification(notificationId) {
            if (confirm('{{ __('Are you sure you want to delete this notification?') }}')) {
                fetch(`/notifications/${notificationId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    document.getElementById(`notification-${notificationId}`).remove();
                })
                .catch(error => console.error('Error:', error));
            }
        }
    </script>
    @endpush
</x-layouts.app>
