<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Mencoba membuat tabel category_budgets...\n";

try {
    if (!Schema::hasTable('category_budgets')) {
        Schema::create('category_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();
            $table->unique(['category', 'month', 'year']);
        });
        echo "Sukses! Tabel 'category_budgets' berhasil dibuat.\n";
    } else {
        echo "Tabel 'category_budgets' ternyata sudah ada.\n";
    }
} catch (\Exception $e) {
    echo "Gagal: " . $e->getMessage() . "\n";
}
