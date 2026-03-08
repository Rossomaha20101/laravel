<?php

namespace App\Filament\Resources\AnimalTypes\Pages;

use App\Filament\Resources\AnimalTypes\AnimalTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAnimalType extends EditRecord
{
    protected static string $resource = AnimalTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
