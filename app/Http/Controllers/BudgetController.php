<?php

namespace App\Http\Controllers;

use App\Models\CategoryBudget;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        // Jika tidak ada input, gunakan minggu sekarang
        $week = $request->get('week', now()->weekOfYear);
        $year = $request->get('year', now()->year);

        // Ambil semua budget untuk minggu & tahun yang dipilih
        $budgets = CategoryBudget::where('week', $week)
            ->where('year', $year)
            ->get();

        // Hitung rentang tanggal untuk minggu tersebut (Senin - Minggu)
        $startOfWeek = now()->setISODate($year, $week)->startOfWeek();
        $endOfWeek = now()->setISODate($year, $week)->endOfWeek();

        // Ambil total pengeluaran per kategori dalam rentang minggu tersebut
        $actualSpending = Transaction::where('type', 'expense')
            ->whereBetween('date', [$startOfWeek, $endOfWeek])
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $expenseCategories = Transaction::expenseCategories();

        return view('budgets.index', compact(
            'budgets', 
            'actualSpending', 
            'expenseCategories', 
            'week', 
            'year',
            'startOfWeek',
            'endOfWeek'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'amount'   => 'required|numeric|min:0',
            'week'     => 'required|integer|between:1,53',
            'year'     => 'required|integer',
        ]);

        CategoryBudget::updateOrCreate(
            [
                'category' => $validated['category'],
                'week'     => $validated['week'],
                'year'     => $validated['year'],
            ],
            ['amount' => $validated['amount']]
        );

        return back()->with('success', 'Anggaran mingguan berhasil diperbarui!');
    }

    public function destroy(CategoryBudget $budget)
    {
        $budget->delete();
        return back()->with('success', 'Anggaran berhasil dihapus!');
    }
}
