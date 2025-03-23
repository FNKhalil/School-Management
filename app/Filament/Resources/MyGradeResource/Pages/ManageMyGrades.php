<?php

namespace App\Filament\Resources\MyGradeResource\Pages;

use App\Filament\Resources\MyGradeResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageMyGrades extends ManageRecords
{
    protected static string $resource = MyGradeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
