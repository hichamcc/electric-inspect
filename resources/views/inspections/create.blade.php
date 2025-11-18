<x-layouts.app :title="__('Create Inspection')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <a href="{{ route('inspections.index') }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <x-phosphor-arrow-left width="16" height="16" class="mr-1" />
                {{ __('Back to Inspections') }}
            </a>
        </div>

        <div class="max-w-3xl">
            <x-heading>{{ __('Schedule Inspection') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Schedule a new inspection. Parameters, results, and files will be added when performing the inspection.') }}
            </x-text>

            <div class="mt-6 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <form method="POST" action="{{ route('inspections.store') }}" enctype="multipart/form-data" class="px-4 py-5 sm:p-6 space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if($technicians)
                        <div>
                            <x-label for="inspector_id" value="{{ __('Assign to Technician') }}" />
                            <x-select id="inspector_id" name="inspector_id" class="mt-1 block w-full">
                                <option value="">Select a technician (optional)</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ old('inspector_id') == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-error for='inspector_id' />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">If not selected, it will be assigned to you</p>
                        </div>
                        @endif

                        <div>
                            <x-label for="customer_id" value="{{ __('Customer') }}" />
                            <x-select id="customer_id" name="customer_id" class="mt-1 block w-full" required>
                                <option value="">Select a customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', request('customer_id')) == $customer->id ? 'selected' : '' }}>
                                        {{ $customer->company_name }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-error for='customer_id' />
                        </div>

                        <div>
                            <x-label for="equipment_id" value="{{ __('Equipment') }}" />
                            <x-select id="equipment_id" name="equipment_id" class="mt-1 block w-full" required disabled>
                                <option value="">Select a customer first</option>
                            </x-select>
                            <x-error for='equipment_id' />
                        </div>

                        <div class="sm:col-span-2">
                            <x-label for="inspection_type" value="{{ __('Inspection Type') }}" />
                            <x-input id="inspection_type" class="mt-1 block w-full" type="text" name="inspection_type" :value="old('inspection_type')" placeholder="e.g., Annual Safety Inspection" />
                            <x-error for='inspection_type' />
                        </div>

                        <div>
                            <x-label for="inspection_date" value="{{ __('Inspection Date') }}" />
                            <x-input id="inspection_date" class="mt-1 block w-full" type="date" name="inspection_date" :value="old('inspection_date', date('Y-m-d'))" required />
                            <x-error for='inspection_date' />
                        </div>

                        <div>
                            <x-label for="inspection_time" value="{{ __('Inspection Time') }}" />
                            <x-input id="inspection_time" class="mt-1 block w-full" type="time" name="inspection_time" :value="old('inspection_time')" />
                            <x-error for='inspection_time' />
                        </div>

                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <x-select id="status" name="status" class="mt-1 block w-full" required>
                                <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ old('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </x-select>
                            <x-error for='status' />
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Mark as "In Progress" to fill parameters and results') }}</p>
                        </div>
                    </div>

                    <div>
                        <x-label for="notes" value="{{ __('Notes') }}" />
                        <x-textarea id="notes" class="mt-1 block w-full" name="notes" rows="3" placeholder="Any special instructions or notes for this inspection...">{{ old('notes') }}</x-textarea>
                        <x-error for='notes' />
                    </div>

                    <!-- Parameters Info Section -->
                    <div id="parameters-info" style="display: none;" class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                        <div class="flex items-start">
                            <x-phosphor-info width="20" height="20" class="text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" />
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">{{ __('Parameters to be filled during inspection') }}</h4>
                                <p class="text-xs text-blue-800 dark:text-blue-200 mt-1">{{ __('The inspector will need to fill the following parameters on the day of inspection:') }}</p>
                                <ul id="parameters-list" class="mt-2 space-y-1 text-xs text-blue-700 dark:text-blue-300">
                                    <!-- Parameters will be inserted here by JavaScript -->
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Schedule Inspection') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-container>

    @push('scripts')
    <script>
        // Equipment data
        const equipmentData = @json($equipmentData);

        document.addEventListener('DOMContentLoaded', function() {
            const customerSelect = document.getElementById('customer_id');
            const equipmentSelect = document.getElementById('equipment_id');
            const parametersInfo = document.getElementById('parameters-info');
            const parametersList = document.getElementById('parameters-list');

            // Handle customer selection
            customerSelect.addEventListener('change', function() {
                const customerId = this.value;

                // Clear equipment dropdown
                equipmentSelect.innerHTML = '<option value="">Select equipment</option>';

                // Hide parameters info when customer changes
                parametersInfo.style.display = 'none';
                parametersList.innerHTML = '';

                if (customerId) {
                    // Filter equipment by selected customer
                    const customerEquipment = Object.entries(equipmentData)
                        .filter(([id, data]) => data.customer_id == customerId);

                    if (customerEquipment.length > 0) {
                        equipmentSelect.disabled = false;
                        customerEquipment.forEach(([id, data]) => {
                            const option = document.createElement('option');
                            option.value = id;
                            option.textContent = data.equipment_type;
                            equipmentSelect.appendChild(option);
                        });
                    } else {
                        equipmentSelect.disabled = true;
                        equipmentSelect.innerHTML = '<option value="">No equipment available for this customer</option>';
                    }
                } else {
                    equipmentSelect.disabled = true;
                    equipmentSelect.innerHTML = '<option value="">Select a customer first</option>';
                }
            });

            // Handle equipment selection - show parameters info
            equipmentSelect.addEventListener('change', function() {
                const equipmentId = this.value;

                // Clear parameters list
                parametersList.innerHTML = '';

                if (equipmentId && equipmentData[equipmentId]) {
                    const equipment = equipmentData[equipmentId];

                    if (equipment.parameters && equipment.parameters.length > 0) {
                        // Show parameters info section
                        parametersInfo.style.display = 'block';

                        // Add each parameter to the list
                        equipment.parameters.forEach(param => {
                            const li = document.createElement('li');
                            li.className = 'flex items-center';
                            li.innerHTML = `
                                <svg class="w-3 h-3 mr-1.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span><strong>${param.label}</strong>${param.is_required ? ' <span class="text-red-500">*</span>' : ' (optional)'}</span>
                            `;
                            parametersList.appendChild(li);
                        });
                    } else {
                        // Hide parameters info if no parameters
                        parametersInfo.style.display = 'none';
                    }
                } else {
                    // Hide parameters info if no equipment selected
                    parametersInfo.style.display = 'none';
                }
            });

            // Trigger change event on page load if customer is pre-selected
            if (customerSelect.value) {
                customerSelect.dispatchEvent(new Event('change'));

                // If equipment is also pre-selected, select it after customer loads
                const preSelectedEquipment = '{{ old("equipment_id", request("equipment_id")) }}';
                if (preSelectedEquipment) {
                    setTimeout(() => {
                        equipmentSelect.value = preSelectedEquipment;
                        // Trigger equipment change to show parameters info
                        equipmentSelect.dispatchEvent(new Event('change'));
                    }, 100);
                }
            }
        });
    </script>
    @endpush
</x-layouts.app>
