<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Bandeau promotion du site : réglages « promo.* » qui écrasent config/promo.php. */
class Promotion extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Promotion';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'active' => (bool) config('promo.active'),
            'title' => config('promo.title'),
            'subtitle' => config('promo.subtitle'),
            'reduction_percent' => (float) config('promo.reduction_percent'),
            'ends_at' => config('promo.ends_at'),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Toggle::make('active')->label('Bandeau promotion affiché')->columnSpanFull(),
                        TextInput::make('title')->label('Titre')->required()->maxLength(150),
                        DatePicker::make('ends_at')->label('Fin de l’offre')->native(false)->displayFormat('d/m/Y'),
                        Textarea::make('subtitle')->label('Sous-titre')->rows(2)->maxLength(300)->columnSpanFull(),
                        TextInput::make('reduction_percent')
                            ->label('Réduction globale (%)')
                            ->helperText('Appliquée aux modèles sans prix promo. 0 = aucune.')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(90)
                            ->suffix('%'),
                    ]),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Enregistrer')->submit('save')->keyBindings(['mod+s']),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::put('promo.title', $data['title']);
        Setting::put('promo.subtitle', $data['subtitle'] ?? '');
        Setting::put('promo.reduction_percent', (string) ($data['reduction_percent'] ?? 0));
        Setting::put('promo.ends_at', $data['ends_at'] ?? '');
        Setting::put('promo.active', $data['active'] ? '1' : '0');

        Notification::make()->success()->title('Promotion enregistrée.')->send();
    }
}
