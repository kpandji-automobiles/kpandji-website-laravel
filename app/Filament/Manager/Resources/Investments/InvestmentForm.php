<?php

namespace App\Filament\Manager\Resources\Investments;

use App\Models\Investment;
use App\Models\RevenueEntry;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;

/** Champs partagés des investissements et des écritures (mêmes règles que l'ancien espace manager). */
final class InvestmentForm
{
    public const PROGRAMS = ['vehicule' => 'Véhicule', 'sira' => 'SIRA', 'privilege' => 'Privilège', 'autre' => 'Autre'];

    public const STATUSES = ['actif' => 'Actif', 'en_attente' => 'En attente', 'cloture' => 'Clôturé'];

    public const ENTRY_TYPES = ['revenu' => 'Revenu', 'versement' => 'Versement', 'frais' => 'Frais'];

    /** @return array<int, Component> */
    public static function investmentFields(bool $withClient): array
    {
        return array_values(array_filter([
            $withClient ? Select::make('user_id')
                ->label('Client')
                ->options(fn (): array => User::query()->where('role', 'client')->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required() : null,
            TextInput::make('label')->label('Libellé')->required()->maxLength(160),
            Select::make('program')->label('Programme')->options(self::PROGRAMS)->in(Investment::PROGRAMS),
            self::money('amount', 'Montant investi'),
            DatePicker::make('start_date')->label('Date de début')->native(false)->displayFormat('d/m/Y'),
            Select::make('status')->label('Statut')->options(self::STATUSES)->in(Investment::STATUSES)->default('actif')->required(),
            Textarea::make('notes')->label('Notes internes')->rows(3)->maxLength(3000)->columnSpanFull(),
        ]));
    }

    /** @return array<int, Component> */
    public static function entryFields(): array
    {
        return [
            DatePicker::make('entry_date')->label('Date')->native(false)->displayFormat('d/m/Y')->default(now())->required(),
            Select::make('type')->label('Type')->options(self::ENTRY_TYPES)->in(RevenueEntry::TYPES)->default('revenu')->required(),
            self::money('amount', 'Montant'),
            TextInput::make('description')->label('Description')->maxLength(255),
        ];
    }

    /** Montant en FCFA ; les espaces de saisie (« 1 500 000 ») sont retirés avant validation. */
    public static function money(string $name, string $label): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->suffix('FCFA')
            ->stripCharacters([' ', ' ', '.'])
            ->integer()
            ->minValue(0)
            ->maxValue(999999999999)
            ->required();
    }

    public static function fcfa(?int $amount): string
    {
        return number_format((int) $amount, 0, ',', ' ').' FCFA';
    }
}
