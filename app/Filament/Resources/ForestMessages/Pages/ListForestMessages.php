<?php

namespace App\Filament\Resources\ForestMessages\Pages;

use App\Filament\Resources\ForestMessages\ForestMessageResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListForestMessages extends ListRecords
{
    protected static string $resource = ForestMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
