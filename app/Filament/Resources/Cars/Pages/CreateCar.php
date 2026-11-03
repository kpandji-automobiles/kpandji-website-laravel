<?php

namespace App\Filament\Resources\Cars\Pages;

use App\Filament\Resources\Cars\CarResource;
use App\Models\Car;
use Filament\Resources\Pages\CreateRecord;

class CreateCar extends CreateRecord
{
    protected static string $resource = CarResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['sort_order'] = (Car::max('sort_order') ?? 0) + 1;
        $data['position'] = $data['sort_order'];
        $data['image'] ??= 'images/cars/placeholder.svg';
        $data['hero_image'] = $data['image'];

        return $data;
    }
}
