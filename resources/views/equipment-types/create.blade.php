<x-layouts.app :title="__('Create Equipment Type')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mb-4">
                <a href="{{ route('equipment-types.index') }}" class="hover:text-gray-900 dark:hover:text-gray-200">{{ __('Equipment Types') }}</a>
                <x-phosphor-caret-right width="16" height="16" />
                <span class="text-gray-900 dark:text-gray-100">{{ __('Create') }}</span>
            </div>
            <x-heading>{{ __('Create Equipment Type') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Define a new equipment type with custom parameters for inspections') }}
            </x-text>
        </div>

        <form action="{{ route('equipment-types.store') }}" method="POST">
            @csrf

            <div class="bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <div class="px-4 py-5 sm:p-6 space-y-6">
                    <!-- Equipment Type Info -->
                    <div>
                        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">{{ __('Equipment Type Information') }}</h3>
                        <div class="mt-4 space-y-4">
                            <div>
                                <x-label for="name">{{ __('Name') }} <span class="text-red-500">*</span></x-label>
                                <x-input type="text" name="name" id="name" value="{{ old('name') }}" required />
                                <x-error for="name" />
                            </div>

                            <div>
                                <x-label for="description">{{ __('Description') }}</x-label>
                                <x-textarea name="description" id="description" rows="3">{{ old('description') }}</x-textarea>
                                <x-error for="description" />
                            </div>
                        </div>
                    </div>

                    <!-- Parameters Section -->
                    <div>
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-gray-100">{{ __('Inspection Parameters') }}</h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ __('Define fields that inspectors will fill during inspections') }}</p>
                            </div>
                            <button type="button" onclick="addParameter()" class="inline-flex items-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                <x-phosphor-plus width="16" height="16" class="mr-1" />
                                {{ __('Add Parameter') }}
                            </button>
                        </div>

                        <div id="parameters-container" class="space-y-4">
                            <!-- Parameters will be added here dynamically -->
                        </div>

                        <div id="no-parameters" class="text-center py-8 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-700">
                            <x-phosphor-list-dashes width="48" height="48" class="mx-auto text-gray-400" />
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">{{ __('No parameters added yet') }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-500">{{ __('Click "Add Parameter" to define inspection fields') }}</p>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-x-4 border-t border-gray-900/10 dark:border-white/10 px-4 py-4 sm:px-6">
                    <a href="{{ route('equipment-types.index') }}" class="text-sm font-semibold leading-6 text-gray-900 dark:text-gray-100">{{ __('Cancel') }}</a>
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        {{ __('Create Equipment Type') }}
                    </button>
                </div>
            </div>
        </form>
    </x-container>

    @push('scripts')
    <script>
        let parameterIndex = 0;

        function addParameter() {
            const container = document.getElementById('parameters-container');
            const noParameters = document.getElementById('no-parameters');

            // Hide "no parameters" message
            noParameters.style.display = 'none';

            const parameterHtml = `
                <div class="parameter-item bg-gray-50 dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700" data-index="${parameterIndex}">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="text-sm font-medium text-gray-900 dark:text-gray-100">Parameter ${parameterIndex + 1}</h4>
                        <button type="button" onclick="removeParameter(this)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                            <svg width="20" height="20" fill="currentColor" viewBox="0 0 256 256"><path d="M205.66,194.34a8,8,0,0,1-11.32,11.32L128,139.31,61.66,205.66a8,8,0,0,1-11.32-11.32L116.69,128,50.34,61.66A8,8,0,0,1,61.66,50.34L128,116.69l66.34-66.35a8,8,0,0,1,11.32,11.32L139.31,128Z"></path></svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Label <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="parameters[${parameterIndex}][label]"
                                   placeholder="e.g., Voltage (V), Serial Number"
                                   class="w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Display name for inspectors</p>
                        </div>
                        <div>
                            <label class="flex items-center">
                                <input type="checkbox"
                                       name="parameters[${parameterIndex}][is_required]"
                                       value="1"
                                       class="rounded border-gray-300 dark:border-gray-700 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ __('Required field') }}</span>
                            </label>
                        </div>
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', parameterHtml);
            parameterIndex++;
        }

        function removeParameter(button) {
            const container = document.getElementById('parameters-container');
            const noParameters = document.getElementById('no-parameters');
            const parameterItem = button.closest('.parameter-item');

            parameterItem.remove();

            // Show "no parameters" message if container is empty
            if (container.children.length === 0) {
                noParameters.style.display = 'block';
            }

            // Renumber remaining parameters
            Array.from(container.children).forEach((item, index) => {
                item.querySelector('h4').textContent = `Parameter ${index + 1}`;
            });
        }

        // Add one parameter by default
        document.addEventListener('DOMContentLoaded', function() {
            addParameter();
        });
    </script>
    @endpush
</x-layouts.app>
