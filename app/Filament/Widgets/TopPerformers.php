<?php

namespace App\Filament\Widgets;

use App\Models\Evaluation;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopPerformers extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Evaluation::query()->with(['employee', 'employee.department', 'manager', 'manager.department'])->orderByDesc('score')->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('Employee'))
                    ->getStateUsing(fn ($record) => $record->employee?->name ?? $record->manager?->name),
                Tables\Columns\TextColumn::make('department')
                    ->label(__('Department'))
                    ->getStateUsing(fn ($record) => $record->employee?->department?->name ?? $record->manager?->department?->name),
                Tables\Columns\TextColumn::make('score')
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ]);
    }
}
