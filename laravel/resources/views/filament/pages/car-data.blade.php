<x-filament-panels::page>
    <style>
        .car-data-section {
            background: #ffffff;
            color: #111827;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 24px;
        }
        .car-data-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #374151;
            margin-bottom: 8px;
        }
        .car-data-input {
            display: block;
            width: 100%;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            padding: 8px 12px;
            font-size: 14px;
            background-color: #ffffff;
            color: #111827;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            transition: all 0.2s;
            box-sizing: border-box;
        }
        .car-data-input:focus {
            border-color: #f59e0b;
            outline: none;
            box-shadow: 0 0 0 3px rgba(245,158,11,0.1);
        }
        .car-data-divider {
            border-top: 1px solid #e5e7eb;
        }
        .car-data-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            background-color: #f59e0b;
            padding: 10px 20px;
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }
        .car-data-button:hover {
            background-color: #d97706;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .car-data-button-danger {
            background-color: #ef4444;
            color: white;
        }
        .car-data-button-danger:hover {
            background-color: #dc2626;
        }
        .car-data-button-secondary {
            background-color: #6b7280;
            color: white;
        }
        .car-data-button-secondary:hover {
            background-color: #4b5563;
        }
        .car-data-error {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px;
            display: block;
        }
        .car-data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .car-data-table th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            font-size: 12px;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        .car-data-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }
        .car-data-table tr:hover {
            background-color: #f9fafb;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 50;
        }
        .modal-overlay.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 24px;
            max-width: 500px;
            width: 90%;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        /* Dark mode */
        .dark .car-data-section {
            background: #1f2937;
            color: #f3f4f6;
            box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        }
        .dark .car-data-label {
            color: #d1d5db;
        }
        .dark .car-data-input {
            background-color: #111827;
            color: #f3f4f6;
            border-color: #4b5563;
        }
        .dark .car-data-input:focus {
            border-color: #fbbf24;
            box-shadow: 0 0 0 3px rgba(251,191,36,0.2);
        }
        .dark .car-data-divider {
            border-top-color: #374151;
        }
        .dark .car-data-input option {
            background-color: #1f2937;
            color: #f3f4f6;
        }
        .dark .car-data-table th {
            background-color: #111827;
            color: #d1d5db;
            border-bottom-color: #374151;
        }
        .dark .car-data-table td {
            border-bottom-color: #374151;
        }
        .dark .car-data-table tr:hover {
            background-color: #111827;
        }
        .dark .modal-content {
            background: #1f2937;
            color: #f3f4f6;
        }
        .dark .modal-overlay {
            background: rgba(0,0,0,0.7);
        }
    </style>

    <!-- Settings Section -->
    <div class="car-data-section">
        <h2 style="font-size: 18px; font-weight: 600; margin-bottom: 16px;">Settings</h2>
        <form wire:submit="save" style="display: flex; flex-direction: column; gap: 24px;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
                <!-- Starting Distance Counter Input -->
                <div>
                    <label for="starting_distance_counter" class="car-data-label">
                        Starting Distance Counter
                    </label>
                    <input
                        type="number"
                        id="starting_distance_counter"
                        min="0"
                        wire:model.blur="starting_distance_counter"
                        placeholder="0"
                        class="car-data-input"
                    />
                    @error('starting_distance_counter')
                        <span class="car-data-error">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Distance Unit Select -->
                <div>
                    <label for="distance_unit" class="car-data-label">
                        Distance Unit
                    </label>
                    <select
                        id="distance_unit"
                        wire:model.blur="distance_unit"
                        class="car-data-input"
                        style="cursor: pointer;"
                    >
                        <option value="Kilometers">Kilometers</option>
                        <option value="Miles">Miles</option>
                    </select>
                    @error('distance_unit')
                        <span class="car-data-error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="car-data-divider" style="display: flex; justify-content: flex-end; gap: 12px; padding-top: 16px;">
                <button type="submit" class="car-data-button">
                    Save Settings
                </button>
            </div>
        </form>
    </div>

    <!-- Fuel/Oil Toggle -->
    <div style="display: flex; justify-content: center; gap: 8px;">
        <button 
            wire:click="setActiveTab('fuel')"
            type="button"
            style="
                padding: 10px 20px;
                border-radius: 8px;
                border: 2px solid @if($activeTab === 'fuel') #f59e0b @else #e5e7eb @endif;
                background-color: @if($activeTab === 'fuel') #f59e0b @else #ffffff @endif;
                color: @if($activeTab === 'fuel') #111827 @else #6b7280 @endif;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            "
            onmouseover="this.style.borderColor='#f59e0b';"
            onmouseout="this.style.borderColor=this.style.borderColor='@if($activeTab === 'fuel') #f59e0b @else #e5e7eb @endif';"
        >
            Fuel
        </button>
        <button 
            wire:click="setActiveTab('oil')"
            type="button"
            style="
                padding: 10px 20px;
                border-radius: 8px;
                border: 2px solid @if($activeTab === 'oil') #f59e0b @else #e5e7eb @endif;
                background-color: @if($activeTab === 'oil') #f59e0b @else #ffffff @endif;
                color: @if($activeTab === 'oil') #111827 @else #6b7280 @endif;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.2s;
            "
            onmouseover="this.style.borderColor='#f59e0b';"
            onmouseout="this.style.borderColor=this.style.borderColor='@if($activeTab === 'oil') #f59e0b @else #e5e7eb @endif';"
        >
            Oil
        </button>
    </div>

    <!-- Consumption Section -->
    <div class="car-data-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
            <h2 style="font-size: 18px; font-weight: 600;">@if($activeTab === 'fuel') Fuel @else Oil @endif Consumption</h2>
            <button wire:click="openCreateModal" class="car-data-button">
                + Add @if($activeTab === 'fuel') Fuel @else Oil @endif
            </button>
        </div>

        @if($this->getConsumptions()->count() > 0)
            <table class="car-data-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Price</th>
                        <th>Distance</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($this->getConsumptions() as $consumption)
                        <tr>
                            <td>{{ $consumption->fill_date->format('Y-m-d') }}</td>
                            <td>{{ number_format($consumption->fill_amount, 2) }}</td>
                            <td>{{ number_format($consumption->fill_price, 2) }}</td>
                            <td>{{ $consumption->current_distance_counter }}</td>
                            <td>
                                <button wire:click="openEditModal({{ $consumption->id }})" class="car-data-button-secondary" style="margin-right: 8px; padding: 6px 12px; font-size: 12px;">
                                    Edit
                                </button>
                                <button wire:click="deleteConsumption({{ $consumption->id }})" wire:confirm="Are you sure?" class="car-data-button-danger" style="padding: 6px 12px; font-size: 12px;">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p style="color: #6b7280; font-size: 14px; margin-top: 12px;">No @if($activeTab === 'fuel') fuel @else oil @endif consumption records yet.</p>
        @endif
    </div>

    <!-- Modal -->
    <div class="modal-overlay @if($showModal) active @endif">
        <div class="modal-content">
            <h3 style="font-size: 16px; font-weight: 600; margin-bottom: 16px;">
                @if($editingId) 
                    Edit @if($activeTab === 'fuel') Fuel @else Oil @endif Consumption
                @else 
                    Add @if($activeTab === 'fuel') Fuel @else Oil @endif Consumption
                @endif
            </h3>

            <form wire:submit="saveFuel" style="display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <label for="modal_fill_date" class="car-data-label">Fill Date</label>
                    <input type="date" id="modal_fill_date" wire:model.blur="fill_date" class="car-data-input" />
                    @error('fill_date') <span class="car-data-error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="modal_fill_amount" class="car-data-label">Amount (Liters/Gallons)</label>
                    <input type="number" id="modal_fill_amount" step="0.01" wire:model.blur="fill_amount" class="car-data-input" />
                    @error('fill_amount') <span class="car-data-error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="modal_fill_price" class="car-data-label">Price</label>
                    <input type="number" id="modal_fill_price" step="0.01" wire:model.blur="fill_price" class="car-data-input" />
                    @error('fill_price') <span class="car-data-error">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="modal_distance" class="car-data-label">Current Distance Counter</label>
                    <input type="number" id="modal_distance" wire:model.blur="current_distance_counter" class="car-data-input" />
                    @error('current_distance_counter') <span class="car-data-error">{{ $message }}</span> @enderror
                </div>

                <div style="display: flex; gap: 12px; justify-content: flex-end; margin-top: 12px;">
                    <button type="button" wire:click="$set('showModal', false)" class="car-data-button-secondary">
                        Cancel
                    </button>
                    <button type="submit" class="car-data-button">
                        @if($editingId) Update @else Create @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>
