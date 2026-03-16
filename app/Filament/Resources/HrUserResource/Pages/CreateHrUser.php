<?php

namespace App\Filament\Resources\HrUserResource\Pages;

use App\Filament\Resources\HrUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateHrUser extends CreateRecord

{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static string $resource = HrUserResource::class;
}
