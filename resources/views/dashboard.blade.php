<x-layouts.app :title="__('Dashboard')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <x-heading>{{ __('Dashboard') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Welcome back, ') }} {{ auth()->user()->name }}
            </x-text>
        </div>

        @if(auth()->user()->isTechnician())
        <!-- Technician Dashboard - Show Stats and Upcoming Inspections -->

        <!-- Stats Cards -->
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-purple-500 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                        <x-phosphor-clipboard-text width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Inspections') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['total'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.index') }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View all') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-green-500 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <x-phosphor-check-circle width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Completed') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['completed'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.index', ['status' => 'completed']) }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View completed') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-blue-500 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <x-phosphor-clock width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('In Progress') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['in_progress'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.index', ['status' => 'in_progress']) }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View in progress') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-orange-500 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                        <x-phosphor-calendar width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Upcoming') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['upcoming'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.index', ['status' => 'scheduled']) }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View scheduled') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>
        </div>

        <!-- Upcoming Inspections Table -->
        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Your Upcoming Inspections') }}
                </h3>

                @if($upcomingInspections->isEmpty())
                    <div class="text-center py-12">
                        <x-phosphor-clipboard-text width="48" height="48" class="mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No upcoming inspections') }}</p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ __('All caught up!') }}</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Inspection') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Customer & Equipment') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Scheduled Date') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Status') }}
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                        {{ __('Action') }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($upcomingInspections as $inspection)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $inspection->inspection_type }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ $inspection->customer->company_name }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $inspection->equipment->equipment_type }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="text-gray-900 dark:text-gray-100">
                                            {{ $inspection->inspection_date->format('M d, Y') }}
                                            @if($inspection->inspection_time)
                                                <span class="text-gray-500 dark:text-gray-400">{{ date('g:i A', strtotime($inspection->inspection_time)) }}</span>
                                            @endif
                                        </div>
                                        <div class="text-xs mt-0.5
                                            @if($inspection->inspection_date->isPast() && !$inspection->inspection_date->isToday())
                                                text-red-600 dark:text-red-400
                                            @elseif($inspection->inspection_date->isToday())
                                                text-blue-600 dark:text-blue-400 font-semibold
                                            @else
                                                text-gray-500 dark:text-gray-400
                                            @endif">
                                            @if($inspection->inspection_date->isPast() && !$inspection->inspection_date->isToday())
                                                Overdue ({{ $inspection->inspection_date->diffForHumans() }})
                                            @elseif($inspection->inspection_date->isToday())
                                                Due today
                                            @else
                                                Due {{ $inspection->inspection_date->diffForHumans() }}
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($inspection->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('inspections.edit', $inspection) }}" class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300">
                                            {{ __('Submit') }}
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        @else
        <!-- Admin/Super Admin Dashboard - Show Stats and Charts -->
        <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-blue-500 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                        <x-phosphor-users width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Customers') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['customers'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('customers.index') }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View all') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-green-500 bg-green-100 dark:bg-green-900/20 rounded-full">
                        <x-phosphor-wrench width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Equipment') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['equipment'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('equipment.index') }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View all') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-purple-500 bg-purple-100 dark:bg-purple-900/20 rounded-full">
                        <x-phosphor-clipboard-text width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Total Inspections') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['inspections'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.index') }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View all') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex items-center">
                    <div class="p-3 mr-4 text-orange-500 bg-orange-100 dark:bg-orange-900/20 rounded-full">
                        <x-phosphor-calendar width="24" height="24" />
                    </div>
                    <div>
                        <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ __('Upcoming Inspections') }}
                        </p>
                        <p class="text-3xl font-semibold text-gray-900 dark:text-gray-100">
                            {{ $stats['upcoming'] }}
                        </p>
                    </div>
                </div>
                <a href="{{ route('inspections.index', ['status' => 'scheduled']) }}" class="mt-4 text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400 inline-flex items-center">
                    {{ __('View scheduled') }}
                    <x-phosphor-arrow-right width="14" height="14" class="ml-1" />
                </a>
            </div>
        </div>

        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Quick Actions') }}
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="{{ route('customers.create') }}" class="flex items-center justify-center px-4 py-3 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded-lg transition">
                            <x-phosphor-plus width="18" height="18" class="mr-2 text-blue-600 dark:text-blue-400" />
                            <span class="text-sm font-medium text-blue-600 dark:text-blue-400">{{ __('New Customer') }}</span>
                        </a>
                        <a href="{{ route('equipment.create') }}" class="flex items-center justify-center px-4 py-3 bg-green-50 dark:bg-green-900/20 hover:bg-green-100 dark:hover:bg-green-900/30 rounded-lg transition">
                            <x-phosphor-plus width="18" height="18" class="mr-2 text-green-600 dark:text-green-400" />
                            <span class="text-sm font-medium text-green-600 dark:text-green-400">{{ __('New Equipment') }}</span>
                        </a>
                        <a href="{{ route('inspections.create') }}" class="flex items-center justify-center px-4 py-3 bg-purple-50 dark:bg-purple-900/20 hover:bg-purple-100 dark:hover:bg-purple-900/30 rounded-lg transition">
                            <x-phosphor-plus width="18" height="18" class="mr-2 text-purple-600 dark:text-purple-400" />
                            <span class="text-sm font-medium text-purple-600 dark:text-purple-400">{{ __('New Inspection') }}</span>
                        </a>
                        <a href="{{ route('inspections.index') }}" class="flex items-center justify-center px-4 py-3 bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition">
                            <x-phosphor-list width="18" height="18" class="mr-2 text-gray-600 dark:text-gray-400" />
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('View All') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('System Information') }}
                    </h3>
                    <dl class="space-y-3">
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('Organization') }}</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                @if (auth()->user()->isSuperAdmin())
                                    {{ __('Super Admin') }}
                                @else
                                    {{ auth()->user()->organization->name }}
                                @endif
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('Role') }}</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ ucfirst(str_replace('_', ' ', auth()->user()->role)) }}
                            </dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-sm text-gray-600 dark:text-gray-400">{{ __('User') }}</dt>
                            <dd class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ auth()->user()->email }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid gap-6 mb-8 md:grid-cols-2">
            <!-- Inspection Trends Chart -->
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Inspection Trends (Last 6 Months)') }}
                </h3>
                <div class="relative h-64">
                    <canvas id="inspectionTrendsChart"></canvas>
                </div>
            </div>

            <!-- Status Distribution Chart -->
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">
                        {{ __('Inspection Status Distribution') }}
                    </h3>
                    <div class="flex items-center gap-2">
                        <button onclick="changeMonth(-1)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                            <x-phosphor-caret-left width="20" height="20" class="text-gray-600 dark:text-gray-400" />
                        </button>
                        <span id="currentMonth" class="text-sm font-medium text-gray-700 dark:text-gray-300 min-w-[120px] text-center"></span>
                        <button onclick="changeMonth(1)" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                            <x-phosphor-caret-right width="20" height="20" class="text-gray-600 dark:text-gray-400" />
                        </button>
                    </div>
                </div>
                <div class="relative h-64">
                    <canvas id="statusDistributionChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Performance Metrics') }}
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Completion Rate') }}</div>
                        <div class="flex items-end gap-2">
                            <span class="text-3xl font-bold text-gray-900 dark:text-gray-100" id="completionRate">0%</span>
                            <span class="text-sm text-green-600 dark:text-green-400 mb-1">
                                <x-phosphor-arrow-up width="16" height="16" class="inline" />
                            </span>
                        </div>
                        <div class="mt-2 w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-green-500 h-2 rounded-full" id="completionBar" style="width: 0%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Average Inspections/Month') }}</div>
                        <div class="flex items-end gap-2">
                            <span class="text-3xl font-bold text-gray-900 dark:text-gray-100" id="avgPerMonth">0</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Last 6 months') }}</div>
                    </div>
                    <div>
                        <div class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ __('Response Time') }}</div>
                        <div class="flex items-end gap-2">
                            <span class="text-3xl font-bold text-gray-900 dark:text-gray-100">2.3</span>
                            <span class="text-sm text-gray-600 dark:text-gray-400 mb-1">{{ __('days avg') }}</span>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">{{ __('Time to completion') }}</div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
        <script>
            let trendsChart = null;
            let statusChart = null;
            let currentMonthOffset = 0; // 0 = current month, -1 = previous month, 1 = next month

            document.addEventListener('DOMContentLoaded', function() {
                // Get chart data from backend
                loadDashboardData();
            });

            function loadDashboardData(monthOffset = 0) {
                fetch('{{ route('dashboard.chart-data') }}?month_offset=' + monthOffset)
                    .then(response => response.json())
                    .then(data => {
                        // Inspection Trends Chart (only load once)
                        if (monthOffset === 0) {
                            const trendCtx = document.getElementById('inspectionTrendsChart');

                            // Destroy existing chart if it exists
                            if (trendsChart) {
                                trendsChart.destroy();
                            }

                            trendsChart = new Chart(trendCtx, {
                                type: 'line',
                                data: {
                                    labels: data.trends.labels,
                                    datasets: [{
                                        label: 'Inspections',
                                        data: data.trends.values,
                                        borderColor: 'rgb(59, 130, 246)',
                                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                        tension: 0.4,
                                        fill: true
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: {
                                            display: false
                                        }
                                    },
                                    scales: {
                                        y: {
                                            beginAtZero: true,
                                            ticks: {
                                                stepSize: 1
                                            }
                                        }
                                    }
                                }
                            });

                            // Update performance metrics (only once)
                            document.getElementById('completionRate').textContent = data.metrics.completionRate + '%';
                            document.getElementById('completionBar').style.width = data.metrics.completionRate + '%';
                            document.getElementById('avgPerMonth').textContent = data.metrics.avgPerMonth;
                        }

                        // Update month label
                        document.getElementById('currentMonth').textContent = data.status.month;

                        // Status Distribution Chart - destroy and recreate
                        if (statusChart) {
                            statusChart.destroy();
                        }

                        const statusCtx = document.getElementById('statusDistributionChart');

                        // Map colors based on status labels to match inspections index
                        const colorMap = {
                            'Scheduled': 'rgb(107, 114, 128)',      // Gray
                            'Completed': 'rgb(16, 185, 129)',       // Green
                            'In progress': 'rgb(59, 130, 246)',     // Blue
                            'Cancelled': 'rgb(239, 68, 68)',        // Red
                            'No Data': 'rgb(156, 163, 175)'         // Light gray for no data
                        };

                        const backgroundColors = data.status.labels.map(label => colorMap[label] || 'rgb(156, 163, 175)');

                        statusChart = new Chart(statusCtx, {
                            type: 'doughnut',
                            data: {
                                labels: data.status.labels,
                                datasets: [{
                                    data: data.status.values,
                                    backgroundColor: backgroundColors,
                                    borderWidth: 0
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                }
                            }
                        });
                    });
            }

            function changeMonth(offset) {
                currentMonthOffset += offset;
                loadDashboardData(currentMonthOffset);
            }
        </script>
        @endpush
        @endif
    </x-container>
</x-layouts.app>
