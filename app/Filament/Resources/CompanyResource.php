<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyResource\Pages;
use App\Models\Company;
use App\Models\Manager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Companies';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Company Information')
                    ->description('Basic company details')
                    ->icon('heroicon-o-building-office')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Company Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Acme Corporation'),

                        Forms\Components\TextInput::make('email')
                            ->label('Company Email')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->placeholder('info@company.com'),

                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->nullable()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('address')
                            ->label('Address')
                            ->nullable()
                            ->maxLength(255),
                    ]),

                // GM section only shows on create
                Forms\Components\Section::make('General Manager Account')
                    ->description('These credentials will be used by the General Manager to login')
                    ->icon('heroicon-o-user-circle')
                    ->columns(2)
                    ->hidden(fn ($record) => $record !== null) // hide on edit
                    ->schema([
                        Forms\Components\TextInput::make('gm_name')
                            ->label('GM Full Name')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('John Doe'),

                        Forms\Components\TextInput::make('gm_email')
                            ->label('GM Email (Login)')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique('managers', 'email')
                            ->placeholder('gm@company.com'),

                        Forms\Components\TextInput::make('gm_password')
                            ->label('GM Password')
                            ->password()
                            ->required()
                            ->minLength(6)
                            ->maxLength(255)
                            ->helperText('Minimum 6 characters. Share this with the GM securely.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->copyable(),

                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('managers_count')
                    ->label('Managers')
                    ->counts('managers')
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('employees_count')
                    ->label('Employees')
                    ->counts('employees')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('departments_count')
                    ->label('Departments')
                    ->counts('departments')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCompanies::route('/'),
            'create' => Pages\CreateCompany::route('/create'),
            'view'   => Pages\ViewCompany::route('/{record}'),
            'edit'   => Pages\EditCompany::route('/{record}/edit'),
        ];
    }
}

