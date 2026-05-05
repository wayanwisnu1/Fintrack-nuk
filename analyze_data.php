<?php
require __DIR__.'/bootstrap/app.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Transaction;
use App\Models\Goal;
use App\Models\CategoryBudget;
use Carbon\Carbon;

$now = Carbon::now();
$income = Transaction::where('type', 'income')->whereMonth('date', $now->month)->sum('amount');
$expense = Transaction::where('type', 'expense')->whereMonth('date', $now->month)->sum('amount');
$totalIncome = Transaction::where('type', 'income')->sum('amount');
$totalExpense = Transaction::where('type', 'expense')->sum('amount');
$balance = $totalIncome - $totalExpense;

$goals = Goal::where('status', 'active')->get()->map(fn($g) => [
    'name' => $g->name, 
    'target' => (float)$g->target_amount, 
    'balance' => (float)$g->balance
]);

$budgets = CategoryBudget::where('week', $now->weekOfYear)->where('year', $now->year)->get()->map(function($b) {
    $spent = Transaction::where('type', 'expense')
        ->where('category', $b->category)
        ->whereBetween('date', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
        ->sum('amount');
    return [
        'cat' => $b->category, 
        'limit' => (float)$b->amount, 
        'spent' => (float)$spent
    ];
});

$topCategories = Transaction::where('type', 'expense')
    ->whereMonth('date', $now->month)
    ->selectRaw('category, SUM(amount) as total')
    ->groupBy('category')
    ->orderByDesc('total')
    ->limit(3)
    ->get();

echo json_encode([
    'month_income' => (float)$income,
    'month_expense' => (float)$expense,
    'total_balance' => (float)$balance,
    'goals' => $goals,
    'budgets' => $budgets,
    'top_expenses' => $topCategories
], JSON_PRETTY_PRINT);
