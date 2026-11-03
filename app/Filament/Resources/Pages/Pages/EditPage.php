<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Les pages du site se dépublient, elles ne se suppriment pas.
            DeleteAction::make()->hidden(fn (): bool => (bool) $this->record->is_system),
        ];
    }
}
