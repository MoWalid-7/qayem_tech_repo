<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManagerResource\Pages;
use App\Filament\Resources\ManagerResource\RelationManagers;
use App\Models\Manager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManagerResource extends Resource
{
    protected static ?string $model = Manager::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

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
                    ->maxLength(255),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->maxLength(255),
                Forms\Components\DatePicker::make('hire_date'),
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
                Forms\Components\Select::make('role')
                    ->options([
                        'general_manager' => 'General Manager',
                        'department_manager' => 'Department Manager',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hire_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasks_completed')
                    ->numeric()
                    ->label('Done')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasks_requested')
                    ->numeric()
                    ->label('Req')
                    ->sortable(),
                Tables\Columns\TextColumn::make('company.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'general_manager' => 'General Manager',
                        'department_manager' => 'Department Manager',
                    })
                    ->colors([
                        'primary' => 'general_manager',
                        'success' => 'department_manager',
                    ]),
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
                    ->modalDescription('Are you sure you want to evaluate this manager using Google Gemini AI? This will create a new structured evaluation record.')
                    ->action(function (Manager $record) {
                        $geminiService = new \App\Services\GeminiService();
                        $result = $geminiService->generateEvaluation($record);

                        if ($result && $result['score'] !== null) {
                            \App\Models\Evaluation::create([
                                'manager_id' => $record->id,
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
            'index' => Pages\ListManagers::route('/'),
            'create' => Pages\CreateManager::route('/create'),
            'view' => Pages\ViewManager::route('/{record}'),
            'edit' => Pages\EditManager::route('/{record}/edit'),
        ];
    }
}
