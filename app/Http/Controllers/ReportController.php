<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Charge;
use App\Models\Room;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    public function streamReport(Request $request)
    {
        $type = $request->query('type');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if ($type != 'reports' && (!$startDate || !$endDate)) {
            abort(400, 'Start date and end date are required');
        }

        if ($type != 'reports') {
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
        }

        $pdf = null;
        $filename = 'room-sales-report-'.now()->format('Y-m-d_H-i-s').'.pdf';

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
            $startDate = Carbon::parse($startDate)->startOfDay();
            $endDate = Carbon::parse($endDate)->endOfDay();
            $groupFormat = 'Y-m-d';

            $transactions = Transaction::where('type', 'rooms')
                ->whereBetween('created_at', [$startDate, $endDate])
                ->whereHas(
                    'booking',
                    fn ($query) => $query->where('status', 'done')
                        ->where('type', '!=', 'bulk_head_online')
                )
                ->with('booking')
                ->get();

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

            $chartConfig = [
                'type' => 'line',
                'data' => [
                    'labels' => $timeTrends->keys()->toArray(),
                    'datasets' => [[
                        'label' => 'Daily Sales (₱)',
                        'data' => $timeTrends->pluck('total_sales')->toArray(),
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
            $imageContents = file_get_contents($quickChartUrl);
            $chartFileName = 'charts/time-trends-chart-'.time().'.png';
            Storage::disk('public_charts')->put($chartFileName, $imageContents);
            $chartImagePath = public_path('public_charts/'.$chartFileName);

            $period = CarbonPeriod::create($startDate->copy()->startOfMonth(), '1 month', $endDate->copy()->endOfMonth());
            $allMonths = collect();
            foreach ($period as $month) {
                $allMonths->push($month->format('Y-m'));
            }

            $roomStats = Room::with(['roomBooking' => function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            }])->get()->map(function ($room) use ($allMonths) {
                $bookings = $room->roomBooking ?? collect();
                $grouped = $bookings->groupBy(function ($booking) {
                    return Carbon::parse($booking->created_at)->format('Y-m');
                });

                $monthly = $allMonths->mapWithKeys(function ($month) use ($grouped) {
                    $group = $grouped->get($month, collect());

                    return [
                        $month => [
                            'total_bookings' => $group->count(),
                            'done' => $group->where('status', 'done')->count(),
                            'completed' => $group->where('status', 'completed')->count(),
                            'cancelled' => $group->where('status', 'cancelled')->count(),
                            'sales' => $group->filter(
                                fn ($b) => in_array($b->status, ['done', 'completed'])
                            )->sum('amount_to_pay'),
                        ],
                    ];
                });

                $labels = $monthly->keys()->toArray();
                $values = $monthly->pluck('total_bookings')->toArray();

                $chartConfig = [
                    'type' => 'bar',
                    'data' => [
                        'labels' => $labels,
                        'datasets' => [[
                            'label' => 'Utilization (Bookings per Month)',
                            'data' => array_values($values),
                            'backgroundColor' => 'rgba(37, 99, 235, 0.7)',
                        ]],
                    ],
                    'options' => [
                        'title' => [
                            'display' => true,
                            'text' => 'Room Utilization Chart',
                        ],
                    ],
                ];

                $chartJson = urlencode(json_encode($chartConfig));
                $quickChartUrl = "https://quickchart.io/chart?c={$chartJson}&width=800&height=400";
                $chartFileName = 'charts/room-utilization-'.$room->id.'-'.time().'.png';
                $imageContents = file_get_contents($quickChartUrl);
                Storage::disk('public_charts')->put($chartFileName, $imageContents);
                $chartPath = public_path('public_charts/'.$chartFileName);

                return [
                    'room_name' => $room->name,
                    'monthly' => $monthly,
                    'chart' => $chartPath,
                ];
            });

            $data = [
                'authorized_name' => Auth::user()->name,
                'report_title' => 'Trends Report',
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'trends' => $timeTrends,
                'grand_total_sales' => $grandTotalSales,
                'grand_total_bookings' => $grandTotalBookings,
                'chart_image_path' => $chartImagePath,
                'group_by' => ($groupFormat === 'Y-m-d' ? 'Day' : 'Month'),
                'type' => 'Room Trends Report',
                'room_stats' => $roomStats,
            ];

            $pdf = Pdf::loadView('reports.reports', compact('data'));
        }

        if ($type == 'reports') {
            $data = [
                'authorized_name' => auth()->user()->name,
                'type' => 'Daily Reports',
                'trasanctions' => Transaction::whereDate('created_at', now())
                    ->whereHas('booking', function ($query) {
                        $query->where('status', 'done');
                    })->with('booking.suiteRoom')->get(),
                'checkout' => Booking::whereDate('created_at', now())->with('suiteRoom')->get(),
            ];

            $bookings = Booking::whereDate('created_at', now())
                ->with('suiteRoom')
                ->get();

            $allChargeIds = $bookings->pluck('additional_charges')
                ->flatten(1)
                ->pluck('name')
                ->unique()
                ->filter();
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
                ->flatten(1)
                ->sum('amount');

            $pdf = Pdf::loadView('reports.reports', compact('data', 'finalList', 'chargetotalAmount'));
        }

        if (! $pdf) {
            abort(400, 'Invalid report type');
        }

        // Stream PDF to display in browser (not download)
        return $pdf->stream($filename);
    }
}
