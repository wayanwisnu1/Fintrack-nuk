<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'target_amount',
        'balance',
        'note',
        'status', // 'active', 'completed'
    ];

    /**
     * Hitung persentase progres
     */
    public function getPercentageAttribute()
    {
        if ($this->target_amount <= 0) return 0;
        return ($this->balance / $this->target_amount) * 100;
    }

    /**
     * Hitung sisa yang harus ditabung
     */
    public function getRemainingAttribute()
    {
        return max(0, $this->target_amount - $this->balance);
    }
}
