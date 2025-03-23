<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\Subject;
use App\Models\User;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TeacherCountWidget extends BaseWidget
{
    use HasWidgetShield;

    protected int | string | array $columnSpan = 'full';

    protected $listeners = ['updateStats' => '$refresh'];

    protected function getStats(): array
    {
        $countTeacher = User::role('teacher')->count();
        $countStudent = User::role('student')->count();
        $countSubject = Subject::count();

        return [
            Stat::make('Total Teachers', number_format($countTeacher))
                ->icon('heroicon-o-academic-cap')
                ->color('primary')
                ->description('Teaching staff members')
                ->extraAttributes(['class' => 'cursor-pointer'])
                ->url(route('filament.admin.resources.users.index')),


        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'widget_TeacherCountWidget',

        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->canAny([
            'widget_TeacherCountWidget',

        ]);
    }
}
