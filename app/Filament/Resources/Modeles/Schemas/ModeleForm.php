<?php

namespace App\Filament\Resources\Modeles\Schemas;

use App\Filament\Support\MediaPath;
use App\Models\Car;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class ModeleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextInput::make('name')->label('Nom')->required()->maxLength(120),
                        Select::make('car_slug')
                            ->label('Fiche modèle liée')
                            ->helperText('Lien « Découvrir ». Sans fiche visible, le lien mène au showroom.')
                            ->options(fn (): array => Car::orderBy('name')->pluck('name', 'slug')->all())
                            ->searchable(),
                        Grid::make(2)->schema([
                            Toggle::make('is_active')->label('Affiché sur l’accueil')->default(true),
                            Toggle::make('in_showroom')->label('Affiché dans le showroom'),
                        ]),
                    ]),
                Tabs::make()
                    ->persistTabInQueryString()
                    ->tabs([
                        Tab::make('Accueil')
                            ->schema([
                                Grid::make(2)->schema([
                                    Textarea::make('description_fr')->label('Description (FR)')->rows(4)->maxLength(3000),
                                    Textarea::make('description_en')->label('Description (EN)')->rows(4)->maxLength(3000),
                                ]),
                                self::bilingualList('characteristics', 'Caractéristiques', 'Ajouter une caractéristique'),
                                Grid::make(2)->schema([
                                    MediaPath::make('top_image', 'Image du haut (1/3)', 'modeles'),
                                    MediaPath::make('top_video', 'Vidéo du haut (2/3)', 'modeles', 'video'),
                                    MediaPath::make('bottom_left_image', 'Image bas gauche (16/10)', 'modeles'),
                                    MediaPath::make('bottom_right_image', 'Image bas droite (16/10)', 'modeles'),
                                ]),
                            ]),
                        Tab::make('Showroom')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('badge_fr')->label('Badge (FR)')->maxLength(60)->placeholder('Nouveau'),
                                    TextInput::make('badge_en')->label('Badge (EN)')->maxLength(60),
                                    TextInput::make('tagline_fr')->label('Accroche (FR)')->maxLength(200),
                                    TextInput::make('tagline_en')->label('Accroche (EN)')->maxLength(200),
                                    Textarea::make('sr_description_fr')->label('Description showroom (FR)')->rows(4)->maxLength(3000),
                                    Textarea::make('sr_description_en')->label('Description showroom (EN)')->rows(4)->maxLength(3000),
                                ]),
                                self::bilingualList('highlights', 'Points forts', 'Ajouter un point fort'),
                                MediaPath::make('brochure', 'Fiche technique (PDF)', 'fiches', 'pdf'),
                                Repeater::make('gallery')
                                    ->label('Galerie')
                                    ->defaultItems(0)
                                    ->reorderable()
                                    ->collapsible()
                                    ->addActionLabel('Ajouter une photo')
                                    ->itemLabel(fn (array $state): ?string => $state['alt'] ?? $state['src'] ?? null)
                                    ->columns(2)
                                    ->schema([
                                        MediaPath::make('src', 'Photo', 'showroom')->columnSpanFull(),
                                        TextInput::make('alt')->label('Texte alternatif')->maxLength(160),
                                        Select::make('tag')->label('Vue')->options(['ext' => 'Extérieur', 'int' => 'Intérieur'])->placeholder('Non classée'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    /** Liste de libellés bilingues stockée en JSON [{fr, en}, …]. */
    private static function bilingualList(string $field, string $label, string $addLabel): Repeater
    {
        return Repeater::make($field)
            ->label($label)
            ->defaultItems(0)
            ->reorderable()
            ->columns(2)
            ->addActionLabel($addLabel)
            ->schema([
                TextInput::make('fr')->label('FR')->maxLength(200),
                TextInput::make('en')->label('EN')->maxLength(200),
            ]);
    }
}
