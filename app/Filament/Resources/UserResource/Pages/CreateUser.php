<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;


    protected function afterCreate(): void
    {
        $role = $this->form->getState()['role'] ?? 'student';
        $this->record->syncRoles([$role]);


        if ($role !== 'student') {
            $this->record->grade()->dissociate();
        }
        if ($role !== 'teacher') {
            $this->record->grades()->sync([]);
        }
    }
}
