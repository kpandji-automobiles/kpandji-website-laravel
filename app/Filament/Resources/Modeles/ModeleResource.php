<?php

namespace App\Filament\Resources\Modeles;

use App\Filament\Resources\Modeles\Pages\CreateModele;
use App\Filament\Resources\Modeles\Pages\EditModele;
use App\Filament\Resources\Modeles\Pages\ListModeles;
use App\Filament\Resources\Modeles\Schemas\ModeleForm;
use App\Filament\Resources\Modeles\Tables\ModelesTable;
use App\Models\Modele;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ModeleResource extends Resource
{
    protected static ?string $model = Modele::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'modèle mis en avant';

    protected static ?string $pluralModelLabel = 'modèles (accueil et showroom)';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ModeleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ModelesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListModeles::route('/'),
            'create' => CreateModele::route('/create'),
            'edit' => EditModele::route('/{record}/edit'),
        ];
    }
}
