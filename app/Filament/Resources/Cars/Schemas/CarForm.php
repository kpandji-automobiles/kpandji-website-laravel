<?php

namespace App\Filament\Resources\Cars\Schemas;

use App\Models\Car;
use App\Support\PublicUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class CarForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Modèle')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nom')
                            ->required()
                            ->maxLength(120)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state): void {
                                if (blank($get('slug'))) {
                                    $set('slug', Str::slug((string) $state));
                                }
                            }),
                        TextInput::make('slug')
                            ->label('Adresse (slug)')
                            ->helperText('Utilisé dans l’URL : /modeles/<slug>.')
                            ->required()
                            ->maxLength(120)
                            ->alphaDash()
                            ->unique(ignoreRecord: true)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state)))
                            ->dehydrateStateUsing(fn (?string $state): string => Str::slug((string) $state)),
                        TextInput::make('category')->label('Catégorie')->maxLength(120)->placeholder('Pick-up, SUV…'),
                        TextInput::make('warranty')->label('Garantie')->maxLength(255)->placeholder('3 ans / 100 000 km'),
                        Textarea::make('description')->label('Description')->rows(4)->maxLength(5000)->columnSpanFull(),
                    ]),
                Section::make('Publication')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('is_active')->label('Visible sur le site')->default(true),
                        FileUpload::make('image')
                            ->label('Photo principale')
                            ->image()
                            ->disk('public')
                            ->directory('cars')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(8192)
                            ->helperText('Une photo livrée avec le site reste en place tant qu’aucune nouvelle n’est envoyée.')
                            ->formatStateUsing(fn (?string $state): ?string => PublicUpload::toDisk($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => PublicUpload::toStored($state))
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        FileUpload::make('brochure')
                            ->label('Brochure PDF')
                            ->disk('public')
                            ->directory('fiches')
                            ->acceptedFileTypes(['application/pdf'])
                            ->maxSize(20480)
                            ->formatStateUsing(fn (?string $state): ?string => PublicUpload::toDisk($state))
                            ->dehydrateStateUsing(fn (?string $state): ?string => PublicUpload::toStored($state))
                            ->dehydrated(fn (?string $state): bool => filled($state)),
                        Toggle::make('remove_brochure')
                            ->label('Retirer la brochure actuelle')
                            ->visible(fn (?Car $record): bool => filled($record?->brochure))
                            ->dehydrated(false),
                    ]),
                Section::make('Prix (FCFA)')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        TextInput::make('price')->label('Prix catalogue')->integer()->minValue(0),
                        TextInput::make('promo_price')->label('Prix promo')->integer()->minValue(0),
                        TextInput::make('campaign_price')->label('Avantage campagne')->integer()->minValue(0),
                        TextInput::make('maintenance_cost')->label('Coût d’entretien')->integer()->minValue(0),
                    ]),
                Section::make('Points forts')
                    ->columnSpan(1)
                    ->schema([
                        Repeater::make('highlights')
                            ->hiddenLabel()
                            ->simple(TextInput::make('highlight')->required()->maxLength(255))
                            ->addActionLabel('Ajouter un point fort')
                            ->defaultItems(0)
                            ->reorderable(),
                    ]),
                Section::make('Caractéristiques techniques')
                    ->columnSpanFull()
                    ->schema([
                        Textarea::make('specs')
                            ->hiddenLabel()
                            ->rows(8)
                            ->maxLength(20000)
                            ->helperText('« ## Groupe » sur une ligne, puis « Libellé : valeur » par ligne.')
                            ->formatStateUsing(fn ($state): string => Car::specsToText(is_array($state) ? $state : null))
                            ->dehydrateStateUsing(fn (?string $state): ?array => Car::parseSpecs($state)),
                    ]),
                Section::make('Versions')
                    ->columnSpanFull()
                    ->collapsible()
                    ->schema([
                        Repeater::make('versions')
                            ->hiddenLabel()
                            ->columns(4)
                            ->addActionLabel('Ajouter une version')
                            ->defaultItems(0)
                            ->itemLabel(fn (array $state): ?string => $state['version'] ?? null)
                            ->schema([
                                TextInput::make('version')->label('Version')->required()->maxLength(150),
                                TextInput::make('motorisation')->label('Motorisation')->maxLength(200),
                                TextInput::make('boite')->label('Boîte')->maxLength(80),
                                TextInput::make('transmission')->label('Transmission')->maxLength(80),
                                TextInput::make('promo_price')->label('Prix promo')->integer()->minValue(0),
                                TextInput::make('campaign_price')->label('Avantage campagne')->integer()->minValue(0),
                                TextInput::make('maintenance_cost')->label('Coût d’entretien')->integer()->minValue(0),
                            ]),
                    ]),
            ]);
    }
}
