<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'amount',
        'type',
        'category',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    // Accessors
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    // Categories
    public static function incomeCategories(): array
    {
        return [
            'salary'      => '💼 Transfer',
            'freelance'   => '💻 Freelance',
            'investment'  => '📈 Investasi',
            'bonus'       => '🎁 Bonus',
            'other'       => '💰 Lainnya',
        ];
    }

    public static function expenseCategories(): array
    {
        return [
            'food'        => '🍜 Makanan & Minuman',
            'transport'   => '🚗 Transportasi',
            'shopping'    => '🛍️ Belanja',
            'health'      => '🏥 Kesehatan',
            'education'   => '📚 Pendidikan',
            'entertainment' => '🎮 Hiburan',
            'bills'       => '📄 Tagihan',
            'other'       => '💸 Lainnya',
        ];
    }
}
