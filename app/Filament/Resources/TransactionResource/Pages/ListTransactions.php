<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Booking;
use App\Models\Charge;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Closure;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
                    $type = $data['type'];
                    if ($type != 'reports') {
                        $startDate = Carbon::parse($data['start_date'])->startOfDay();
                        $endDate = Carbon::parse($data['end_date'])->endOfDay();
                    }

                    if ($type == 'sales') {
                        $transactions = Transaction::where('type', 'rooms')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->whereHas('booking', function ($query) {
                                $query->where('status', 'done')
                                    ->where('type', '!=', 'bulk_head_online');
                            })
                            ->with('booking.room', 'booking.suiteRoom')
                            ->get();

                        $totalRevenue = $transactions->sum(function ($transaction) {
                            return $transaction->booking->amount_to_pay ?? 0;
                        });

                        $salesByRoomType = $transactions->groupBy(function ($transaction) {
                            return $transaction->booking->room->name ?? 'Unknown Room Type';
                        })->map(function ($groupedTransactions) {
                            return $groupedTransactions->sum(function ($transaction) {
                                return $transaction->booking->amount_to_pay ?? 0;
                            });
                        });

                        $chartConfig = [
                            'type' => 'bar',
                            'data' => [
                                'labels' => $salesByRoomType->keys()->toArray(),
                                'datasets' => [[
                                    'label' => 'Revenue (₱)',
                                    'data' => $salesByRoomType->values()->toArray(),
                                    'backgroundColor' => ['#2563eb', '#10b981', '#f59e0b', '#ef4444', '#6366f1'],
                                ]],
                            ],
                            'options' => [
                                'title' => ['display' => true, 'text' => 'Revenue by Room Type'],
                                'scales' => [
                                    'yAxes' => [['ticks' => ['beginAtZero' => true]]],
                                ],
                            ],
                        ];

                        $chartJson = urlencode(json_encode($chartConfig));
                        $quickChartUrl = 'https://quickchart.io/chart?c='.$chartJson.'&width=700&height=400';
                        $imageContents = file_get_contents($quickChartUrl);
                        $chartFileName = 'charts/sales-chart-'.time().'.png';
                        Storage::disk('public_charts')->put($chartFileName, $imageContents);

                        $data = [
                            'type' => 'Sales Reports',
                            'authorized_name' => auth()->user()->name,
                            'report_title' => 'Millenium Sales Report',
                            'start_date' => $startDate->toDateString(),
                            'end_date' => $endDate->toDateString(),
                            'transactions' => $transactions,
                            'total_revenue' => $totalRevenue,
                            'total_transactions' => $transactions->count(),
                            'sales_by_room_type' => $salesByRoomType,
                            'chart_image_url' => public_path('public_charts/'.$chartFileName),
                        ];

                        $pdf = Pdf::loadView('reports.reports', compact('data'));
                    }

                    if ($type == 'rooms') {
                        // $transactions = Transaction::where('type', 'rooms')
                        //     ->whereBetween('created_at', [$startDate, $endDate])
                        //     ->whereHas('booking', function ($query) {
                        //         $query->where('status', 'done')
                        //             ->where('type', '!=', 'bulk_head_online');
                        //     })
                        //     ->with('booking.room')
                        //     ->get();

                        // // Group by room name and compute stats
                        // $roomTrends = $transactions->groupBy(fn ($tx) => $tx->booking->room->name)
                        //     ->map(function ($group) {
                        //         return [
                        //             'total_bookings' => $group->count(),
                        //             'total_sales' => $group->sum(fn ($tx) => $tx->booking->amount_to_pay),
                        //         ];
                        //     });

                        // $data = [
                        //     'authorized_name' => auth()->user()->name,
                        //     'type' => 'Room Trends Report',
                        //     'start_date' => $startDate->toDateString(),
                        //     'end_date' => $endDate->toDateString(),
                        //     'trends' => $roomTrends,
                        // ];

                        // $pdf = Pdf::loadView('reports.reports', compact('data'));

                        // 2. Date Setup
                        $startDate = Carbon::parse($startDate)->startOfDay();
                        $endDate = Carbon::parse($endDate)->endOfDay();

                        // Define the format for grouping (e.g., 'Y-m-d' for daily, 'Y-m' for monthly)
                        $groupFormat = 'Y-m-d';

                        // 2. Fetch Transactions
                        $transactions = Transaction::where('type', 'rooms')
                            ->whereBetween('created_at', [$startDate, $endDate])
                            ->whereHas(
                                'booking',
                                fn ($query) => $query->where('status', 'done')
                                    ->where('type', '!=', 'bulk_head_online')
                            )
                            ->with('booking') // Only need booking amount
                            ->get();

                        // 3. Calculate Time Trends and Grand Total
                        $timeTrends = $transactions->groupBy(
                            fn ($tx) => Carbon::parse($tx->created_at)->format($groupFormat)
                        )
                            ->map(function ($dateGroup) {
                                $totalSales = $dateGroup->sum(fn ($tx) => $tx->booking->amount_to_pay ?? 0);

                                return [
                                    'total_sales' => $totalSales,
                                    'transaction_count' => $dateGroup->count(),
                                ];
                            });

                        $grandTotalSales = $timeTrends->sum('total_sales');
                        $grandTotalBookings = $timeTrends->sum('transaction_count');

                        // 4. Generate Chart, Download Image, and Get Local Path (The Fix)
                        $chartConfig = [
                            'type' => 'line', // Line chart for time-series data
                            'data' => [
                                'labels' => $timeTrends->keys()->toArray(), // Dates/Months
                                'datasets' => [[
                                    'label' => 'Daily Sales (₱)',
                                    'data' => $timeTrends->pluck('total_sales')->toArray(), // Sales Amounts
                                    'backgroundColor' => 'rgba(37, 99, 235, 0.5)',
                                    'borderColor' => '#2563eb',
                                    'fill' => true,
                                ]],
                            ],
                            'options' => [
                                'title' => ['display' => true, 'text' => 'Sales Performance Over Time'],
                                'scales' => [
                                    'yAxes' => [['ticks' => ['beginAtZero' => true]]],
                                ],
                            ],
                        ];

                        $chartJson = urlencode(json_encode($chartConfig));
                        $quickChartUrl = 'https://quickchart.io/chart?c='.$chartJson.'&width=800&height=400';

                        // Download and save the image
                        $imageContents = file_get_contents($quickChartUrl);
                        $chartFileName = 'charts/time-trends-chart-'.time().'.png';
                        Storage::disk('public_charts')->put($chartFileName, $imageContents);

                        $data['chart_image_path'] = public_path('public_charts/'.$chartFileName);

                        // 5. Assemble Final Data Array
                        $data = [
                            'authorized_name' => Auth::user()->name,
                            'report_title' => 'Trends Report',
                            'start_date' => $startDate->toDateString(),
                            'end_date' => $endDate->toDateString(),
                            'trends' => $timeTrends,
                            'grand_total_sales' => $grandTotalSales,
                            'grand_total_bookings' => $grandTotalBookings,
                            'chart_image_path' => $data['chart_image_path'],
                            'group_by' => ($groupFormat === 'Y-m-d' ? 'Day' : 'Month'),
                            'type' => 'Room Trends Report',
                        ];

                        // 6. Generate PDF
                        $pdf = Pdf::loadView('reports.reports', compact('data'));
                    }

                    if ($type == 'reports') {
                        $data = [
                            'authorized_name' => auth()->user()->name,
                            'type' => 'Daily Reports',
                            // 'start_date' => $startDate->toDateString(),
                            // 'end_date' => $endDate->toDateString(),
                            'trasanctions' => Transaction::whereDate('created_at', now())
                                ->whereHas('booking', function ($query) {
                                    $query->where('status', 'done');
                                })->with('booking.suiteRoom')->get(),
                            'checkout' => Booking::whereDate('created_at', now())->with('suiteRoom')->get(),
                        ];

                        $bookings = Booking::whereDate('created_at', now())
                            ->with('suiteRoom')
                            ->get();

                        $allChargeIds = $bookings->pluck('additional_charges') // Get all charge arrays
                            ->flatten(1)                 // Merge into one big collection of charges
                            ->pluck('name')                  // Get just the 'id' from the 'name' key
                            ->unique()                   // Get only unique IDs
                            ->filter();                  // Remove any nulls/empty values
                        $chargeLookup = Charge::whereIn('id', $allChargeIds)
                            ->get()
                            ->keyBy('id');
                        $finalList = $bookings->flatMap(function ($booking) use ($chargeLookup) {

                            $charges = $booking->additional_charges;
                            if (! is_array($charges)) {
                                return [];
                            }

                            foreach ($charges as &$charge) {
                                if (is_array($charge)) {

                                    $chargeId = $charge['name'] ?? null;

                                    $chargeModel = $chargeLookup->get($chargeId);

                                    $charge['charge_name'] = $chargeModel ? $chargeModel->name : 'Unknown Charge';
                                    $charge['room_name'] = $booking->suiteRoom ? $booking->suiteRoom->name : 'Unknown Room';
                                }
                            }

                            return $charges;
                        })
                            ->groupBy('charge_name')
                            ->map(function ($groupOfCharges) {

                                return $groupOfCharges->map(function ($charge) {
                                    return [
                                        'room_name' => $charge['room_name'],
                                        'amount' => $charge['amount'],
                                    ];
                                });
                            });

                        $chargetotalAmount = $finalList
                            ->flatten(1)    // Merges all groups into one flat collection
                            ->sum('amount'); // Sums the 'amount' from every item

                        $pdf = Pdf::loadView('reports.reports', compact('data', 'finalList', 'chargetotalAmount'));
                    }

                    return response()->streamDownload(
                        fn () => print ($pdf->output()),
                        'room-sales-report-'.now()->format('Y-m-d_H-i-s').'.pdf'

                    );
                })
                ->form([
                    Select::make('type')
                        ->label('Type')
                        ->options([
                            'sales' => 'Sales',
                            'rooms' => 'Room Trends',
                            // 'reports' => 'Daily Report',
                        ])
                        ->live()
                        ->required(),

                    DatePicker::make('start_date')
                        ->label('Start Date')
                        ->hidden(function ($get) {
                            if ($get('type') == 'reports') {
                                return true;
                            }
                        })
                        ->required(),
                    DatePicker::make('end_date')
                        ->label('End Date')->hidden(function ($get) {
                            if ($get('type') == 'reports') {
                                return true;
                            }
                        })
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
