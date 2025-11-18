<x-layouts.app :title="$inspection->inspection_type">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <a href="{{ route('inspections.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <x-phosphor-arrow-left width="16" height="16" class="mr-1" />
                {{ __('Back to Inspections') }}
            </a>
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

        <div class="flex justify-between items-start mb-6">
            <div>
                <x-heading>{{ $inspection->inspection_type }}</x-heading>
                <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Inspection Date:') }} {{ $inspection->inspection_date->format('M d, Y') }}
                </x-text>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('inspections.export.pdf', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <x-phosphor-file-pdf width="16" height="16" class="mr-1" />
                    {{ __('Export PDF') }}
                </a>
                <a href="{{ route('inspections.preview.pdf', $inspection) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-purple-700 focus:bg-purple-700 active:bg-purple-900 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <x-phosphor-eye width="16" height="16" class="mr-1" />
                    {{ __('Preview PDF') }}
                </a>
                <a href="{{ route('inspections.edit', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <x-phosphor-pencil width="16" height="16" class="mr-1" />
                    {{ __('Edit') }}
                </a>
                <form action="{{ route('inspections.destroy', $inspection) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this inspection?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <x-phosphor-trash width="16" height="16" class="mr-1" />
                        {{ __('Delete') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Inspection Details') }}
                        </h3>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Type') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inspection->inspection_type }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Inspector') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inspection->inspector->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Date') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                                    {{ $inspection->inspection_date->format('M d, Y') }}
                                    @if ($inspection->inspection_time)
                                        at {{ date('g:i A', strtotime($inspection->inspection_time)) }}
                                    @endif
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Result') }}</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $inspection->result }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Status') }}</dt>
                                <dd class="mt-1">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        @if($inspection->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400
                                        @elseif($inspection->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400
                                        @elseif($inspection->status === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400
                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900/20 dark:text-gray-400
                                        @endif">
                                        {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                    </span>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Customer') }}</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('customers.show', $inspection->customer) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                        {{ $inspection->customer->company_name }}
                                    </a>
                                </dd>
                            </div>
                            <div class="sm:col-span-2">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Equipment') }}</dt>
                                <dd class="mt-1">
                                    <a href="{{ route('equipment.show', $inspection->equipment) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                        {{ $inspection->equipment->equipment_type }} - {{ $inspection->equipment->location }}
                                    </a>
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                @if ($inspection->parameterValues->count() > 0)
                    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Equipment Parameters') }}
                            </h3>
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-4">
                                @foreach ($inspection->parameterValues as $value)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $value->parameter->label }}</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $value->value ?: '-' }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    </div>
                @endif

                @if ($inspection->notes)
                    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Notes') }}
                            </h3>
                            <p class="text-sm text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $inspection->notes }}</p>
                        </div>
                    </div>
                @endif

                @if ($inspection->files->count() > 0)
                    <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                        <div class="px-4 py-5 sm:p-6">
                            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('Attached Files') }}
                            </h3>
                            <div class="space-y-3">
                                @foreach ($inspection->files as $file)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                        <div class="flex items-center space-x-3">
                                            @if (in_array($file->file_type, ['jpg', 'jpeg', 'png']))
                                                <x-phosphor-image width="24" height="24" class="text-blue-500" />
                                            @else
                                                <x-phosphor-file-pdf width="24" height="24" class="text-red-500" />
                                            @endif
                                            <div>
                                                <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $file->file_name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ number_format($file->file_size / 1024, 2) }} KB
                                                    • Uploaded {{ $file->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <a href="{{ Storage::url($file->file_path) }}" target="_blank" class="inline-flex items-center px-3 py-1 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                <x-phosphor-download width="14" height="14" class="mr-1" />
                                                View
                                            </a>
                                            <form action="{{ route('inspections.files.delete', [$inspection, $file]) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this file?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                    <x-phosphor-trash width="14" height="14" />
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('Quick Actions') }}
                        </h3>
                        <div class="space-y-2">
                            <a href="{{ route('equipment.show', $inspection->equipment) }}" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                                <x-phosphor-wrench width="16" height="16" class="mr-2" />
                                {{ __('View Equipment') }}
                            </a>
                            <a href="{{ route('customers.show', $inspection->customer) }}" class="flex items-center w-full px-3 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition">
                                <x-phosphor-user width="16" height="16" class="mr-2" />
                                {{ __('View Customer') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                            {{ __('File Statistics') }}
                        </h3>
                        <dl class="space-y-3">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __('Attached Files') }}</dt>
                                <dd class="mt-1 text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $inspection->files->count() }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </x-container>
</x-layouts.app>
