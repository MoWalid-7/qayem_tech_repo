<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EmployeeResource\Pages;
use App\Filament\Resources\EmployeeResource\RelationManagers;
use App\Models\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => filled($state) ? \Illuminate\Support\Facades\Hash::make($state) : null)
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('hire_date'),
                Forms\Components\TextInput::make('salary')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('attendance_rate')
                    ->numeric()
                    ->suffix('%')
                    ->minValue(0)
                    ->maxValue(100),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('tasks_requested')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Forms\Components\TextInput::make('tasks_completed')
                            ->required()
                            ->numeric()
                            ->default(0),
                    ]),
                Forms\Components\Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required(),
                Forms\Components\Select::make('department_id')
                    ->relationship('department', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('job_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attendance_rate')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasks_completed')
                    ->numeric()
                    ->label('Done')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasks_requested')
                    ->numeric()
                    ->label('Req')
                    ->sortable(),
                Tables\Columns\TextColumn::make('department.name')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\Action::make('evaluate')
                    ->label('AI Evaluation')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate AI Evaluation')
                    ->modalDescription('Are you sure you want to evaluate this employee using Google Gemini AI? This will create a new structured evaluation record.')
                    ->action(function (Employee $record) {
                        $geminiService = new \App\Services\GeminiService();
                        $result = $geminiService->generateEvaluation($record);

                        if ($result && $result['score'] !== null) {
                            \App\Models\Evaluation::create([
                                'employee_id' => $record->id,
                                'evaluation_text' => $result['text'],
                                'score' => $result['score'],
                                'strengths' => $result['strengths'] ?? null,
                                'weaknesses' => $result['weaknesses'] ?? null,
                                'recommendations' => $result['recommendations'] ?? null,
                            ]);

                            \Filament\Notifications\Notification::make()
                                ->title('Evaluation generated successfully!')
                                ->success()
                                ->send();
                        } else {
                            \Filament\Notifications\Notification::make()
                                ->title('Failed to generate evaluation from AI.')
                                ->danger()
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListEmployees::route('/'),
            'create' => Pages\CreateEmployee::route('/create'),
            'view' => Pages\ViewEmployee::route('/{record}'),
            'edit' => Pages\EditEmployee::route('/{record}/edit'),
        ];
    }
}
