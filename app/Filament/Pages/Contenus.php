<?php

namespace App\Filament\Pages;

use App\Filament\Support\MediaPath;
use App\Models\Content;
use App\Support\Cms;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Url;
use UnitEnum;

/**
 * Contenus du site : un formulaire par rubrique du registre CMS (config/cms*.php),
 * une seule rubrique chargée à la fois (le registre compte plus de 1 500 champs).
 */
class Contenus extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPencilSquare;

    protected static string|UnitEnum|null $navigationGroup = 'Contenus';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'Contenus du site';

    protected static ?string $slug = 'contenus';

    #[Url]
    public ?string $tab = null;

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        if (! array_key_exists((string) $this->tab, config('cms.tabs'))) {
            $this->tab = array_key_first(config('cms.tabs'));
        }

        $this->form->fill($this->currentValues());
    }

    /** Les clés du registre contiennent des points, que Filament lirait comme des sous-niveaux. */
    public static function fieldName(string $key): string
    {
        return str_replace('.', '__', $key);
    }

    public function form(Schema $schema): Schema
    {
        $components = [];
        foreach ($this->fields() as $key => $definition) {
            $components[] = $this->component(self::fieldName($key), $definition);
        }

        return $schema
            ->statePath('data')
            ->components([
                Section::make(config('cms.tabs')[$this->tab])->columns(2)->schema($components),
            ]);
    }

    public function content(Schema $schema): Schema
    {
        $tabs = [];
        foreach (config('cms.tabs') as $key => $label) {
            $tabs[] = Action::make('tab_'.$key)
                ->label($label)
                ->url(static::getUrl(['tab' => $key]))
                ->color($key === $this->tab ? 'primary' : 'gray')
                ->outlined($key !== $this->tab)
                ->size('sm');
        }

        return $schema->components([
            Actions::make($tabs)->key('cms-tabs'),
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('save')
                ->footer([
                    Actions::make([
                        Action::make('save')->label('Enregistrer')->submit('save')->keyBindings(['mod+s']),
                    ])->sticky(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset')
                ->label('Rétablir les textes d’origine')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Les contenus modifiés de cette rubrique reprennent leur valeur d’origine.')
                ->action(function (): void {
                    Content::whereIn('key', array_keys($this->fields()))->delete();
                    Cache::forget(Content::CACHE);
                    $this->form->fill($this->currentValues());
                    Notification::make()->success()->title('Rubrique rétablie.')->send();
                }),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        foreach ($this->fields() as $key => [$group, $type, $label]) {
            $name = self::fieldName($key);
            if (! array_key_exists($name, $state)) {
                continue;
            }
            $value = $state[$name];

            if (in_array($type, ['image', 'video'], true)) {
                if (blank($value)) {
                    continue; // garde le fichier existant
                }
            } elseif ($type === 'list') {
                $value = json_encode($this->listForStorage($key, (array) $value), JSON_UNESCAPED_UNICODE);
            } elseif ($type === 'richtext') {
                $value = Cms::cleanHtml($value);
            } else {
                $value = trim(mb_substr((string) $value, 0, 5000));
            }

            Content::firstOrNew(['key' => $key])->fill(['group' => $group, 'type' => $type, 'label' => $label, 'value' => $value])->save();
        }

        Cache::forget(Content::CACHE);
        Notification::make()->success()->title('Contenu enregistré. Le site est à jour.')->send();
    }

    /** @return array<string, array<int, mixed>> */
    private function fields(): array
    {
        return Cms::fields($this->tab);
    }

    /** @return array<string, mixed> */
    private function currentValues(): array
    {
        $values = [];
        foreach ($this->fields() as $key => [, $type]) {
            $value = content($key);
            if ($type === 'list') {
                $value = array_map(fn ($row): array => $this->listRowForForm($key, (array) $row), is_array($value) ? $value : []);
            }
            $values[self::fieldName($key)] = $value;
        }

        return $values;
    }

    /** @param  array<int, mixed>  $definition */
    private function component(string $name, array $definition): Component
    {
        [, $type, $label] = $definition;

        return match ($type) {
            'textarea' => Textarea::make($name)->label($label)->rows(3)->maxLength(5000)->columnSpanFull(),
            'richtext' => RichEditor::make($name)->label($label)->columnSpanFull(),
            'url' => TextInput::make($name)->label($label)->maxLength(5000)->rule(fn (): Closure => $this->urlRule()),
            'image', 'video' => MediaPath::make($name, $label, 'cms', $type),
            'list' => $this->listComponent($name, $definition),
            default => TextInput::make($name)->label($label)->maxLength(5000),
        };
    }

    /** @param  array<int, mixed>  $definition */
    private function listComponent(string $name, array $definition): Repeater
    {
        $columns = $definition[4] ?? ['label' => 'Libellé'];
        $fields = [];
        foreach ($columns as $column => $columnLabel) {
            $fields[] = match ($column) {
                'visible' => Toggle::make($column)->label('Visible')->default(true),
                'url' => TextInput::make($column)->label($columnLabel)->maxLength(500)->rule(fn (): Closure => $this->urlRule()),
                default => TextInput::make($column)->label($columnLabel)->maxLength(500),
            };
        }

        return Repeater::make($name)
            ->label($definition[2])
            ->schema($fields)
            ->columns(min(3, count($fields)))
            ->defaultItems(0)
            ->reorderable()
            ->collapsible()
            ->itemLabel(fn (array $state): ?string => $state['label'] ?? $state['name'] ?? $state['title'] ?? null)
            ->columnSpanFull();
    }

    private function urlRule(): Closure
    {
        return function (string $attribute, mixed $value, Closure $fail): void {
            if (! Cms::validUrl((string) $value)) {
                $fail('Lien invalide (utilisez /page, #ancre ou https://…).');
            }
        };
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function listRowForForm(string $key, array $row): array
    {
        if (array_key_exists('visible', $this->listColumns($key))) {
            $row['visible'] = ($row['visible'] ?? '1') !== '0';
        }

        return $row;
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return list<array<string, string>>
     */
    private function listForStorage(string $key, array $rows): array
    {
        $clean = [];
        foreach ($rows as $row) {
            $item = [];
            foreach (array_keys($this->listColumns($key)) as $column) {
                $item[$column] = $column === 'visible'
                    ? (($row['visible'] ?? true) ? '1' : '0')
                    : trim(mb_substr((string) ($row[$column] ?? ''), 0, 500));
            }
            if (implode('', array_diff_key($item, ['visible' => true])) !== '') {
                $clean[] = $item;
            }
        }

        return $clean;
    }

    /** @return array<string, string> */
    private function listColumns(string $key): array
    {
        return config('cms.fields')[$key][4] ?? ['label' => 'Libellé'];
    }
}
