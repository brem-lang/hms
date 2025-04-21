<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
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
                ->action(function () {
                    $transactions = Transaction::whereHas('booking', function ($query) {
                        $query->where('status', 'completed');
                    })->with('booking')->get();

                    $pdf = Pdf::loadView('reports.reports', compact('transactions'));

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'sales-report-'.now()->format('Y-m-d_H-i-s').'.pdf'
                    );
                })
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Generate Report'),
        ];
    }
}
