<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserManagementResource\Pages;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;

class UserManagementResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static ?string $navigationGroup = 'User Management';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->label('Email')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->minLength(8)
                            ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                        Select::make('role')
                            ->label('Role')
                            ->options([
                                'admin' => 'Admin',
                                'front-desk' => 'Front Desk',
                                'customer' => 'Customer',
                            ])
                            ->required(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('role')
                    ->badge()->color(fn (string $state): string => match ($state) {
                        'admin' => 'success',
                        'customer' => 'gray',
                        'front-desk' => 'warning',
                        'staff' => 'danger',
                    })->formatStateUsing(fn (string $state): string => __(ucfirst($state)))
                    ->searchable(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                RestoreAction::make(),
                DeleteAction::make(),
                Impersonate::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->latest());
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
            'index' => Pages\ListUserManagement::route('/'),
            'create' => Pages\CreateUserManagement::route('/create'),
            'edit' => Pages\EditUserManagement::route('/{record}/edit'),
        ];
    }
}
