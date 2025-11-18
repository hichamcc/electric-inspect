<x-layouts.app :title="__('Inspections')">
    <x-container class="py-6 lg:py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <x-heading>{{ __('Inspections') }}</x-heading>
                <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Manage inspection records') }}
                </x-text>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('inspections.export.excel', request()->all()) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <x-phosphor-file-xls width="16" height="16" class="mr-1" />
                    {{ __('Export Excel') }}
                </a>
                <a href="{{ route('inspections.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <x-phosphor-plus width="16" height="16" class="mr-1" />
                    {{ __('New Inspection') }}
                </a>
            </div>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 dark:bg-green-900/20 p-4">
                <div class="flex">
                    <x-phosphor-check-circle width="20" height="20" class="text-green-400" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <div class="px-4 py-5 sm:p-6">
                <form method="GET" action="{{ route('inspections.index') }}" class="mb-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
                        <div>
                            <x-input type="text" name="search" placeholder="Search inspections..." value="{{ request('search') }}" />
                        </div>
                        <div>
                            <x-select name="status">
                                <option value="">All Status</option>
                                <option value="scheduled" {{ request('status') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </x-select>
                        </div>
                        <div>
                            <x-select name="customer_id">
                                <option value="">All Customers</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                    </option>
                                @endforeach
                            </x-select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                {{ __('Search') }}
                            </button>
                            <a href="{{ route('inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">
                                {{ __('Reset') }}
                            </a>
                        </div>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Inspection
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Customer / Equipment
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Date
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Result
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Status
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse ($inspections as $inspection)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $inspection->inspection_type }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $inspection->inspector->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 dark:text-gray-100">
                                            <a href="{{ route('customers.show', $inspection->customer) }}" class="text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                                {{ $inspection->customer->company_name }}
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <a href="{{ route('equipment.show', $inspection->equipment) }}" class="hover:text-gray-700 dark:hover:text-gray-300">
                                                {{ $inspection->equipment->equipment_type }}
                                            </a>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="text-gray-900 dark:text-gray-100">
                                            {{ $inspection->inspection_date->format('M d, Y') }}
                                            @if($inspection->inspection_time)
                                                <span class="text-gray-500 dark:text-gray-400">{{ date('g:i A', strtotime($inspection->inspection_time)) }}</span>
                                            @endif
                                        </div>
                                        @if(!in_array($inspection->status, ['completed', 'cancelled']))
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
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $inspection->result }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                            @if($inspection->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                            @elseif($inspection->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                            @elseif($inspection->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                            @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                            @endif">
                                            {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('inspections.show', $inspection) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">View</a>
                                        <a href="{{ route('inspections.export.pdf', $inspection) }}" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 mr-3" title="Export PDF">PDF</a>

                                        @if(auth()->user()->isTechnician())
                                            @if(in_array($inspection->status, ['scheduled', 'in_progress']))
                                                <a href="{{ route('inspections.edit', $inspection) }}" class="text-orange-600 hover:text-orange-900 dark:text-orange-400 dark:hover:text-orange-300 mr-3">Submit</a>
                                            @endif
                                        @else
                                            <a href="{{ route('inspections.edit', $inspection) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</a>
                                            <form action="{{ route('inspections.destroy', $inspection) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this inspection?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                                        <x-phosphor-clipboard-text width="48" height="48" class="mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                        <p>No inspections found.</p>
                                        <a href="{{ route('inspections.create') }}" class="mt-2 inline-flex items-center text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                            <x-phosphor-plus width="16" height="16" class="mr-1" />
                                            Create your first inspection
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($inspections->hasPages())
                    <div class="mt-6">
                        {{ $inspections->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-container>
</x-layouts.app>
