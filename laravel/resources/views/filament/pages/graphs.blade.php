<x-filament-panels::page>
    <div class="mb-4">
        <x-filament::input.wrapper inline-prefix>
            <x-filament::input.select wire:model.live="selectedMonth">
                @foreach ($this->monthOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </x-filament::input.select>
        </x-filament::input.wrapper>
    </div>
    <div class="grid grid-cols-2 gap-4" style="display: flex;justify-content: space-between; flex-wrap: wrap;">
        <div style="flex-grow: 3;" class="md:mr-4">
            @livewire(\App\Filament\Widgets\SpendingByCategory::class, ['filter' => $this->selectedMonth], key('spending-' . $this->selectedMonth))
        </div>
        <div style="flex-grow: 3;" class="md:ml-4">
            @livewire(\App\Filament\Widgets\IncomeByCategory::class, ['filter' => $this->selectedMonth], key('income-' . $this->selectedMonth))
        </div>
    </div>
    <div class="mt-4">
        @livewire(\App\Filament\Widgets\MonthSpendingChart::class, ['filter' => $this->selectedMonth], key('monthly-' . $this->selectedMonth))
    </div>
    <div class="mt-4">
        @livewire(\App\Filament\Widgets\NetWorthChart::class)
    </div>
</x-filament-panels::page>
