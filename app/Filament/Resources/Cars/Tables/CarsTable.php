<?php

namespace App\Filament\Resources\Cars\Tables;

use App\Models\Car;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CarsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                ImageColumn::make('image')
                    ->label('')
                    ->state(fn (Car $record): ?string => $record->image ? asset($record->image) : null)
                    ->imageHeight(40),
                TextColumn::make('name')
                    ->label('Modèle')
                    ->description(fn (Car $record): ?string => $record->category)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('promo_price')
                    ->label('Prix promo')
                    ->formatStateUsing(fn (?int $state): string => Car::fcfa($state))
                    ->alignEnd()
                    ->sortable(),
                ToggleColumn::make('is_active')->label('Visible'),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Visibilité')->trueLabel('Visibles')->falseLabel('Masqués'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
