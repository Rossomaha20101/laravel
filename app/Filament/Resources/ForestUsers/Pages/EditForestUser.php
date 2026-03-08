<?php

namespace App\Filament\Resources\ForestUsers\Pages;

use App\Filament\Resources\ForestUsers\ForestUserResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditForestUser extends EditRecord
{
    protected static string $resource = ForestUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
