<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Transaction::query()->orderBy('date', 'desc');

        // Filter by type
        if ($request->filled('type') && in_array($request->type, ['income', 'expense'])) {
            $query->where('type', $request->type);
        }

        // Filter by date (tanggal spesifik)
        if ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        }
        // Filter by month (bulan + tahun)
        elseif ($request->filled('month')) {
            [$year, $month] = explode('-', $request->month);
            $query->whereYear('date', $year)->whereMonth('date', $month);
        }
        // Filter by year (tahun saja)
        elseif ($request->filled('year')) {
            $query->whereYear('date', $request->year);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Hitung ringkasan hasil filter
        $filteredIncome  = (clone $query)->where('type', 'income')->sum('amount');
        $filteredExpense = (clone $query)->where('type', 'expense')->sum('amount');

        $transactions = $query->paginate(10)->withQueryString();

        // Daftar tahun yang ada transaksinya (untuk dropdown tahun)
        $availableYears = Transaction::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        return view('transactions.index', compact(
            'transactions',
            'filteredIncome',
            'filteredExpense',
            'availableYears'
        ));
    }

    public function create(): View
    {
        $incomeCategories  = Transaction::incomeCategories();
        $expenseCategories = Transaction::expenseCategories();

        return view('transactions.create', compact('incomeCategories', 'expenseCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount'      => 'required|numeric|min:1',
            'type'        => 'required|in:income,expense',
            'category'    => 'required|string|max:50',
            'date'        => 'required|date',
        ]);

        Transaction::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Transaksi berhasil ditambahkan!');
    }

    public function edit(Transaction $transaction): View
    {
        $incomeCategories  = Transaction::incomeCategories();
        $expenseCategories = Transaction::expenseCategories();

        return view('transactions.edit', compact('transaction', 'incomeCategories', 'expenseCategories'));
    }

    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'amount'      => 'required|numeric|min:1',
            'type'        => 'required|in:income,expense',
            'category'    => 'required|string|max:50',
            'date'        => 'required|date',
        ]);

        $transaction->update($validated);

        return redirect()->route('transactions.index')
            ->with('success', 'Transaksi berhasil diperbarui!');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->delete();

        return redirect()->back()
            ->with('success', 'Transaksi berhasil dihapus!');
    }
}
