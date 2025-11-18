<x-layouts.app :title="__('Inspection Calendar')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <x-heading>{{ __('Inspection Calendar') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('View and manage inspections in calendar format') }}
            </x-text>
        </div>

        <!-- Calendar Legend -->
        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl mb-6 p-4">
            <div class="flex flex-wrap gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background-color: #6b7280;"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Scheduled') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background-color: #10b981;"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Completed') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background-color: #3b82f6;"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('In Progress') }}</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-4 h-4 rounded" style="background-color: #ef4444;"></div>
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Cancelled') }}</span>
                </div>
            </div>
        </div>

        <!-- Calendar Container -->
        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
            <div id="calendar"></div>
        </div>
    </x-container>

    @push('styles')
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css" rel="stylesheet">
    <style>
        /* FullCalendar Dark Mode Support */
        .dark #calendar {
            --fc-border-color: rgb(55 65 81);
            --fc-button-bg-color: rgb(55 65 81);
            --fc-button-border-color: rgb(75 85 99);
            --fc-button-hover-bg-color: rgb(75 85 99);
            --fc-button-hover-border-color: rgb(107 114 128);
            --fc-button-active-bg-color: rgb(107 114 128);
            --fc-button-active-border-color: rgb(107 114 128);
            --fc-event-bg-color: rgb(59 130 246);
            --fc-event-border-color: rgb(59 130 246);
            --fc-today-bg-color: rgba(59, 130, 246, 0.1);
        }

        .dark .fc {
            color: rgb(229 231 235);
        }

        .dark .fc-theme-standard td,
        .dark .fc-theme-standard th {
            border-color: rgb(55 65 81);
        }

        .dark .fc-theme-standard .fc-scrollgrid {
            border-color: rgb(55 65 81);
        }

        .dark .fc-col-header-cell {
            background-color: rgb(31 41 55);
        }

        .dark .fc-daygrid-day-number {
            color: rgb(229 231 235);
        }

        .dark .fc-day-today {
            background-color: rgba(59, 130, 246, 0.1) !important;
        }

        /* Event styling */
        .fc-event {
            cursor: pointer;
        }

        .fc-event:hover {
            opacity: 0.8;
        }
    </style>
    @endpush

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                height: 'auto',
                events: function(info, successCallback, failureCallback) {
                    fetch('{{ route('inspections.calendar.data') }}?start=' + info.startStr + '&end=' + info.endStr)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Calendar events loaded:', data);
                            successCallback(data);
                        })
                        .catch(error => {
                            console.error('Error loading calendar events:', error);
                            failureCallback(error);
                        });
                },
                eventClick: function(info) {
                    // Redirect to inspection details
                    if (info.event.url) {
                        window.location.href = info.event.url;
                        info.jsEvent.preventDefault();
                    }
                },
                eventDidMount: function(info) {
                    // Add tooltip
                    if (info.event.extendedProps.tooltip) {
                        info.el.title = info.event.extendedProps.tooltip;
                    }
                },
                // Color events based on status
                eventClassNames: function(arg) {
                    return ['fc-event-status-' + arg.event.extendedProps.status];
                },
                loading: function(isLoading) {
                    console.log(isLoading ? 'Loading calendar events...' : 'Calendar events loaded');
                }
            });

            calendar.render();
            console.log('Calendar rendered');
        });
    </script>
    @endpush
</x-layouts.app>
