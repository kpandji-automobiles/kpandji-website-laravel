<?php

namespace App\Filament\Resources\Pages\Tables;

use App\Models\Page;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->orderByDesc('is_system'))
            ->defaultSort('title')
            ->columns([
                TextColumn::make('title')->label('Page')->searchable()->sortable()->description(fn (Page $record): string => '/'.$record->slug),
                IconColumn::make('is_system')->label('Page du site')->boolean(),
                ToggleColumn::make('published')->label('Publiée'),
                ToggleColumn::make('in_nav')->label('Menu'),
                TextColumn::make('updated_at')->label('Modifiée')->since()->sortable(),
            ])
            ->filters([
                TernaryFilter::make('published')->label('Publication'),
                TernaryFilter::make('is_system')->label('Pages du site'),
            ])
            ->recordActions([
                Action::make('view')->label('Voir')->icon('heroicon-o-arrow-top-right-on-square')->url(fn (Page $record): string => $record->url(), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make()->hidden(fn (Page $record): bool => $record->is_system),
            ]);
    }
}
