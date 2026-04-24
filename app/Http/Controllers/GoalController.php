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

        $goal->increment('balance', $validated['amount']);

        if ($goal->balance >= $goal->target_amount) {
            $goal->update(['status' => 'completed']);
        }

        return back()->with('success', 'Saldo berhasil ditambahkan ke ' . $goal->name);
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();
        return back()->with('success', 'Target berhasil dihapus.');
    }
}
