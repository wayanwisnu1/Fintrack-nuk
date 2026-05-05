<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;

class ForecastingService
{
    /**
     * Prediksi kapan saldo akan habis berdasarkan rata-rata pengeluaran 3 bulan terakhir.
     */
    public function getForecast(): array
    {
        $now = Carbon::now();
        $threeMonthsAgo = $now->copy()->subDays(90);

        // 1. Hitung total saldo saat ini
        $totalIncome = Transaction::income()->sum('amount');
        $totalExpense = Transaction::expense()->sum('amount');
        $currentBalance = $totalIncome - $totalExpense;

        // 2. Hitung rata-rata pengeluaran harian dalam 90 hari terakhir
        // Ambil transaksi pertama untuk menentukan umur akun jika kurang dari 90 hari
        $firstTransaction = Transaction::orderBy('date', 'asc')->first();
        $daysToDivide = 90;

        if ($firstTransaction) {
            $daysSinceFirst = $firstTransaction->date->diffInDays($now);
            if ($daysSinceFirst < 90) {
                $daysToDivide = max($daysSinceFirst, 7); // Minimal bagi 7 hari agar tidak terlalu fluktuatif
            }
        }

        $expenseLast90Days = Transaction::expense()
            ->where('date', '>=', $threeMonthsAgo)
            ->sum('amount');

        $averageDailyExpense = $expenseLast90Days / $daysToDivide;

        // 3. Hitung proyeksi
        if ($currentBalance <= 0) {
            return [
                'status' => 'critical',
                'message' => 'Saldo Anda sudah habis atau negatif.',
                'days_remaining' => 0,
                'forecast_date' => null,
                'daily_average' => $averageDailyExpense
            ];
        }

        if ($averageDailyExpense <= 0) {
            return [
                'status' => 'safe',
                'message' => 'Tidak ada pengeluaran terdeteksi dalam 3 bulan terakhir.',
                'days_remaining' => 999, // Infinite
                'forecast_date' => null,
                'daily_average' => 0
            ];
        }

        $daysRemaining = floor($currentBalance / $averageDailyExpense);
        $forecastDate = $now->copy()->addDays($daysRemaining);

        // Tentukan level peringatan
        $status = 'normal';
        if ($daysRemaining <= 7) {
            $status = 'critical';
        } elseif ($daysRemaining <= 20) {
            $status = 'warning';
        }

        return [
            'status' => $status,
            'days_remaining' => $daysRemaining,
            'forecast_date' => $forecastDate,
            'daily_average' => $averageDailyExpense,
            'current_balance' => $currentBalance,
            'message' => $this->formatForecastMessage($daysRemaining, $forecastDate)
        ];
    }

    private function formatForecastMessage($days, $date): string
    {
        if ($days > 365) return "Saldo Anda diprediksi aman hingga lebih dari setahun ke depan.";
        
        $formattedDate = $date->translatedFormat('d F Y');
        
        if ($days <= 0) return "Berdasarkan pola belanja, saldo Anda diprediksi habis hari ini!";
        
        return "Jika pola belanja tetap seperti ini, saldo Anda diprediksi akan habis pada tanggal **$formattedDate** ($days hari lagi).";
    }
}
