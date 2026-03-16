<?php

namespace App\Filament\Resources\HrUserResource\Pages;

use App\Filament\Resources\HrUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListHrUsers extends ListRecords
{
    protected static string $resource = HrUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
