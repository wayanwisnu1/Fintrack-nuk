<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function() {
    return redirect()->route('login');
});

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
    Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
    Route::put('/transactions/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');

    // Export & Import
    Route::get('/transactions/export/excel', [TransactionController::class, 'exportExcel'])->name('transactions.export.excel');
    Route::get('/transactions/export/pdf', [TransactionController::class, 'exportPdf'])->name('transactions.export.pdf');
    Route::post('/transactions/import', [TransactionController::class, 'importExcel'])->name('transactions.import');

    // Budgeting
    Route::get('/budgets', [BudgetController::class, 'index'])->name('budgets.index');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');

    // Financial Goals
    Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
    Route::post('/goals', [GoalController::class, 'store'])->name('goals.store');
    Route::post('/goals/{goal}/deposit', [GoalController::class, 'deposit'])->name('goals.deposit');
    Route::delete('/goals/{goal}', [GoalController::class, 'destroy'])->name('goals.destroy');
});

Route::get('/run-migration', function() {
    try {
        $msg = "";
        
        // Fix Category Budgets (Week system)
        if (\Illuminate\Support\Facades\Schema::hasTable('category_budgets')) {
            \Illuminate\Support\Facades\Schema::table('category_budgets', function ($table) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('category_budgets', 'month')) {
                    $table->renameColumn('month', 'week');
                }
            });
            $msg .= "Tabel category_budgets diperbarui (Mingguan).<br>";
        }

        // Create Goals Table
        if (!\Illuminate\Support\Facades\Schema::hasTable('goals')) {
            \Illuminate\Support\Facades\Schema::create('goals', function ($table) {
                $table->id();
                $table->string('name');
                $table->decimal('target_amount', 15, 2);
                $table->decimal('balance', 15, 2)->default(0);
                $table->text('note')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });
            $msg .= "Tabel goals berhasil dibuat!<br>";
        } else {
            $msg .= "Tabel goals sudah ada.<br>";
        }

        return "<h3>Proses Selesai:</h3>" . $msg . "<br><a href='/goals'>Klik di sini untuk ke halaman Target</a>";
    } catch (\Exception $e) {
        return "Terjadi kesalahan: " . $e->getMessage();
    }
});

