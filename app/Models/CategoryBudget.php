<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryBudget extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'amount',
        'week',
        'year',
    ];

    /**
     * Hitung sisa budget untuk kategori ini
     */
    public function getRemainingAttribute($actualSpending)
    {
        return $this->amount - $actualSpending;
    }

    /**
     * Hitung persentase pemakaian
     */
    public function getPercentageAttribute($actualSpending)
    {
        if ($this->amount <= 0) return 0;
        return ($actualSpending / $this->amount) * 100;
    }
}
