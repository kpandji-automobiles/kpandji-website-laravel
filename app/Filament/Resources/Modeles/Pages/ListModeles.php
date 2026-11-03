<?php

namespace App\Filament\Resources\Modeles\Pages;

use App\Filament\Resources\Modeles\ModeleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListModeles extends ListRecords
{
    protected static string $resource = ModeleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
