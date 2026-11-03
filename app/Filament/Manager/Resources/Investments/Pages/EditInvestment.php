<?php

namespace App\Filament\Manager\Resources\Investments\Pages;

use App\Filament\Manager\Resources\Investments\InvestmentResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditInvestment extends EditRecord
{
    protected static string $resource = InvestmentResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
