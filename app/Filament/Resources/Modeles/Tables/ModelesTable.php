<?php

namespace App\Filament\Resources\Modeles\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ModelesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('name')->label('Modèle')->searchable()->description(fn ($record): ?string => $record->tagline_fr),
                TextColumn::make('car_slug')->label('Fiche liée')->placeholder('Showroom'),
                ToggleColumn::make('is_active')->label('Accueil'),
                ToggleColumn::make('in_showroom')->label('Showroom'),
                TextColumn::make('sr_sort')->label('Ordre showroom')->numeric()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Accueil'),
                TernaryFilter::make('in_showroom')->label('Showroom'),
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
