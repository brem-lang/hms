<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Closure;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('generateReport')
                ->label('Generate Report')
                ->action(function ($data) {
                    $startDate = Carbon::parse($data['start_date'])->startOfDay();
                    $endDate = Carbon::parse($data['end_date'])->endOfDay();

                    $transactions = Transaction::where('type', 'rooms')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->whereHas('booking', function ($query) {
                            $query->where('status', 'completed');
                        })->with('booking')->get();

                    $pdf = Pdf::loadView('reports.reports', compact('transactions'));

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'room-sales-report-'.now()->format('Y-m-d_H-i-s').'.pdf'
                    );
                })
                ->form([
                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('End Date')
                        ->rules([
                            function (callable $get) {
                                return function (string $attribute, $value, Closure $fail) use ($get) {

                                    $date1 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($get('start_date'))));
                                    $date2 = Carbon::createFromFormat('m/d/Y H:i:s', date('m/d/Y H:i:s', strtotime($value)));

                                    $result = $date1->gte($date2);

                                    if ($result) {
                                        $fail('End Date must be ahead from Start Date');
                                    }
                                };
                            },
                        ])
                        ->required(),
                ])
                ->visible(auth()->user()->isAdmin())
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Generate Report'),
        ];
    }
}
