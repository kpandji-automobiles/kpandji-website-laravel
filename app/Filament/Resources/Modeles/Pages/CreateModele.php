<?php

namespace App\Filament\Resources\Modeles\Pages;

use App\Filament\Resources\Modeles\ModeleResource;
use App\Models\Modele;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateModele extends CreateRecord
{
    protected static string $resource = ModeleResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['key'] = Str::slug($data['name']).'-'.Str::lower(Str::random(4));
        $data['sort'] = (int) Modele::max('sort') + 1;
        $data['sr_sort'] = (int) Modele::max('sr_sort') + 1;

        return $data;
    }
}
