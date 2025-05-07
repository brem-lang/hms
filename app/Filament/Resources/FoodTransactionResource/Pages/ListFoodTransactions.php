<?php

namespace App\Filament\Resources\FoodTransactionResource\Pages;

use App\Filament\Resources\FoodTransactionResource;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListFoodTransactions extends ListRecords
{
    protected static string $resource = FoodTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
            Action::make('generateReport')
                ->label('Generate Report')
                ->action(function () {
                    $transactions = Transaction::whereHas('foodOrder', function ($query) {
                        $query->where('status', 'completed');
                    })->with('foodOrder')->get();

                    $pdf = Pdf::loadView('reports.food-reports', compact('transactions'));

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'food-sales-report-'.now()->format('Y-m-d_H-i-s').'.pdf'
                    );
                })
                ->visible(auth()->user()->isAdmin())
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Generate Report'),
        ];
    }
}
