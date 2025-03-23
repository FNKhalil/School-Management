<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentCountWidget extends BaseWidget
{



    protected function getStats(): array
    {

        $count = User::role('student')->count();

        return [
            Stat::make('Total Students', number_format($count))
                ->icon('heroicon-o-user-group')
                ->color('success')
                ->description('Enrolled learners')
        ];
    }
    public static function getPermissionPrefixes(): array
    {
        return [

            'widget_StudentCountWidget'
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->canAny([
           
            'widget_StudentCountWidget',

        ]);
    }
}
