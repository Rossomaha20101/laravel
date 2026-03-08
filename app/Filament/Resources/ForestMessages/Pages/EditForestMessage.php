<?php

namespace App\Filament\Resources\ForestMessages\Pages;

use App\Filament\Resources\ForestMessages\ForestMessageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForestMessage extends EditRecord
{
    protected static string $resource = ForestMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
