<x-layouts.app :title="__('Equipment Types')">
    <x-container class="py-6 lg:py-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <x-heading>{{ __('Equipment Types') }}</x-heading>
                <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Define equipment types with custom inspection parameters') }}
                </x-text>
            </div>
            <a href="{{ route('equipment-types.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <x-phosphor-plus width="16" height="16" class="mr-1" />
                {{ __('New Equipment Type') }}
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

        @if (session('error'))
            <div class="mb-4 rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                <div class="flex">
                    <x-phosphor-x-circle width="20" height="20" class="text-red-400" />
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl overflow-hidden">
            @if ($equipmentTypes->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100 sm:pl-6">
                                    {{ __('Name') }}
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Description') }}
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Parameters') }}
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    {{ __('Equipment Count') }}
                                </th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">{{ __('Actions') }}</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800 bg-white dark:bg-gray-900">
                            @foreach ($equipmentTypes as $type)
                                <tr>
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-100 sm:pl-6">
                                        {{ $type->name }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $type->description ? Str::limit($type->description, 50) : '-' }}
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        <span class="inline-flex items-center rounded-md bg-blue-50 dark:bg-blue-900/20 px-2 py-1 text-xs font-medium text-blue-700 dark:text-blue-400 ring-1 ring-inset ring-blue-700/10 dark:ring-blue-400/30">
                                            {{ $type->parameters->count() }} {{ __('parameters') }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $type->equipment()->count() }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('equipment-types.show', $type) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 mr-3">
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('equipment-types.edit', $type) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">
                                            {{ __('Edit') }}
                                        </a>
                                        <form action="{{ route('equipment-types.destroy', $type) }}" method="POST" class="inline" onsubmit="return confirm('{{ __('Are you sure you want to delete this equipment type?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                {{ __('Delete') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800 sm:px-6">
                    {{ $equipmentTypes->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <x-phosphor-package width="48" height="48" class="mx-auto text-gray-400" />
                    <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('No equipment types') }}</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Get started by creating a new equipment type.') }}</p>
                    <div class="mt-6">
                        <a href="{{ route('equipment-types.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            <x-phosphor-plus width="16" height="16" class="mr-1" />
                            {{ __('New Equipment Type') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </x-container>
</x-layouts.app>
