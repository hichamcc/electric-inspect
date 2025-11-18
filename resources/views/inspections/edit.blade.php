<x-layouts.app :title="__('Edit Inspection')">
    <x-container class="py-6 lg:py-8">
        <div class="mb-6">
            <a href="{{ route('inspections.show', $inspection) }}" class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                <x-phosphor-arrow-left width="16" height="16" class="mr-1" />
                {{ __('Back to Inspection') }}
            </a>
        </div>

        <div class="max-w-3xl">
            <x-heading>{{ __('Perform Inspection') }}</x-heading>
            <x-text class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Fill in inspection parameters, results, and attach files') }}
            </x-text>

            @if($inspection->inspection_date->isFuture())
                <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                    <div class="flex">
                        <x-phosphor-warning width="20" height="20" class="text-yellow-600 dark:text-yellow-400 mr-2" />
                        <div>
                            <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">
                                {{ __('Inspection Date Not Reached') }}
                            </p>
                            <p class="text-xs text-yellow-700 dark:text-yellow-300 mt-1">
                                {{ __('This inspection is scheduled for') }} {{ $inspection->inspection_date->format('M d, Y') }}
                                @if($inspection->inspection_time)
                                    {{ __('at') }} {{ date('g:i A', strtotime($inspection->inspection_time)) }}
                                @endif
                                - <strong>{{ $inspection->inspection_date->isToday() ? 'Due today' : 'Due ' . $inspection->inspection_date->diffForHumans() }}</strong>
                            </p>
                        </div>
                    </div>
                </div>
            @elseif($inspection->inspection_date->isToday())
                <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex">
                        <x-phosphor-info width="20" height="20" class="text-blue-600 dark:text-blue-400 mr-2" />
                        <div>
                            <p class="text-sm font-medium text-blue-800 dark:text-blue-200">
                                {{ __('Inspection Scheduled Today') }}
                            </p>
                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                @if($inspection->inspection_time)
                                    {{ __('Scheduled at') }} {{ date('g:i A', strtotime($inspection->inspection_time)) }}
                                @else
                                    {{ __('This inspection is due today') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @endif

            <div class="mt-6 bg-white dark:bg-gray-900 shadow-sm ring-1 ring-gray-900/5 dark:ring-white/10 sm:rounded-xl">
                <form method="POST" action="{{ route('inspections.update', $inspection) }}" enctype="multipart/form-data" class="px-4 py-5 sm:p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-4">
                            <div class="flex">
                                <x-phosphor-warning width="20" height="20" class="text-red-600 dark:text-red-400 mr-2 flex-shrink-0" />
                                <div>
                                    <h3 class="text-sm font-medium text-red-800 dark:text-red-200">{{ __('There were some errors with your submission') }}</h3>
                                    <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        @if($technicians)
                        <div>
                            <x-label for="inspector_id" value="{{ __('Assign to Technician') }}" />
                            <x-select id="inspector_id" name="inspector_id" class="mt-1 block w-full">
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician->id }}" {{ old('inspector_id', $inspection->inspector_id) == $technician->id ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                    </option>
                                @endforeach
                            </x-select>
                            <x-error for='inspector_id' />
                        </div>
                        @endif

                        @if(auth()->user()->isTechnician())
                        <!-- Read-only fields for technicians -->
                        <div>
                            <x-label for="customer_display" value="{{ __('Customer') }}" />
                            <x-input name="customer_display" type="text" value="{{ $inspection->customer->company_name }}" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" readonly />
                            <input type="hidden" name="customer_id" value="{{ $inspection->customer_id }}" />
                        </div>

                        <div>
                            <x-label for="equipment_display" value="{{ __('Equipment') }}" />
                            <x-input name="equipment_display" type="text" value="{{ $inspection->equipment->equipment_type }}" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" readonly />
                            <input type="hidden" name="equipment_id" value="{{ $inspection->equipment_id }}" />
                        </div>
                        @else
                        <!-- Editable fields for admins -->
                        <div>
                            <x-label for="customer_id" value="{{ __('Customer') }}" />
                            <x-select id="customer_id" name="customer_id" class="mt-1 block w-full" required>
                                <option value="">Select a customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ old('customer_id', $inspection->customer_id) == $customer->id ? 'selected' : '' }}>
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
                        @endif

                        <div class="sm:col-span-2">
                            <x-label for="inspection_type" value="{{ __('Inspection Type') }}" />
                            <x-input id="inspection_type" class="mt-1 block w-full" type="text" name="inspection_type" :value="old('inspection_type', $inspection->inspection_type)" />
                            <x-error for='inspection_type' />
                        </div>

                        <div>
                            <x-label for="inspection_date" value="{{ __('Inspection Date') }}" />
                            @if(auth()->user()->isTechnician())
                                <x-input id="inspection_date" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" type="date" name="inspection_date" value="{{ $inspection->inspection_date->format('Y-m-d') }}" readonly />
                            @else
                                <x-input id="inspection_date" class="mt-1 block w-full" type="date" name="inspection_date" :value="old('inspection_date', $inspection->inspection_date->format('Y-m-d'))" required />
                            @endif
                            <x-error for='inspection_date' />
                        </div>

                        <div>
                            <x-label for="inspection_time" value="{{ __('Inspection Time') }}" />
                            @if(auth()->user()->isTechnician())
                                <x-input id="inspection_time" class="mt-1 block w-full bg-gray-100 dark:bg-gray-800" type="time" name="inspection_time" value="{{ $inspection->inspection_time }}" readonly />
                            @else
                                <x-input id="inspection_time" class="mt-1 block w-full" type="time" name="inspection_time" :value="old('inspection_time', $inspection->inspection_time)" />
                            @endif
                            <x-error for='inspection_time' />
                        </div>

                        <div>
                            <x-label for="status" value="{{ __('Status') }}" />
                            <x-select id="status" name="status" class="mt-1 block w-full" required>
                                <option value="scheduled" {{ old('status', $inspection->status) == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                <option value="in_progress" {{ old('status', $inspection->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $inspection->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ old('status', $inspection->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </x-select>
                            <x-error for='status' />
                        </div>

                        <div>
                            <x-label for="result" value="{{ __('Result') }}" />
                            <x-input id="result" class="mt-1 block w-full" type="text" name="result" :value="old('result', $inspection->result)" required placeholder="e.g., Pass, Fail, Conditional" />
                            <x-error for='result' />
                        </div>
                    </div>

                    <x-separator />

                    <!-- Dynamic Parameters Section -->
                    <div id="parameters-section" style="display: none;">
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-4">
                            <div class="flex items-start">
                                <x-phosphor-info width="20" height="20" class="text-blue-600 dark:text-blue-400 mr-2 mt-0.5" />
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-100">{{ __('Equipment-Specific Parameters') }}</h4>
                                    <p class="text-xs text-blue-800 dark:text-blue-200 mt-1">{{ __('Fill in the following parameters for this equipment type') }}</p>
                                </div>
                            </div>
                        </div>
                        <div id="parameters-container" class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <!-- Dynamic parameters will be inserted here -->
                        </div>
                        <x-separator class="mt-6" />
                    </div>

                    <div>
                        <x-label for="notes" value="{{ __('Notes') }}" />
                        <x-textarea id="notes" class="mt-1 block w-full" name="notes" rows="3">{{ old('notes', $inspection->notes) }}</x-textarea>
                        <x-error for='notes' />
                    </div>

                    <x-separator />

                    <div>
                        <x-label for="files" value="{{ __('Attach Additional Files') }}" />
                        <input id="files" type="file" name="files[]" multiple accept=".pdf,.jpg,.jpeg,.png" class="mt-1 block w-full text-sm text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-700 rounded-md cursor-pointer bg-gray-50 dark:bg-gray-900 focus:outline-none">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PDF, JPG, PNG (Max 10MB per file)</p>
                        <x-error for='files' />
                        @if($errors->has('files.*'))
                            <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                                @foreach($errors->get('files.*') as $messages)
                                    @foreach($messages as $message)
                                        <p>{{ $message }}</p>
                                    @endforeach
                                @endforeach
                            </div>
                        @endif
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        <a href="{{ route('inspections.show', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-700 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            {{ auth()->user()->isTechnician() ? __('Submit Inspection') : __('Save Inspection') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-container>

    @push('scripts')
    <script>
        // Equipment data with parameters
        const equipmentData = @json($equipmentData);

        // Existing parameter values
        const existingValues = @json($inspection->parameterValues->mapWithKeys(function($value) {
            return [$value->equipment_type_parameter_id => $value->value];
        }));

        document.addEventListener('DOMContentLoaded', function() {
            const parametersSection = document.getElementById('parameters-section');
            const parametersContainer = document.getElementById('parameters-container');

            @if(!auth()->user()->isTechnician())
            // Cascading dropdown logic only for admins
            const customerSelect = document.getElementById('customer_id');
            const equipmentSelect = document.getElementById('equipment_id');

            // Handle customer selection
            customerSelect.addEventListener('change', function() {
                const customerId = this.value;

                // Store currently selected equipment
                const currentEquipment = equipmentSelect.value;

                // Clear equipment dropdown
                equipmentSelect.innerHTML = '<option value="">Select equipment</option>';

                // Clear parameters
                parametersContainer.innerHTML = '';
                parametersSection.style.display = 'none';

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
                            // Re-select if it was previously selected
                            if (id == currentEquipment) {
                                option.selected = true;
                            }
                            equipmentSelect.appendChild(option);
                        });

                        // Trigger change if equipment was re-selected
                        if (currentEquipment && equipmentSelect.value) {
                            equipmentSelect.dispatchEvent(new Event('change'));
                        }
                    } else {
                        equipmentSelect.disabled = true;
                        equipmentSelect.innerHTML = '<option value="">No equipment available for this customer</option>';
                    }
                } else {
                    equipmentSelect.disabled = true;
                    equipmentSelect.innerHTML = '<option value="">Select a customer first</option>';
                }
            });

            // Handle equipment selection
            equipmentSelect.addEventListener('change', function() {
                const equipmentId = this.value;

                // Clear previous parameters
                parametersContainer.innerHTML = '';

                if (equipmentId && equipmentData[equipmentId]) {
                    const equipment = equipmentData[equipmentId];

                    if (equipment.parameters && equipment.parameters.length > 0) {
                        // Show parameters section
                        parametersSection.style.display = 'block';

                        // Add each parameter field
                        equipment.parameters.forEach(param => {
                            const existingValue = existingValues[param.id] || '';

                            const fieldHtml = `
                                <div>
                                    <label for="param_${param.id}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        ${param.label} ${param.is_required ? '<span class="text-red-500">*</span>' : ''}
                                    </label>
                                    <input type="text"
                                           id="param_${param.id}"
                                           name="parameters[${param.id}]"
                                           value="${existingValue}"
                                           ${param.is_required ? 'required' : ''}
                                           class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            `;
                            parametersContainer.insertAdjacentHTML('beforeend', fieldHtml);
                        });
                    } else {
                        // Hide parameters section if no parameters
                        parametersSection.style.display = 'none';
                    }
                } else {
                    // Hide parameters section if no equipment selected
                    parametersSection.style.display = 'none';
                }
            });

            // Trigger change event on page load if customer is selected
            if (customerSelect.value) {
                // Store the existing equipment selection
                const existingEquipment = '{{ old("equipment_id", $inspection->equipment_id) }}';

                // Trigger customer change to load equipment
                customerSelect.dispatchEvent(new Event('change'));

                // Wait for equipment dropdown to be populated, then select the equipment
                setTimeout(() => {
                    if (existingEquipment) {
                        equipmentSelect.value = existingEquipment;
                        equipmentSelect.dispatchEvent(new Event('change'));
                    }
                }, 50);
            }
            @else
            // For technicians, just load parameters for the existing equipment
            const existingEquipmentId = '{{ $inspection->equipment_id }}';
            if (existingEquipmentId && equipmentData[existingEquipmentId]) {
                const equipment = equipmentData[existingEquipmentId];

                if (equipment.parameters && equipment.parameters.length > 0) {
                    // Show parameters section
                    parametersSection.style.display = 'block';

                    // Add each parameter field
                    equipment.parameters.forEach(param => {
                        const existingValue = existingValues[param.id] || '';

                        const fieldHtml = `
                            <div>
                                <label for="param_${param.id}" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    ${param.label} ${param.is_required ? '<span class="text-red-500">*</span>' : ''}
                                </label>
                                <input type="text"
                                       id="param_${param.id}"
                                       name="parameters[${param.id}]"
                                       value="${existingValue}"
                                       ${param.is_required ? 'required' : ''}
                                       class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                        `;
                        parametersContainer.insertAdjacentHTML('beforeend', fieldHtml);
                    });
                }
            }
            @endif
        });
    </script>
    @endpush
</x-layouts.app>
