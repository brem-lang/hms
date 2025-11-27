<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoomResource\Pages;
use App\Filament\Resources\RoomResource\RelationManagers\SuiteRoomsRelationManager;
use App\Models\Room;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RoomResource extends Resource
{
    protected static ?string $model = Room::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $modelLabel = 'Suite Types';

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
                                FileUpload::make('image')
                                    ->required()
                                    ->disk('public_uploads_suite')
                                    ->directory('/')
                                    ->image()
                                    ->label('Image'),
                                FileUpload::make('images')
                                    ->hint('OTher images of the suite type')
                                    ->disk('public_uploads_suite')
                                    ->directory('/')
                                    ->image()
                                    ->multiple()
                                    ->label('Images'),
                            ]),
                    ])
                    ->columnSpan(1),
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Name')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                                Select::make('status')
                                    ->label('Status')
                                    ->options([
                                        '1' => 'Active',
                                        '0' => 'Inactive',
                                    ])
                                    ->required()
                                    ->columnSpanFull(),
                                // TextInput::make('available_rooms')
                                //     ->numeric()
                                //     ->label('Available Rooms')
                                //     ->required()
                                //     ->maxLength(255),
                                // TextInput::make('total_rooms')
                                //     ->numeric()
                                //     ->label('Total Rooms')
                                //     ->required()
                                //     ->maxLength(255),

                                Repeater::make('items')
                                    ->maxItems(6)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->reorderableWithDragAndDrop(false)
                                    ->columnSpanFull()
                                    ->schema([
                                        TextInput::make('item')->required(),
                                        TextInput::make('price')->required()->numeric()->prefix('₱'),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),
                    ])
                    ->columnSpan(2),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([0])
            ->columns([
                ImageColumn::make('image')
                    ->square()
                    ->disk('public_uploads_suite'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('suite_rooms_available_count')
                    ->label('Available Rooms')
                    ->searchable(),
                TextColumn::make('total_rooms')->searchable()
                    ->formatStateUsing(function ($record) {
                        return $record->suite_rooms->count();
                    }),
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
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('suite_rooms_available')->latest());
    }

    public static function getRelations(): array
    {
        return [
            SuiteRoomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRooms::route('/'),
            'create' => Pages\CreateRoom::route('/create'),
            'edit' => Pages\EditRoom::route('/{record}/edit'),
        ];
    }
}
