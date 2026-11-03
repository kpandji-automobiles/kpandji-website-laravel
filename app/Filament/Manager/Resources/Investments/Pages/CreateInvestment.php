<?php

namespace App\Filament\Manager\Resources\Investments\Pages;

use App\Filament\Manager\Resources\Investments\InvestmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInvestment extends CreateRecord
{
    protected static string $resource = InvestmentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
