<x-filament-panels::page>
    <style>
        .budget-summary-card {
            border-radius: 0.75rem;
            border: 1px solid;
            border-color: rgb(229 231 235);
            background: linear-gradient(to bottom right, rgb(255 255 255), rgb(249 250 251));
            padding: 1.5rem;
            box-shadow: 0 10px 15px -3px rgb(0 0 0 / 0.1);
        }
        .dark .budget-summary-card {
            border-color: rgb(55 65 81);
            background: linear-gradient(to bottom right, rgb(31 41 55), rgb(17 24 39));
        }
        .budget-summary-title {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(31 41 55);
            margin-bottom: 1rem;
        }
        .dark .budget-summary-title {
            color: rgb(243 244 246);
        }
        .budget-summary-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: rgb(255 255 255);
            border: 1px solid rgb(243 244 246);
            margin-bottom: 0.75rem;
        }
        .dark .budget-summary-row {
            background-color: rgb(31 41 55);
            border-color: rgb(55 65 81);
        }
        .budget-summary-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: rgb(75 85 99);
        }
        .dark .budget-summary-label {
            color: rgb(156 163 175);
        }
        .budget-summary-value-blue {
            font-size: 1rem;
            font-weight: 600;
            color: rgb(37 99 235);
        }
        .dark .budget-summary-value-blue {
            color: rgb(96 165 250);
        }
        .budget-summary-value-red {
            font-size: 1rem;
            font-weight: 600;
            color: rgb(220 38 38);
        }
        .dark .budget-summary-value-red {
            color: rgb(248 113 113);
        }
        .budget-summary-value-green {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(22 163 74);
        }
        .dark .budget-summary-value-green {
            color: rgb(134 239 172);
        }
        .budget-table-container {
            overflow-x: auto;
            border-radius: 0.5rem;
            border: 1px solid;
            border-color: rgb(229 231 235);
            background-color: rgb(255 255 255);
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            margin-top: 1.5rem;
        }
        .dark .budget-table-container {
            border-color: rgb(55 65 81);
            background-color: rgb(31 41 55);
        }
        .budget-summary-value-negative {
            font-size: 1.125rem;
            font-weight: 700;
            color: rgb(220 38 38);
        }
        .dark .budget-summary-value-negative {
            color: rgb(248 113 113);
        }
        .budget-table-title {
            padding: 0.75rem 1rem;
            font-weight: 600;
            color: rgb(17 24 39);
        }
        .dark .budget-table-title {
            color: rgb(243 244 246);
        }
        .budget-table thead {
            background-color: rgb(249 250 251);
        }
        .dark .budget-table thead {
            background-color: rgb(17 24 39);
        }
        .budget-table th {
            padding: 0.5rem 1rem;
            color: rgb(55 65 81);
            font-weight: 500;
        }
        .dark .budget-table th {
            color: rgb(209 213 219);
        }
        .budget-table tbody {
            border-top: 1px solid;
            border-color: rgb(243 244 246);
        }
        .dark .budget-table tbody {
            border-color: rgb(55 65 81);
        }
        .budget-table tbody tr {
            border-bottom: 1px solid;
            border-color: rgb(243 244 246);
        }
        .dark .budget-table tbody tr {
            border-color: rgb(55 65 81);
        }
        .budget-table td {
            padding: 0.75rem 1rem;
            color: rgb(17 24 39);
        }
        .dark .budget-table td {
            color: rgb(243 244 246);
        }
        .budget-table-label {
            font-weight: 500;
            color: rgb(55 65 81);
        }
        .dark .budget-table-label {
            color: rgb(209 213 219);
        }
        .budget-row-subcategory {
            background-color: rgb(243 244 246);
        }
        .dark .budget-row-subcategory {
            background-color: rgb(17 24 39);
        }
        .budget-row-subcategory td {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            color: rgb(55 65 81);
        }
        .dark .budget-row-subcategory td {
            color: rgb(209 213 219);
        }
        .budget-btn-group {
            display: inline-flex;
            gap: 0;
            border-radius: 0.375rem;
            overflow: hidden;
            border: 1px solid rgba(209, 213, 219, 0.5);
            background-color: rgba(249, 250, 251, 0.5);
            padding: 2px;
            margin-left: 0.75rem;
        }
        .dark .budget-btn-group {
            border-color: rgba(75, 85, 99, 0.5);
            background-color: rgba(31, 41, 55, 0.3);
        }
        .budget-btn-add {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgb(59 130 246);
            background-color: transparent;
            border: none;
            border-radius: 0;
            padding: 0.375rem;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .budget-btn-add:hover {
            background-color: rgba(59, 130, 246, 0.15);
            color: rgb(37 99 235);
        }
        .dark .budget-btn-add {
            color: rgb(147 197 253);
        }
        .dark .budget-btn-add:hover {
            background-color: rgba(147, 197, 253, 0.15);
            color: rgb(191 219 254);
        }
        .budget-btn-delete {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgb(239 68 68);
            background-color: transparent;
            border: none;
            border-radius: 0;
            padding: 0.375rem;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .budget-btn-delete:hover {
            background-color: rgba(239, 68, 68, 0.15);
            color: rgb(220 38 38);
        }
        .dark .budget-btn-delete {
            color: rgb(248 113 113);
        }
        .dark .budget-btn-delete:hover {
            background-color: rgba(248, 113, 113, 0.15);
            color: rgb(254 202 202);
        }
        .budget-btn-edit {
            font-size: 0.75rem;
            font-weight: 600;
            color: rgb(251 146 60);
            background-color: transparent;
            border: none;
            border-radius: 0;
            padding: 0.375rem;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease-in-out;
            cursor: pointer;
        }
        .budget-btn-edit:hover {
            background-color: rgba(251, 146, 60, 0.15);
            color: rgb(249 115 22);
        }
        .dark .budget-btn-edit {
            color: rgb(253 186 116);
        }
        .dark .budget-btn-edit:hover {
            background-color: rgba(253, 186, 116, 0.15);
            color: rgb(254 215 170);
        }
        .budget-input {
            width: 100%;
            border-radius: 0.375rem;
            border: 1px solid rgb(209 213 219);
            background-color: rgb(255 255 255);
            color: rgb(17 24 39);
            font-size: 0.875rem;
            padding: 0.5rem;
        }
        .dark .budget-input {
            border-color: rgb(75 85 99);
            background-color: rgb(55 65 81);
            color: rgb(243 244 246);
        }
        .budget-text-muted {
            color: rgb(107 114 128);
        }
        .dark .budget-text-muted {
            color: rgb(156 163 175);
        }
        .budget-empty-state {
            border-radius: 0.5rem;
            border: 1px solid rgb(229 231 235);
            background-color: rgb(249 250 251);
            padding: 1rem;
            color: rgb(55 65 81);
        }
        .dark .budget-empty-state {
            border-color: rgb(55 65 81);
            background-color: rgb(31 41 55);
            color: rgb(209 213 219);
        }
    </style>
    @php
        $monthLabel = \Carbon\Carbon::parse($this->currentMonth)->format('F Y');
    @endphp
    <div class="space-y-4">
        <div class="flex items-center justify-between gap-3" style="margin-bottom: 1rem;">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="previousMonth"
                    class="fi-btn fi-btn-size-md fi-btn-color-gray"
                >
                    ‹ Previous
                </button>
                <button
                    type="button"
                    wire:click="nextMonth"
                    class="fi-btn fi-btn-size-md fi-btn-color-gray"
                >
                    Next ›
                </button>
            </div>
        </div>

        @if ($this->budget)
            @php
                $settings = app(\App\Settings\GeneralSettings::class);
                $baseCurrency = \App\Models\Currency::where('code', $settings->default_currency)->first();
                $baseCurrencyCode = $baseCurrency ? $baseCurrency->code : 'USD';
                
                $totalBudgetedExpenses = $this->categories->sum(function ($category) {
                    return $category->subcategories->sum(function ($subcategory) {
                        $budgetedEntry = $subcategory->budgetedAmounts->first();
                        return $budgetedEntry ? $budgetedEntry->budgeted : 0;
                    });
                });
                $totalExpectedIncome = $this->incomes->sum(function ($income) {
                    $budgetedEntry = $income->budgetedAmounts->first();
                    return $budgetedEntry ? $budgetedEntry->expected : 0;
                });
                $freeFunds = $totalExpectedIncome - $totalBudgetedExpenses;
                
                // Current actual values
                $currentIncome = $this->incomes->sum(function ($income) {
                    return $income->incomes->sum('amount_normalized');
                });
                $currentExpenses = $this->categories->sum(function ($category) {
                    return $category->subcategories->sum(function ($subcategory) {
                        return $subcategory->expenses->sum('amount_normalized');
                    });
                });
                $currentAvailableFunds = $currentIncome - $currentExpenses;
            @endphp

            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem;">
                <!-- Monthly Summary Card -->
                <div class="budget-summary-card">
                    <h3 class="budget-summary-title">
                        Monthly Summary
                    </h3>
                    <div>
                        <div class="budget-summary-row">
                            <span class="budget-summary-label">Expected Income:</span>
                            <span class="budget-summary-value-blue">{{ number_format($totalExpectedIncome, 2) }} {{ $baseCurrencyCode }}</span>
                        </div>
                        <div class="budget-summary-row">
                            <span class="budget-summary-label">Budgeted Expenses:</span>
                            <span class="budget-summary-value-red">{{ number_format($totalBudgetedExpenses, 2) }} {{ $baseCurrencyCode }}</span>
                        </div>
                        <div class="budget-summary-row">
                            <span class="budget-summary-label">Available Funds:</span>
                            <span class="{{ $freeFunds >= 0 ? 'budget-summary-value-green' : 'budget-summary-value-negative' }}">
                                {{ $freeFunds >= 0 ? '+' : '' }}{{ number_format($freeFunds, 2) }} {{ $baseCurrencyCode }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Current Status Card -->
                <div class="budget-summary-card">
                    <h3 class="budget-summary-title">
                        Current Status
                    </h3>
                    <div>
                        <div class="budget-summary-row">
                            <span class="budget-summary-label">Current Income:</span>
                            <span class="budget-summary-value-blue">{{ number_format($currentIncome, 2) }} {{ $baseCurrencyCode }}</span>
                        </div>
                        <div class="budget-summary-row">
                            <span class="budget-summary-label">Current Expenses:</span>
                            <span class="budget-summary-value-red">{{ number_format($currentExpenses, 2) }} {{ $baseCurrencyCode }}</span>
                        </div>
                        <div class="budget-summary-row">
                            <span class="budget-summary-label">Available Funds:</span>
                            <span class="{{ $currentAvailableFunds >= 0 ? 'budget-summary-value-green' : 'budget-summary-value-negative' }}">
                                {{ $currentAvailableFunds >= 0 ? '+' : '' }}{{ number_format($currentAvailableFunds, 2) }} {{ $baseCurrencyCode }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="budget-table-container">
                <p class="budget-table-title">Expenses</p>
                <table class="min-w-full text-left text-sm budget-table fi-ta-table table-fixed">
                        <thead>
                            <tr>
                                <th colspan="6" style="padding: 0.75rem 1rem; font-size: 1rem; font-weight: 600;">
                                    <div style="display:flex;align-items:center;justify-content:center;gap:8px;">
                                        <span>{{ $monthLabel }}</span>
                                        <div style="position: relative;">
                                            <button
                                                type="button"
                                                wire:click.stop="$toggle('showOptions')"
                                                aria-label="Options"
                                                style="display:flex;align-items:center;justify-content:center;width:36px;height:36px;border:1px solid #d1d5db;border-radius:8px;background:#f9fafb;color:#111827;"
                                            >
                                                <x-filament::icon icon="heroicon-o-ellipsis-horizontal" class="w-5 h-5" />
                                            </button>
                                            @if($showOptions)
                                                <div style="position:absolute;right:0;margin-top:0.5rem;width:220px;border-radius:8px;box-shadow:0 10px 15px -3px rgba(0,0,0,0.1);background:#ffffff;border:1px solid rgba(0,0,0,0.05);z-index:10;">
                                                    <div style="padding:0.25rem 0;">
                                                        <button
                                                            type="button"
                                                            wire:click.stop="copyPreviousMonthBudget"
                                                            style="display:block;width:100%;text-align:left;padding:0.5rem 0.75rem;font-size:0.875rem;color:#111827;background:transparent;border:none;cursor:pointer;"
                                                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'"
                                                        >
                                                            <span style="display:flex;align-items:center;gap:0.5rem;">
                                                                <x-filament::icon icon="heroicon-o-arrow-up-tray" class="w-4 h-4" />
                                                                <span>Copy previous month budget</span>
                                                            </span>
                                                        </button>
                                                        <button
                                                            type="button"
                                                            wire:click.stop="resetBudget"
                                                            style="display:block;width:100%;text-align:left;padding:0.5rem 0.75rem;font-size:0.875rem;color:#111827;background:transparent;border:none;cursor:pointer;"
                                                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='transparent'"
                                                        >
                                                            <span style="display:flex;align-items:center;gap:0.5rem;">
                                                                <x-filament::icon icon="heroicon-o-arrow-path" class="w-4 h-4" />
                                                                <span>Reset budget</span>
                                                            </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th style="width: 25%;">Category</th>
                                <th style="width: 25%;">Budgeted</th>
                                <th style="width: 25%;">Spent</th>
                                <th style="width: 25%;">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php
                            $totalBudgeted = $this->categories->sum(function ($category) {
                                return $category->subcategories->sum(function ($subcategory) {
                                    $budgetedEntry = $subcategory->budgetedAmounts->first();
                                    return $budgetedEntry ? $budgetedEntry->budgeted : 0;
                                });
                            });
                            $totalSpent = $this->categories->sum(function ($category) {
                                return $category->subcategories->sum(function ($subcategory) {
                                    return $subcategory->expenses->sum('amount_normalized');
                                });
                            });
                        @endphp
                        <tr>
                            <td class="budget-table-label">Budgeted</td>
                            <td>{{ number_format($totalBudgeted, 2) }} {{ $baseCurrencyCode }}</td>
                            <td>{{ number_format($totalSpent, 2) }} {{ $baseCurrencyCode }}</td>
                            <td>{{ number_format($totalBudgeted - $totalSpent, 2) }} {{ $baseCurrencyCode }}</td>
                        </tr>
                        @foreach ($this->categories as $category)
                            @php
                                $categoryBudgeted = $category->subcategories->sum(function ($subcategory) {
                                    $budgetedEntry = $subcategory->budgetedAmounts->first();
                                    return $budgetedEntry ? $budgetedEntry->budgeted : 0;
                                });
                                $categorySpent = $category->subcategories->sum(function ($subcategory) {
                                    return $subcategory->expenses->sum('amount_normalized');
                                });
                            @endphp
                            <tr>
                                <td style="padding-left: .5em;">
                                    <div class="flex items-center">
                                        <span>{{ $category->name }}</span>
                                        <div class="budget-btn-group">
                                            <button
                                                type="button"
                                                wire:click="$dispatch('open-modal', { id: 'edit-category-{{ $category->id }}' })"
                                                class="budget-btn-edit"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                                    <path d="M5.433 13.917 3 14.5l.583-2.433a1.5 1.5 0 0 1 .399-.725l6.67-6.67a1.5 1.5 0 0 1 2.121 0l1.555 1.555a1.5 1.5 0 0 1 0 2.122l-6.67 6.67a1.5 1.5 0 0 1-.725.399Z" />
                                                    <path d="M2 15.75A.75.75 0 0 1 2.75 15h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 15.75Z" />
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="$dispatch('open-modal', { id: 'create-subcategory-{{ $category->id }}' })"
                                                class="budget-btn-add"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                                    <path d="M10.75 4.75a.75.75 0 0 0-1.5 0v4.5h-4.5a.75.75 0 0 0 0 1.5h4.5v4.5a.75.75 0 0 0 1.5 0v-4.5h4.5a.75.75 0 0 0 0-1.5h-4.5v-4.5Z" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ number_format($categoryBudgeted, 2) }} {{ $baseCurrencyCode }}</td>
                                <td>{{ number_format($categorySpent, 2) }} {{ $baseCurrencyCode }}</td>
                                <td>{{ number_format($categoryBudgeted - $categorySpent, 2) }} {{ $baseCurrencyCode }}</td>
                            </tr>

                            @foreach ($category->subcategories as $subcategory)
                                @php
                                    $budgetedEntry = $subcategory->budgetedAmounts->first();
                                    $budgetedValue = $budgetedEntry ? $budgetedEntry->budgeted : 0;
                                    $spentValue = $subcategory->expenses->sum('amount_normalized');
                                @endphp
                                <tr class="budget-row-subcategory">
                                    <td style="padding-left: 1em;">
                                        <div class="flex items-center">
                                            <span>{{ $subcategory->name }}</span>
                                            <div class="budget-btn-group">
                                                <button
                                                    type="button"
                                                    wire:click="$dispatch('open-modal', { id: 'edit-subcategory-{{ $subcategory->id }}' })"
                                                    class="budget-btn-edit"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                                        <path d="M5.433 13.917 3 14.5l.583-2.433a1.5 1.5 0 0 1 .399-.725l6.67-6.67a1.5 1.5 0 0 1 2.121 0l1.555 1.555a1.5 1.5 0 0 1 0 2.122l-6.67 6.67a1.5 1.5 0 0 1-.725.399Z" />
                                                        <path d="M2 15.75A.75.75 0 0 1 2.75 15h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 15.75Z" />
                                                    </svg>
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="$dispatch('open-modal', { id: 'delete-subcategory-{{ $subcategory->id }}' })"
                                                    class="budget-btn-delete"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                                        <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div x-data="{ editing: false, value: {{ $budgetedValue }} }">
                                            <template x-if="!editing">
                                            <span @click="editing = true" class="cursor-pointer block">{{ number_format($budgetedValue, 2) }} {{ $baseCurrencyCode }}</span>
                                            </template>
                                            <template x-if="editing">
                                                <div class="flex items-center gap-2">
                                                    <input
                                                        type="number"
                                                        step="0.01"
                                                        x-model="value"
                                                        @blur="$wire.updateBudgeted({{ $subcategory->id }}, parseFloat(value)); editing = false"
                                                        @keydown.enter="$wire.updateBudgeted({{ $subcategory->id }}, parseFloat(value)); editing = false"
                                                        @keydown.escape="editing = false"
                                                        x-init="$nextTick(() => $el.focus())"
                                                        class="budget-input"
                                                    />
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                    <td>{{ number_format($spentValue, 2) }} {{ $baseCurrencyCode }}</td>
                                    <td>{{ number_format($budgetedValue - $spentValue, 2) }} {{ $baseCurrencyCode }}</td>
                                </tr>
                            @endforeach

                            <x-filament::modal id="create-subcategory-{{ $category->id }}" width="md">
                                <x-slot name="heading">
                                    Add Subcategory to {{ $category->name }}
                                </x-slot>

                                <form wire:submit="createSubcategory({{ $category->id }})">
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                            type="text"
                                            wire:model="subcategoryName"
                                            placeholder="Subcategory name"
                                            required
                                        />
                                    </x-filament::input.wrapper>

                                    <div class="mt-4 flex justify-end gap-2">
                                        <x-filament::button
                                            type="button"
                                            color="gray"
                                            x-on:click="$dispatch('close-modal', { id: 'create-subcategory-{{ $category->id }}' })"
                                        >
                                            Cancel
                                        </x-filament::button>
                                        <x-filament::button type="submit">
                                            Create
                                        </x-filament::button>
                                    </div>
                                </form>
                            </x-filament::modal>

                            <x-filament::modal id="edit-category-{{ $category->id }}" width="md">
                                <x-slot name="heading">
                                    Edit Category Name
                                </x-slot>

                                <div x-data="{ name: @js($category->name) }" class="space-y-4">
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                            type="text"
                                            x-model="name"
                                            placeholder="Category name"
                                            required
                                        />
                                    </x-filament::input.wrapper>

                                    <div class="mt-2 flex justify-end gap-2">
                                        <x-filament::button
                                            type="button"
                                            color="gray"
                                            x-on:click="$dispatch('close-modal', { id: 'edit-category-{{ $category->id }}' })"
                                        >
                                            Cancel
                                        </x-filament::button>
                                        <x-filament::button
                                            type="button"
                                            color="primary"
                                            x-on:click="$wire.renameCategory({{ $category->id }}, name)"
                                        >
                                            Save
                                        </x-filament::button>
                                    </div>
                                </div>
                            </x-filament::modal>

                            @foreach ($category->subcategories as $subcategory)
                                <x-filament::modal id="delete-subcategory-{{ $subcategory->id }}" width="md">
                                    <x-slot name="heading">
                                        Delete Subcategory
                                    </x-slot>

                                    <div class="text-sm text-gray-700">
                                        Are you sure you want to delete <strong>{{ $subcategory->name }}</strong>?
                                    </div>

                                    <div class="mt-4 flex justify-end gap-2">
                                        <x-filament::button
                                            type="button"
                                            color="gray"
                                            x-on:click="$dispatch('close-modal', { id: 'delete-subcategory-{{ $subcategory->id }}' })"
                                        >
                                            Cancel
                                        </x-filament::button>
                                        <x-filament::button
                                            type="button"
                                            color="danger"
                                            wire:click="deleteSubcategory({{ $subcategory->id }})"
                                        >
                                            Delete
                                        </x-filament::button>
                                    </div>
                                </x-filament::modal>

                                <x-filament::modal id="edit-subcategory-{{ $subcategory->id }}" width="md">
                                    <x-slot name="heading">
                                        Edit Subcategory Name
                                    </x-slot>

                                    <div x-data="{ name: @js($subcategory->name) }" class="space-y-4">
                                        <x-filament::input.wrapper>
                                            <x-filament::input
                                                type="text"
                                                x-model="name"
                                                placeholder="Subcategory name"
                                                required
                                            />
                                        </x-filament::input.wrapper>

                                        <div class="mt-2 flex justify-end gap-2">
                                            <x-filament::button
                                                type="button"
                                                color="gray"
                                                x-on:click="$dispatch('close-modal', { id: 'edit-subcategory-{{ $subcategory->id }}' })"
                                            >
                                                Cancel
                                            </x-filament::button>
                                            <x-filament::button
                                                type="button"
                                                color="primary"
                                                x-on:click="$wire.renameSubcategory({{ $subcategory->id }}, name)"
                                            >
                                                Save
                                            </x-filament::button>
                                        </div>
                                    </div>
                                </x-filament::modal>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Income Table -->
            <div class="budget-table-container">
                <p class="budget-table-title">Income</p>
                <table class="min-w-full text-left text-sm budget-table fi-ta-table table-fixed">
                    <thead>
                        <tr>
                            <th style="width: 25%;">Category</th>
                            <th style="width: 25%;">Expected</th>
                            <th style="width: 25%;">Recieved</th>
                            <th style="width: 25%;">Difference</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalExpected = $this->incomes->sum(function ($income) {
                                $budgetedEntry = $income->budgetedAmounts->first();
                                return $budgetedEntry ? $budgetedEntry->expected : 0;
                            });
                            $totalReceived = $this->incomes->sum(function ($income) {
                                return $income->incomes->sum('amount_normalized');
                            });
                            $totalDifference = $totalExpected - $totalReceived;
                        @endphp
                        <tr>
                            <td class="budget-table-label">Budgeted</td>
                            <td>{{ number_format($totalExpected, 2) }} {{ $baseCurrencyCode }}</td>
                            <td>{{ number_format($totalReceived, 2) }} {{ $baseCurrencyCode }}</td>
                            <td>{{ number_format($totalDifference, 2) }} {{ $baseCurrencyCode }}</td>
                        </tr>
                        @foreach ($this->incomes as $income)
                            @php
                                $budgetedEntry = $income->budgetedAmounts->first();
                                $expectedValue = $budgetedEntry ? $budgetedEntry->expected : 0;
                                $receivedValue = $income->incomes->sum('amount_normalized');
                                $differenceValue = $expectedValue - $receivedValue;
                            @endphp
                            <tr>
                                <td style="padding-left: .5em;">
                                    <div class="flex items-center">
                                        <span>{{ $income->name }}</span>
                                        <div class="budget-btn-group">
                                            <button
                                                type="button"
                                                wire:click="$dispatch('open-modal', { id: 'edit-income-{{ $income->id }}' })"
                                                class="budget-btn-edit"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                                    <path d="M5.433 13.917 3 14.5l.583-2.433a1.5 1.5 0 0 1 .399-.725l6.67-6.67a1.5 1.5 0 0 1 2.121 0l1.555 1.555a1.5 1.5 0 0 1 0 2.122l-6.67 6.67a1.5 1.5 0 0 1-.725.399Z" />
                                                    <path d="M2 15.75A.75.75 0 0 1 2.75 15h14.5a.75.75 0 0 1 0 1.5H2.75A.75.75 0 0 1 2 15.75Z" />
                                                </svg>
                                            </button>
                                            <button
                                                type="button"
                                                wire:click="$dispatch('open-modal', { id: 'delete-income-{{ $income->id }}' })"
                                                class="budget-btn-delete"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 14px; height: 14px;">
                                                    <path fill-rule="evenodd" d="M8.75 1A2.75 2.75 0 0 0 6 3.75v.443c-.795.077-1.584.176-2.365.298a.75.75 0 1 0 .23 1.482l.149-.022.841 10.518A2.75 2.75 0 0 0 7.596 19h4.807a2.75 2.75 0 0 0 2.742-2.53l.841-10.52.149.023a.75.75 0 0 0 .23-1.482A41.03 41.03 0 0 0 14 4.193V3.75A2.75 2.75 0 0 0 11.25 1h-2.5ZM10 4c.84 0 1.673.025 2.5.075V3.75c0-.69-.56-1.25-1.25-1.25h-2.5c-.69 0-1.25.56-1.25 1.25v.325C8.327 4.025 9.16 4 10 4ZM8.58 7.72a.75.75 0 0 0-1.5.06l.3 7.5a.75.75 0 1 0 1.5-.06l-.3-7.5Zm4.34.06a.75.75 0 1 0-1.5-.06l-.3 7.5a.75.75 0 1 0 1.5.06l.3-7.5Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div x-data="{ editing: false, value: {{ $expectedValue }} }">
                                        <template x-if="!editing">
                                            <span @click="editing = true" class="cursor-pointer block">{{ number_format($expectedValue, 2) }} {{ $baseCurrencyCode }}</span>
                                        </template>
                                        <template x-if="editing">
                                            <div class="flex items-center gap-2">
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    x-model="value"
                                                    @blur="$wire.updateExpected({{ $income->id }}, parseFloat(value)); editing = false"
                                                    @keydown.enter="$wire.updateExpected({{ $income->id }}, parseFloat(value)); editing = false"
                                                    @keydown.escape="editing = false"
                                                    x-init="$nextTick(() => $el.focus())"
                                                    class="budget-input"
                                                />
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td>{{ number_format($receivedValue, 2) }} {{ $baseCurrencyCode }}</td>
                                <td>{{ number_format($differenceValue, 2) }} {{ $baseCurrencyCode }}</td>
                            </tr>
                        @endforeach

                        @foreach ($this->incomes as $income)
                            <x-filament::modal id="delete-income-{{ $income->id }}" width="md">
                                <x-slot name="heading">
                                    Delete Income Category
                                </x-slot>

                                <div class="text-sm text-gray-700">
                                    Are you sure you want to delete <strong>{{ $income->name }}</strong>?
                                </div>

                                <div class="mt-4 flex justify-end gap-2">
                                    <x-filament::button
                                        type="button"
                                        color="gray"
                                        x-on:click="$dispatch('close-modal', { id: 'delete-income-{{ $income->id }}' })"
                                    >
                                        Cancel
                                    </x-filament::button>
                                    <x-filament::button
                                        type="button"
                                        color="danger"
                                        wire:click="deleteIncome({{ $income->id }})"
                                    >
                                        Delete
                                    </x-filament::button>
                                </div>
                            </x-filament::modal>

                            <x-filament::modal id="edit-income-{{ $income->id }}" width="md">
                                <x-slot name="heading">
                                    Edit Income Category Name
                                </x-slot>

                                <div x-data="{ name: @js($income->name) }" class="space-y-4">
                                    <x-filament::input.wrapper>
                                        <x-filament::input
                                            type="text"
                                            x-model="name"
                                            placeholder="Income category name"
                                            required
                                        />
                                    </x-filament::input.wrapper>

                                    <div class="mt-2 flex justify-end gap-2">
                                        <x-filament::button
                                            type="button"
                                            color="gray"
                                            x-on:click="$dispatch('close-modal', { id: 'edit-income-{{ $income->id }}' })"
                                        >
                                            Cancel
                                        </x-filament::button>
                                        <x-filament::button
                                            type="button"
                                            color="primary"
                                            x-on:click="$wire.renameIncome({{ $income->id }}, name)"
                                        >
                                            Save
                                        </x-filament::button>
                                    </div>
                                </div>
                            </x-filament::modal>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="budget-empty-state">
                No budget found for {{ $monthLabel }}.
            </div>
        @endif
    </div>
</x-filament-panels::page>
