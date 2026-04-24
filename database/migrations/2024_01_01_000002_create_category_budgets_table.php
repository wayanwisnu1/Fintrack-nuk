<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('category_budgets', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->decimal('amount', 15, 2);
            $table->integer('month');
            $table->integer('year');
            $table->timestamps();

            // Satu kategori hanya punya satu budget per bulan/tahun
            $table->unique(['category', 'month', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_budgets');
    }
};
