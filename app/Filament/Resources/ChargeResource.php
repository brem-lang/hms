<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChargeResource\Pages;
use App\Models\Charge;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ChargeResource extends Resource
{
    protected static ?string $model = Charge::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationGroup = 'Accommodation Control Panel';

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
                            ->label('Charge Name')
                            ->required()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Description')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('amount')
                            ->label('Amount')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->step(0.01)
                            ->prefix('PHP'),
                        Toggle::make('status')
                            ->label('Status')
                            ->required()
                            ->default(true),
                        Select::make('type')
                            ->label('Type')
                            ->dehydrated(false)
                            ->live()
                            ->options([
                                'food' => 'Food',
                                'other' => 'Other',
                            ])
                            ->required(),
                        FileUpload::make('image')
                            ->disk('public_uploads_food')
                            ->required(function ($get) {
                                return $get('type') === 'food';
                            })
                            ->directory('/')
                            ->image()
                            ->label('Image')
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Charge Name')->searchable()->sortable(),
                TextColumn::make('description')->label('Description')->searchable()->sortable(),
                TextColumn::make('amount')->label('Amount')->searchable()->sortable(),
                TextColumn::make('status')->label('Status')->badge()->color(fn ($state) => $state ? 'success' : 'danger')->formatStateUsing(fn ($state) => $state ? 'Active' : 'Inactive'),
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
            'index' => Pages\ListCharges::route('/'),
            'create' => Pages\CreateCharge::route('/create'),
            'edit' => Pages\EditCharge::route('/{record}/edit'),
        ];
    }
}
