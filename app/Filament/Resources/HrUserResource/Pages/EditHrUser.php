<?php

namespace App\Filament\Resources\HrUserResource\Pages;

use App\Filament\Resources\HrUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHrUser extends EditRecord

{
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static string $resource = HrUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
