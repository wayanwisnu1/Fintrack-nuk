<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index()
    {
        $goals = Goal::orderBy('status', 'asc')->orderBy('created_at', 'desc')->get();
        return view('goals.index', compact('goals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|max:255',
            'target_amount' => 'required|numeric|min:1',
            'note'          => 'nullable|string',
        ]);

        Goal::create($validated);

        return back()->with('success', 'Target keuangan baru berhasil dibuat!');
    }

    public function deposit(Request $request, Goal $goal)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $amount = $validated['amount'];

        // 1. Tambah saldo di tabel Goals
        $goal->increment('balance', $amount);

        // 2. Buat transaksi pengeluaran otomatis agar saldo dashboard berkurang
        \App\Models\Transaction::create([
            'title'       => 'Menabung: ' . $goal->name,
            'amount'      => $amount,
            'type'        => 'expense',
            'category'    => 'investment', // Masuk kategori investasi
            'date'        => now(),
            'description' => 'Alokasi dana untuk target keuangan: ' . $goal->name,
        ]);

        if ($goal->balance >= $goal->target_amount) {
            $goal->update(['status' => 'completed']);
        }

        return back()->with('success', 'Saldo berhasil dialokasikan ke ' . $goal->name . '. Saldo utama Anda telah berkurang.');
    }

    public function destroy(Goal $goal)
    {
        $currentBalance = $goal->balance;

        // Jika ada saldo yang sudah ditabung, kembalikan ke saldo utama
        if ($currentBalance > 0) {
            \App\Models\Transaction::create([
                'title'       => 'Pembatalan Target: ' . $goal->name,
                'amount'      => $currentBalance,
                'type'        => 'income',
                'category'    => 'other',
                'date'        => now(),
                'description' => 'Pengembalian saldo karena target keuangan dihapus/dibatalkan.',
            ]);
        }

        $goal->delete();

        return back()->with('success', 'Target berhasil dihapus. Saldo sebesar Rp ' . number_format($currentBalance, 0, ',', '.') . ' telah dikembalikan ke saldo utama Anda.');
    }
}
