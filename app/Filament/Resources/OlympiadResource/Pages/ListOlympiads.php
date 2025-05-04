<?php

namespace App\Filament\Resources\OlympiadResource\Pages;

use App\Filament\Resources\OlympiadResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOlympiads extends ListRecords
{
    protected static string $resource = OlympiadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
