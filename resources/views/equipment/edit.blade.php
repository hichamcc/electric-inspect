<x-layouts.app :title="__('Edit Equipment')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <a href="{{ route('equipment.show', $equipment) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <x-phosphor-arrow-left width="16" height="16" class="mr-1" />
                {{ __('Back to Equipment') }}
            </a>
        </div>

        <div class="max-w-3xl">
            <x-heading>{{ __('Edit Equipment') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Update equipment information') }}
            </x-text>

            <div class="mt-6 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <form method="POST" action="{{ route('equipment.update', $equipment) }}" class="px-4 py-5 sm:p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div>
                        <x-label for="customer_id" value="{{ __('Customer') }}" />
                        <x-select id="customer_id" name="customer_id" class="mt-1 block w-full" required>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}" {{ old('customer_id', $equipment->customer_id) == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->company_name }}
                                </option>
                            @endforeach
                        </x-select>
                        <x-error for='customer_id' />
                    </div>

                    <x-separator />

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if($equipmentTypes->count() > 0)
                        <div class="sm:col-span-2">
                            <div class="flex justify-between items-center mb-1">
                                <x-label for="equipment_type_id" value="{{ __('Equipment Type') }}" />
                                <a href="{{ route('equipment-types.create') }}" class="text-xs text-blue-600 hover:text-blue-500 dark:text-blue-400">
                                    + {{ __('Add New Type') }}
                                </a>
                            </div>
                            <x-select id="equipment_type_id" name="equipment_type_id" class="mt-1 block w-full" required>
                                <option value="">{{ __('Select equipment type') }}</option>
                                @foreach ($equipmentTypes as $type)
                                    <option value="{{ $type->id }}" data-name="{{ $type->name }}" {{ old('equipment_type_id', $equipment->equipment_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-error for='equipment_type_id' />
                        </div>

                        <input type="hidden" id="equipment_type" name="equipment_type" value="{{ old('equipment_type', $equipment->equipment_type) }}" />
                        @else
                        <div class="sm:col-span-2">
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="flex">
                                    <x-phosphor-warning width="20" height="20" class="text-yellow-600 dark:text-yellow-400 mr-2" />
                                    <div>
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">{{ __('No equipment types available') }}</p>
                                        <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                            <a href="{{ route('equipment-types.create') }}" class="font-semibold underline">{{ __('Create an equipment type') }}</a> {{ __('to get started') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div>
                            <x-label for="manufacturer" value="{{ __('Manufacturer') }}" />
                            <x-input id="manufacturer" class="mt-1 block w-full" type="text" name="manufacturer" :value="old('manufacturer', $equipment->manufacturer)" />
                            <x-error for='manufacturer' />
                        </div>

                        <div>
                            <x-label for="model" value="{{ __('Model') }}" />
                            <x-input id="model" class="mt-1 block w-full" type="text" name="model" :value="old('model', $equipment->model)" />
                            <x-error for='model' />
                        </div>

                        <div>
                            <x-label for="serial_number" value="{{ __('Serial Number') }}" />
                            <x-input id="serial_number" class="mt-1 block w-full" type="text" name="serial_number" :value="old('serial_number', $equipment->serial_number)" />
                            <x-error for='serial_number' />
                        </div>

                        <div>
                            <x-label for="location" value="{{ __('Location') }}" />
                            <x-input id="location" class="mt-1 block w-full" type="text" name="location" :value="old('location', $equipment->location)" required />
                            <x-error for='location' />
                        </div>

                        <div>
                            <x-label for="installation_date" value="{{ __('Installation Date') }}" />
                            <x-input id="installation_date" class="mt-1 block w-full" type="date" name="installation_date" :value="old('installation_date', $equipment->installation_date?->format('Y-m-d'))" />
                            <x-error for='installation_date' />
                        </div>

                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <x-select id="status" name="status" class="mt-1 block w-full" required>
                                <option value="active" {{ old('status', $equipment->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $equipment->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="maintenance" {{ old('status', $equipment->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="retired" {{ old('status', $equipment->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                            </x-select>
                            <x-error for='status' />
                        </div>
                    </div>

                    <div>
                        <x-label for="description" value="{{ __('Description') }}" />
                        <x-textarea id="description" class="mt-1 block w-full" name="description" rows="2">{{ old('description', $equipment->description) }}</x-textarea>
                        <x-error for='description' />
                    </div>

                    <div>
                        <x-label for="notes" value="{{ __('Notes') }}" />
                        <x-textarea id="notes" class="mt-1 block w-full" name="notes" rows="3">{{ old('notes', $equipment->notes) }}</x-textarea>
                        <x-error for='notes' />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('equipment.show', $equipment) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Update Equipment') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-container>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const equipmentTypeSelect = document.getElementById('equipment_type_id');
            const equipmentTypeInput = document.getElementById('equipment_type');

            if (equipmentTypeSelect && equipmentTypeInput) {
                equipmentTypeSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const typeName = selectedOption.getAttribute('data-name') || '';
                    equipmentTypeInput.value = typeName;
                });

                // Trigger on page load if a type is already selected
                if (equipmentTypeSelect.value) {
                    equipmentTypeSelect.dispatchEvent(new Event('change'));
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
