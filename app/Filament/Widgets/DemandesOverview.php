<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Cars\CarResource;
use App\Filament\Resources\JobApplications\JobApplicationResource;
use App\Filament\Resources\SavRequests\SavRequestResource;
use App\Filament\Resources\TestDriveRequests\TestDriveRequestResource;
use App\Models\Car;
use App\Models\JobApplication;
use App\Models\PrivilegeRequest;
use App\Models\SavRequest;
use App\Models\SiraRequest;
use App\Models\TestDriveRequest;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

/** Chiffres clés du tableau de bord : ce qui attend une action. */
class DemandesOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $todo = fn (string $model): int => $model::query()->whereNull('handled_at')->count();
        $essais = TestDriveRequest::query()->whereNull('handled_at');

        return [
            Stat::make('Essais et contacts à traiter', $todo(TestDriveRequest::class))
                ->description((clone $essais)->where('type', 'essai')->count().' demandes d’essai')
                ->color('warning')
                ->url(TestDriveRequestResource::getUrl()),
            Stat::make('S.A.V. à traiter', $todo(SavRequest::class))
                ->url(SavRequestResource::getUrl()),
            Stat::make('Candidatures nouvelles', $todo(JobApplication::class))
                ->description(($todo(SiraRequest::class) + $todo(PrivilegeRequest::class)).' inscriptions SIRA et demandes Privilège')
                ->url(JobApplicationResource::getUrl()),
            Stat::make('Modèles en ligne', Car::query()->where('is_active', true)->count().' / '.Car::query()->count())
                ->url(CarResource::getUrl()),
        ];
    }
}
