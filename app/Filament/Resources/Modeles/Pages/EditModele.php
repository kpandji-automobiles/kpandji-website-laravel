<?php

namespace App\Filament\Resources\Modeles\Pages;

use App\Filament\Resources\Modeles\ModeleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditModele extends EditRecord
{
    protected static string $resource = ModeleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
