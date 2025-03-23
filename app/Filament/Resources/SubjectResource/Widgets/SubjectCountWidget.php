<?php

namespace App\Filament\Resources\SubjectResource\Widgets;

use App\Models\Subject;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubjectCountWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $count = Subject::count();

        return [
            Stat::make('Available Subjects', number_format($count))
                ->icon('heroicon-o-book-open')
                ->color('danger')
                ->description('Courses offered')
        ];
    }

    public static function getPermissionPrefixes(): array
    {
        return [

            'widget_SubjectCountWidget'
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->canAny([
          
            'widget_SubjectCountWidget'
        ]);
    }
}
