<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use Carbon\Carbon;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generateReport')
                ->label('Generate Report')
                ->action(function (array $data) {
                    $url = route('reports.stream', [
                        'type' => $data['type'],
                        'start_date' => $data['start_date'] ?? null,
                        'end_date' => $data['end_date'] ?? null,
                    ]);

                    return $this->js("window.open('{$url}', '_blank')");
                })
                ->form([
                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'sales' => 'Sales',
                            'rooms' => 'Room Trends',
                        ])
                        ->live()
                        ->required(),

                    DatePicker::make('start_date')
                        ->hidden(fn ($get) => $get('type') === 'reports')
                        ->required(),

                    DatePicker::make('end_date')
                        ->hidden(fn ($get) => $get('type') === 'reports')
                        ->required()
                        ->rules([
                            fn (callable $get) => function (string $attribute, $value, Closure $fail) use ($get) {
                                if (Carbon::parse($value)->lte(Carbon::parse($get('start_date')))) {
                                    $fail('End Date must be ahead of Start Date');
                                }
                            },
                        ]),
                ])
                ->visible(auth()->user()->isAdmin())
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Generate Report'),

        ];
    }
}
