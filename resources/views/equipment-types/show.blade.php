<x-layouts.app :title="$equipmentType->name">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                <a href="{{ route('equipment-types.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200">{{ __('Equipment Types') }}</a>
                <x-phosphor-caret-right width="16" height="16" />
                <span class="text-gray-900 dark:text-gray-100">{{ $equipmentType->name }}</span>
            </div>
            <div class="flex justify-between items-start">
                <div>
                    <x-heading>{{ $equipmentType->name }}</x-heading>
                    <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                        {{ $equipmentType->description ?? __('No description provided') }}
                    </x-text>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('equipment-types.edit', $equipmentType) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        <x-phosphor-pencil width="16" height="16" class="mr-1" />
                        {{ __('Edit') }}
                    </a>
                    <form action="{{ route('equipment-types.destroy', $equipmentType) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to delete this equipment type?') }}');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <x-phosphor-trash width="16" height="16" class="mr-1" />
                            {{ __('Delete') }}
                        </button>
                    </form>
                </div>
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

        <div class="grid gap-6 md:grid-cols-2">
            <!-- Parameters Section -->
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Inspection Parameters') }}
                    </h3>

                    @if ($equipmentType->parameters->count() > 0)
                        <div class="space-y-3">
                            @foreach ($equipmentType->parameters as $parameter)
                                <div class="flex items-start justify-between p-3 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $parameter->label }}
                                            </span>
                                            @if ($parameter->is_required)
                                                <span class="inline-flex items-center rounded-md bg-red-50 dark:bg-red-900/20 px-2 py-0.5 text-xs font-medium text-red-700 dark:text-red-400 ring-1 ring-inset ring-red-700/10 dark:ring-red-400/30">
                                                    {{ __('Required') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">
                                            {{ __('Field name:') }} <code class="text-blue-600 dark:text-blue-400">{{ $parameter->name }}</code>
                                        </p>
                                    </div>
                                    <x-phosphor-text-t width="20" height="20" class="text-gray-400" />
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <x-phosphor-list-dashes width="48" height="48" class="mx-auto text-gray-400" />
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('No parameters defined') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ __('Add parameters to customize inspections for this equipment type') }}</p>
                            <div class="mt-4">
                                <a href="{{ route('equipment-types.edit', $equipmentType) }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    {{ __('Add Parameters') }} →
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Equipment Using This Type -->
            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                        {{ __('Equipment Using This Type') }}
                    </h3>

                    @if ($equipmentType->equipment->count() > 0)
                        <div class="space-y-2">
                            @foreach ($equipmentType->equipment as $equipment)
                                <a href="{{ route('equipment.show', $equipment) }}" class="block p-3 bg-gray-50 dark:bg-gray-800 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ $equipment->equipment_type }}
                                            </p>
                                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                                {{ $equipment->customer->company_name }}
                                            </p>
                                        </div>
                                        <x-phosphor-caret-right width="16" height="16" class="text-gray-400" />
                                    </div>
                                </a>
                            @endforeach
                        </div>

                        @if ($equipmentType->equipment->count() > 5)
                            <div class="mt-4 text-center">
                                <a href="{{ route('equipment.index') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    {{ __('View All Equipment') }} →
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-8">
                            <x-phosphor-wrench width="48" height="48" class="mx-auto text-gray-400" />
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('No equipment using this type yet') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ __('Create equipment and assign this type') }}</p>
                            <div class="mt-4">
                                <a href="{{ route('equipment.create') }}" class="text-sm text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    {{ __('Create Equipment') }} →
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Type Information -->
        <div class="mt-6 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
            <div class="px-4 py-5 sm:p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100 mb-4">
                    {{ __('Type Information') }}
                </h3>
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Created') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $equipmentType->created_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Last Updated') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $equipmentType->updated_at->format('M d, Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Total Equipment') }}</dt>
                        <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $equipmentType->equipment->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </x-container>
</x-layouts.app>
