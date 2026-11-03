<?php

namespace App\Filament\Pages;

use App\Filament\Support\MediaPath;
use App\Models\Setting;
use App\Support\AdPopup;
use App\Support\Cms;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use UnitEnum;

/** Fenêtre publicitaire affichée sur le site public : image, lien, période et fréquence (réglages popup.*). */
class FenetrePublicitaire extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Catalogue';

    protected static ?int $navigationSort = 4;

    protected static ?string $slug = 'fenetre-publicitaire';

    protected static ?string $title = 'Fenêtre publicitaire';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $popup = AdPopup::settings();
        $popup['active'] = $popup['active'] === '1';

        $this->form->fill($popup);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Toggle::make('active')->label('Afficher la fenêtre sur le site')->columnSpanFull(),
                        MediaPath::make('image', 'Image (format portrait ou carré conseillé)', 'popup')
                            ->columnSpanFull(),
                        TextInput::make('title')->label('Titre (facultatif)')->maxLength(120),
                        TextInput::make('cta')->label('Texte du bouton')->maxLength(40)->required(),
                        Textarea::make('text')->label('Texte sous l’image (facultatif)')->rows(2)->maxLength(300)->columnSpanFull(),
                        TextInput::make('link')
                            ->label('Lien du bouton et de l’image (facultatif)')
                            ->placeholder('/modeles/lathaye ou https://…')
                            ->maxLength(255)
                            ->rules([fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                if (! Cms::validUrl((string) $value)) {
                                    $fail('Lien invalide : commencez par « / », « https:// », « mailto: » ou « tel: ».');
                                }
                            }])
                            ->columnSpanFull(),
                        DatePicker::make('starts_at')->label('Début (facultatif)')->native(false)->displayFormat('d/m/Y'),
                        DatePicker::make('ends_at')->label('Fin (facultatif)')->native(false)->displayFormat('d/m/Y')
                            ->afterOrEqual(fn (Get $get) => $get('starts_at') ?: null),
                        Radio::make('scope')->label('Pages')->options(AdPopup::SCOPES)->in(array_keys(AdPopup::SCOPES))->required(),
                        Select::make('frequency')->label('Fréquence pour un même visiteur')->options(AdPopup::FREQUENCIES)->in(array_keys(AdPopup::FREQUENCIES))->required(),
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
                        Action::make('preview')->label('Voir le site')->url(route('home'), shouldOpenInNewTab: true)->color('gray'),
                    ]),
                ]),
        ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        if (($data['active'] ?? false) && blank($data['image'] ?? null)) {
            Notification::make()->danger()->title('Ajoutez une image avant d’activer la fenêtre.')->send();

            return;
        }

        foreach (AdPopup::KEYS as $key) {
            $value = $data[$key] ?? '';
            Setting::put('popup.'.$key, $key === 'active' ? ($value ? '1' : '0') : (string) $value);
        }

        Notification::make()->success()->title($data['active'] ? 'Fenêtre publicitaire enregistrée et affichée.' : 'Fenêtre publicitaire enregistrée (désactivée).')->send();
    }
}
