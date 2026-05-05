<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\CategoryBudget;
use App\Models\Goal;
use Carbon\Carbon;

class FinancialHealthService
{
    /**
     * Hitung Skor Kesehatan Keuangan (1-100)
     */
    public function getScore(): array
    {
        // 1. Rasio Menabung (Savings Ratio) - Bobot 40%
        $savingsScore = $this->calculateSavingsScore();

        // 2. Ketaatan Budget (Budget Adherence) - Bobot 30%
        $budgetScore = $this->calculateBudgetScore();

        // 3. Progres Dana Darurat / Goals - Bobot 30%
        $goalsScore = $this->calculateGoalsScore();

        // Total Skor Akhir
        $totalScore = ($savingsScore * 0.4) + ($budgetScore * 0.3) + ($goalsScore * 0.3);
        $totalScore = round(max(1, min(100, $totalScore)));

        return [
            'total' => $totalScore,
            'savings' => $savingsScore,
            'budget' => $budgetScore,
            'goals' => $goalsScore,
            'rating' => $this->getRatingLabel($totalScore),
            'advice' => $this->getAdvice($totalScore, $savingsScore, $budgetScore, $goalsScore)
        ];
    }

    private function calculateSavingsScore(): float
    {
        $thisMonth = Carbon::now();
        $income = Transaction::income()->whereMonth('date', $thisMonth->month)->whereYear('date', $thisMonth->year)->sum('amount');
        $expense = Transaction::expense()->whereMonth('date', $thisMonth->month)->whereYear('date', $thisMonth->year)->sum('amount');

        if ($income <= 0) return 0;

        $savings = $income - $expense;
        $ratio = ($savings / $income) * 100;

        // Ideal ratio adalah 20% (Skor 100). Jika 0% atau minus, skor 0.
        return min(100, max(0, ($ratio / 20) * 100));
    }

    private function calculateBudgetScore(): float
    {
        $thisWeek = Carbon::now()->weekOfYear;
        $thisYear = Carbon::now()->year;
        
        $budgets = CategoryBudget::where('week', $thisWeek)->where('year', $thisYear)->get();
        if ($budgets->isEmpty()) return 50; // Skor tengah jika belum set budget

        $overBudgetCount = 0;
        foreach ($budgets as $budget) {
            $spending = Transaction::expense()
                ->where('category', $budget->category)
                ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('amount');

            if ($spending > $budget->amount) {
                $overBudgetCount++;
            }
        }

        $totalBudgets = $budgets->count();
        return (($totalBudgets - $overBudgetCount) / $totalBudgets) * 100;
    }

    private function calculateGoalsScore(): float
    {
        $goals = Goal::where('status', 'active')->get();
        if ($goals->isEmpty()) return 50;

        $totalProgress = 0;
        foreach ($goals as $goal) {
            $totalProgress += $goal->percentage;
        }

        return min(100, $totalProgress / $goals->count());
    }

    private function getRatingLabel($score): string
    {
        if ($score >= 80) return 'Sehat Sekali';
        if ($score >= 60) return 'Cukup Sehat';
        if ($score >= 40) return 'Kurang Sehat';
        return 'Kritis';
    }

    private function getAdvice($total, $savings, $budget, $goals): string
    {
        if ($total >= 80) return "Luar biasa! Pertahankan kebiasaan menabungmu.";
        if ($savings < 50) return "Coba sisihkan minimal 20% pemasukanmu untuk tabungan.";
        if ($budget < 70) return "Beberapa pengeluaranmu melebilyi budget, yuk lebih disiplin.";
        if ($goals < 50) return "Ayo lebih rutin mengisi pos tabungan target keuanganmu.";
        return "Mulai catat setiap transaksi agar keuanganmu lebih terkontrol.";
    }
}
