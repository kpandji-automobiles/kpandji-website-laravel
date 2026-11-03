<?php

namespace App\Filament\Resources\Pages\Schemas;

use App\Filament\Support\MediaPath;
use App\Models\Page;
use App\Support\Cms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Contenu')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('title')->label('Titre')->required()->maxLength(160),
                        TextInput::make('slug')
                            ->label('Adresse (slug)')
                            ->prefix(url('/').'/')
                            ->helperText('Lettres minuscules, chiffres et tirets.')
                            ->disabled(fn (?Page $record): bool => (bool) $record?->is_system)
                            ->required(fn (?Page $record): bool => ! $record?->is_system)
                            ->maxLength(80)
                            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
                            ->unique(ignoreRecord: true)
                            ->rule(fn (?Page $record) => Rule::notIn(Page::reservedSlugs((bool) $record?->is_system)))
                            ->validationMessages([
                                'not_in' => 'Ce slug est déjà utilisé par une page du site.',
                                'regex' => 'Slug : lettres minuscules, chiffres et tirets uniquement.',
                            ])
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Set $set, ?string $state) => $set('slug', Str::slug((string) $state))),
                        TextInput::make('kicker')->label('Surtitre')->maxLength(160),
                        Textarea::make('intro')->label('Chapeau')->rows(3)->maxLength(2000),
                        RichEditor::make('body')
                            ->label('Corps de la page')
                            ->maxLength(100000)
                            ->dehydrateStateUsing(fn (?string $state): string => Cms::cleanHtml($state)),
                    ]),
                Section::make('Publication')
                    ->columnSpan(1)
                    ->schema([
                        Toggle::make('published')->label('Publiée')->default(true),
                        Toggle::make('in_nav')->label('Dans le menu'),
                        TextInput::make('nav_order')->label('Ordre dans le menu')->integer()->minValue(0)->maxValue(999)->default(0),
                        MediaPath::make('hero_image', 'Image d’en-tête', 'cms'),
                    ]),
                Section::make('Référencement')
                    ->columnSpanFull()
                    ->columns(2)
                    ->collapsed()
                    ->schema([
                        TextInput::make('seo_title')->label('Titre SEO')->maxLength(160),
                        Textarea::make('seo_description')->label('Description SEO')->rows(2)->maxLength(300),
                    ]),
            ]);
    }
}
