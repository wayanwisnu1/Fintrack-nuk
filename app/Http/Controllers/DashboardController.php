<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request): View
    {
        // Summary totals
        $totalIncome  = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $balance      = $totalIncome - $totalExpense;

        // This month stats
        $monthIncome  = Transaction::income()->thisMonth()->sum('amount');
        $monthExpense = Transaction::expense()->thisMonth()->sum('amount');

        // Recent transactions
        $recentTransactions = Transaction::orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Monthly chart data (last 6 months)
        $chartData = $this->getMonthlyChartData();

        // Expense breakdown by category (this month)
        $expenseByCategory = Transaction::expense()
            ->thisMonth()
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Pengeluaran & pemasukan per hari dalam beberapa minggu terakhir
        $weeksToDisplay = (int) $request->query('weeks', 2);
        if ($weeksToDisplay < 1) $weeksToDisplay = 2;
        if ($weeksToDisplay > 12) $weeksToDisplay = 12; // Limit to 3 months max for performance

        $weeklyData = $this->getWeeklyDailyData($weeksToDisplay);

        return view('dashboard.index', compact(
            'totalIncome',
            'totalExpense',
            'balance',
            'monthIncome',
            'monthExpense',
            'recentTransactions',
            'chartData',
            'expenseByCategory',
            'weeklyData',
            'weeksToDisplay'
        ));
    }

    private function getMonthlyChartData(): array
    {
        $months = collect();

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $label = $date->translatedFormat('M Y');

            $income = Transaction::income()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $expense = Transaction::expense()
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $months->push([
                'label'   => $label,
                'income'  => (float) $income,
                'expense' => (float) $expense,
            ]);
        }

        return $months->toArray();
    }

    private function getWeeklyDailyData(int $weeksCount = 2): array
    {
        $weeks = [];
        $dayNames = ['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'];

        // Ambil beberapa minggu terakhir
        for ($w = 0; $w < $weeksCount; $w++) {
            $days = [];
            $startOfWeek = now()->subWeeks($w)->startOfWeek(\Carbon\Carbon::MONDAY);

            for ($i = 0; $i < 7; $i++) {
                $date = $startOfWeek->copy()->addDays($i);
                $isToday = $date->isToday();
                $isFuture = $date->isFuture() && !$isToday;

                $expense = Transaction::expense()
                    ->whereDate('date', $date->toDateString())
                    ->sum('amount');

                $income = Transaction::income()
                    ->whereDate('date', $date->toDateString())
                    ->sum('amount');

                // Ambil transaksi detail hari ini
                $transactions = Transaction::whereDate('date', $date->toDateString())
                    ->orderBy('created_at', 'desc')
                    ->get();

                $days[] = [
                    'label'        => $dayNames[$i],
                    'date'         => $date->format('d M'),
                    'full_date'    => $date->toDateString(),
                    'expense'      => (float) $expense,
                    'income'       => (float) $income,
                    'is_today'     => $isToday,
                    'is_future'    => $isFuture,
                    'transactions' => $transactions,
                ];
            }

            $label = 'Minggu Ini';
            if ($w === 1) $label = 'Minggu Lalu';
            if ($w > 1) $label = $w . ' Minggu Lalu';

            $weeks[] = [
                'week_label'    => $label,
                'range'         => $startOfWeek->format('d M') . ' – ' . $startOfWeek->copy()->endOfWeek(\Carbon\Carbon::SUNDAY)->format('d M Y'),
                'days'          => $days,
                'total_expense' => collect($days)->sum('expense'),
                'is_current'    => $w === 0,
            ];
        }

        return $weeks;
    }
}
