<?php

namespace App\Filament\Resources\CompanyResource\Pages;

use App\Filament\Resources\CompanyResource;
use App\Models\Manager;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateCompany extends CreateRecord
{
    protected static string $resource = CompanyResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * After the Company is saved, create the General Manager account.
     */
    protected function afterCreate(): void
    {
        $data = $this->form->getState();

        Manager::create([
            'name'       => $data['gm_name'],
            'email'      => $data['gm_email'],
            'password'   => Hash::make($data['gm_password']),
            'company_id' => $this->record->id,
            'role'       => 'general_manager',
        ]);

        \Filament\Notifications\Notification::make()
            ->title('Company Created Successfully')
            ->body("GM account for {$data['gm_name']} created. Email: {$data['gm_email']}")
            ->success()
            ->send();
    }

    /**
     * Exclude the virtual GM fields from being saved to the companies table.
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['gm_name'], $data['gm_email'], $data['gm_password']);
        return $data;
    }
}
