<?php

namespace App\Filament\Resources\AnimalTypes\Pages;

use App\Filament\Resources\AnimalTypes\AnimalTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAnimalTypes extends ListRecords
{
    protected static string $resource = AnimalTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
