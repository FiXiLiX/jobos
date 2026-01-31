<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class CarDataGraphs extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Car Data Graphs';
    protected static ?int $navigationSort = 101;
    protected static ?string $slug = 'car-data-graphs';
    protected string $view = 'filament.pages.car-data-graphs';
}
