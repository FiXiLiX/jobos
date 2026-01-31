<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Livewire\Attributes\Validate;
use App\Settings\GeneralSettings;
use App\Models\FuelConsumption;
use App\Models\OilConsumption;
use Livewire\Attributes\On;

class CarData extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Car Data';
    protected static ?int $navigationSort = 100;
    protected static ?string $slug = 'car-data';
    protected string $view = 'filament.pages.car-data';

    #[Validate('integer|min:0')]
    public int $starting_distance_counter = 0;

    #[Validate('in:Kilometers,Miles')]
    public string $distance_unit = 'Kilometers';

    public string $activeTab = 'fuel'; // 'fuel' or 'oil'

    public bool $showModal = false;
    public ?int $editingId = null;

    #[Validate('date')]
    public string $fill_date = '';

    #[Validate('numeric|min:0')]
    public float $fill_amount = 0;

    #[Validate('numeric|min:0')]
    public float $fill_price = 0;

    #[Validate('integer|min:0')]
    public int $current_distance_counter = 0;

    public function mount(): void
    {
        $settings = app(GeneralSettings::class);
        $this->starting_distance_counter = $settings->starting_distance_counter;
        $this->distance_unit = $settings->distance_unit;
        $this->fill_date = now()->format('Y-m-d');
    }

    public function save(): void
    {
        $this->validate();

        $settings = app(GeneralSettings::class);
        $settings->starting_distance_counter = $this->starting_distance_counter;
        $settings->distance_unit = $this->distance_unit;
        $settings->save();

        $this->dispatch('notify', message: 'Car Data saved successfully');
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetFuelForm();
    }

    public function getConsumptions()
    {
        if ($this->activeTab === 'fuel') {
            return FuelConsumption::orderBy('fill_date', 'desc')->get();
        } else {
            return OilConsumption::orderBy('fill_date', 'desc')->get();
        }
    }

    public function openCreateModal(): void
    {
        $this->resetFuelForm();
        $this->showModal = true;
        $this->editingId = null;
    }

    public function openEditModal(int $id): void
    {
        $consumption = $this->activeTab === 'fuel' 
            ? FuelConsumption::find($id)
            : OilConsumption::find($id);
            
        if ($consumption) {
            $this->fill_date = $consumption->fill_date->format('Y-m-d');
            $this->fill_amount = $consumption->fill_amount;
            $this->fill_price = $consumption->fill_price;
            $this->current_distance_counter = $consumption->current_distance_counter;
            $this->editingId = $id;
            $this->showModal = true;
        }
    }

    public function saveFuel(): void
    {
        $this->validate([
            'fill_date' => 'date',
            'fill_amount' => 'numeric|min:0',
            'fill_price' => 'numeric|min:0',
            'current_distance_counter' => 'integer|min:0',
        ]);

        $modelClass = $this->activeTab === 'fuel' ? FuelConsumption::class : OilConsumption::class;
        
        if ($this->editingId) {
            $consumption = $modelClass::find($this->editingId);
            $consumption->update([
                'fill_date' => $this->fill_date,
                'fill_amount' => $this->fill_amount,
                'fill_price' => $this->fill_price,
                'current_distance_counter' => $this->current_distance_counter,
            ]);
            $message = ucfirst($this->activeTab) . ' consumption updated successfully';
        } else {
            $modelClass::create([
                'fill_date' => $this->fill_date,
                'fill_amount' => $this->fill_amount,
                'fill_price' => $this->fill_price,
                'current_distance_counter' => $this->current_distance_counter,
            ]);
            $message = ucfirst($this->activeTab) . ' consumption created successfully';
        }

        $this->resetFuelForm();
        $this->showModal = false;
        $this->dispatch('notify', message: $message);
    }

    public function deleteConsumption(int $id): void
    {
        $modelClass = $this->activeTab === 'fuel' ? FuelConsumption::class : OilConsumption::class;
        $modelClass::destroy($id);
        $this->dispatch('notify', message: ucfirst($this->activeTab) . ' consumption deleted successfully');
    }

    private function resetFuelForm(): void
    {
        $this->fill_date = now()->format('Y-m-d');
        $this->fill_amount = 0;
        $this->fill_price = 0;
        $this->current_distance_counter = 0;
        $this->editingId = null;
    }
}
