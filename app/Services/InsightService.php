<?php

namespace App\Services;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class InsightService
{
    /**
     * Ambil daftar insight/anomali keuangan
     */
    public function getInsights(): Collection
    {
        $insights = collect();

        $this->checkCategorySpikes($insights);
        $this->checkLargeTransactions($insights);
        $this->checkNewCategoryUsage($insights);

        return $insights;
    }

    /**
     * Deteksi lonjakan pengeluaran per kategori (Minggu ini vs Minggu lalu)
     */
    private function checkCategorySpikes(Collection $insights): void
    {
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisWeekEnd = Carbon::now()->endOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        $thisWeekSpending = Transaction::expense()
            ->whereBetween('date', [$thisWeekStart, $thisWeekEnd])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $lastWeekSpending = Transaction::expense()
            ->whereBetween('date', [$lastWeekStart, $lastWeekEnd])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categories = Transaction::expenseCategories();

        foreach ($thisWeekSpending as $category => $currentAmount) {
            if (isset($lastWeekSpending[$category]) && $lastWeekSpending[$category] > 0) {
                $prevAmount = $lastWeekSpending[$category];
                $increasePct = (($currentAmount - $prevAmount) / $prevAmount) * 100;

                // Jika naik lebih dari 30% dan nominal kenaikan > 50rb
                if ($increasePct >= 30 && ($currentAmount - $prevAmount) >= 50000) {
                    $categoryLabel = $categories[$category] ?? $category;
                    $insights->push([
                        'type' => 'spike',
                        'title' => 'Lonjakan Pengeluaran',
                        'message' => "Pengeluaran **$categoryLabel** naik **" . round($increasePct) . "%** dibanding minggu lalu. Ada apa?",
                        'icon' => 'trending-up',
                        'color' => 'var(--expense)'
                    ]);
                }
            }
        }
    }

    /**
     * Deteksi transaksi tunggal yang nilainya tidak wajar (Anomali)
     */
    private function checkLargeTransactions(Collection $insights): void
    {
        $avgTransaction = Transaction::expense()->avg('amount') ?: 0;
        
        if ($avgTransaction <= 0) return;

        // Cari transaksi minggu ini yang nilainya > 4x rata-rata
        $largeTransactions = Transaction::expense()
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->where('amount', '>', $avgTransaction * 4)
            ->get();

        foreach ($largeTransactions as $t) {
            $insights->push([
                'type' => 'anomaly',
                'title' => 'Transaksi Besar Terdeteksi',
                'message' => "Transaksi **\"$t->title\"** bernilai **Rp " . number_format($t->amount, 0, ',', '.') . "** jauh di atas rata-rata pengeluaranmu.",
                'icon' => 'alert-circle',
                'color' => 'var(--accent2)'
            ]);
        }
    }

    /**
     * Deteksi penggunaan kategori baru yang jarang digunakan
     */
    private function checkNewCategoryUsage(Collection $insights): void
    {
        $thisWeekCategories = Transaction::expense()
            ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->distinct()
            ->pluck('category');

        $historicCategories = Transaction::expense()
            ->where('date', '<', Carbon::now()->startOfWeek())
            ->distinct()
            ->pluck('category');

        $newCategories = $thisWeekCategories->diff($historicCategories);
        $categoriesLabel = Transaction::expenseCategories();

        foreach ($newCategories as $cat) {
            $label = $categoriesLabel[$cat] ?? $cat;
            $insights->push([
                'type' => 'new_habit',
                'title' => 'Kebiasaan Baru?',
                'message' => "Kamu baru saja mulai belanja di kategori **$label**. Pastikan tetap sesuai rencana ya!",
                'icon' => 'sparkles',
                'color' => 'var(--accent)'
            ]);
        }
    }
}
