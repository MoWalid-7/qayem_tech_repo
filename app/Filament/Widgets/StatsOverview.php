<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Manager;
use App\Models\Evaluation;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $activeSubscriptions = \App\Models\Subscription::where('status', 'active')->count();

        return [
            Stat::make('Registered Companies', Company::count())
                ->description('Total companies using Qayem')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('primary')
                ->chart([7, 2, 10, 3, 15, 4, 17]),

            Stat::make('Active Subscriptions', $activeSubscriptions)
                ->description('Currently active plans')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([1, 4, 3, 5, 8, 12, 15]),

            Stat::make('Total Employees', Employee::count())
                ->description('Workforce managed across platform')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning')
                ->chart([10, 25, 40, 60, 80, 100, 150]),

            Stat::make('AI Evaluations', Evaluation::count())
                ->description('Performance reports generated')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('info')
                ->chart([2, 5, 8, 10, 20, 35, 50]),
        ];
    }
}
