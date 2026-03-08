<?php

namespace App\Filament\Resources\ForestUsers\Pages;

use App\Filament\Resources\ForestUsers\ForestUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForestUsers extends ListRecords
{
    protected static string $resource = ForestUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
