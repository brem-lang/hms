<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FoodResource\Pages;
use App\Models\Food;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class FoodResource extends Resource
{
    protected static ?string $model = Food::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static ?string $modelLabel = 'Food Types';

    protected static ?string $navigationGroup = 'Accommodation Control Panel';

    public static function canViewAny(): bool
    {
        return auth()->user()->isAdmin();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->columnSpanFull()
                                    ->maxLength(255),
                                TextInput::make('price')
                                    ->numeric()
                                    ->prefix('₱')
                                    ->label('Price')
                                    ->required()
                                    ->maxLength(255),
                                ToggleButtons::make('is_available')
                                    ->label('Is Available')
                                    ->boolean()
                                    ->inline(),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->required()
                                    ->columnSpanFull(),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(2),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                FileUpload::make('image')
                                    ->required()
                                    ->disk('public_uploads_food')
                                    ->directory('/')
                                    ->image()
                                    ->label('Image')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50])
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->disk('public_uploads_food'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('price')->searchable(),
                TextColumn::make('is_available')
                    ->badge()->color(fn (string $state): string => match ($state) {
                        '1' => 'success',
                        '0' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => $state === '1' ? 'Available' : 'Unavailable')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListFood::route('/'),
            'create' => Pages\CreateFood::route('/create'),
            'edit' => Pages\EditFood::route('/{record}/edit'),
        ];
    }
}
