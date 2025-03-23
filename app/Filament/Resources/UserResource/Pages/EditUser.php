<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;




    protected function mutateFormDataBeforeFill(array $data): array
    {

        $data['role'] = $this->record->getRoleNames()->first();


        if ($data['role'] === 'teacher') {
            $data['grades'] = $this->record->grades->pluck('id');
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $role = $this->form->getState()['role'] ?? 'student';


        $this->record->syncRoles([$role]);

      
        if ($role !== 'student') {
            $this->record->grade()->dissociate();
        }
        if ($role !== 'teacher') {
            $this->record->grades()->sync([]);
        }

        $this->record->save();
    }
    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
