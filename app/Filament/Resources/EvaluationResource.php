<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationResource\Pages;
use App\Filament\Resources\EvaluationResource\RelationManagers;
use App\Models\Evaluation;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Evaluatee')
                    ->schema([
                        Forms\Components\Select::make('employee_id')
                            ->relationship('employee', 'name')
                            ->placeholder('Select employee (if applicable)'),
                        Forms\Components\Select::make('manager_id')
                            ->relationship('manager', 'name')
                            ->placeholder('Select manager (if applicable)'),
                    ])->columns(2),

                Forms\Components\Section::make('AI Report')
                    ->schema([
                        Forms\Components\MarkdownEditor::make('evaluation_text')
                            ->label('Detailed Report')
                            ->columnSpanFull(),
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('score')
                                    ->numeric()
                                    ->minValue(0)
                                    ->maxValue(10),
                                Forms\Components\Textarea::make('strengths'),
                                Forms\Components\Textarea::make('weaknesses'),
                                Forms\Components\Textarea::make('recommendations'),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('evaluatee')
                    ->label('Professional')
                    ->state(fn(Evaluation $record): string => $record->employee?->name ?? $record->manager?->name ?? 'Unknown')
                    ->description(fn(Evaluation $record): string => $record->employee ? 'Employee' : ($record->manager ? 'Manager' : '-'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->whereHas('employee', fn($q) => $q->where('name', 'like', "%{$search}%"))
                            ->orWhereHas('manager', fn($q) => $q->where('name', 'like', "%{$search}%"));
                    }),
                Tables\Columns\TextColumn::make('score')
                    ->badge()
                    ->color(fn(string $state): string => match (true) {
                        $state >= 8 => 'success',
                        $state >= 5 => 'warning',
                        default => 'danger',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'create' => Pages\CreateEvaluation::route('/create'),
            'view' => Pages\ViewEvaluation::route('/{record}'),
            'edit' => Pages\EditEvaluation::route('/{record}/edit'),
        ];
    }
}
