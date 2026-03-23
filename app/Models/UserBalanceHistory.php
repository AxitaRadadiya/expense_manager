<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBalanceHistory extends Model
{
    use HasFactory, LogsActivity;               // ✅ LogsActivity added

    protected $fillable = [
        'user_id',
        'change_type',
        'change_amount',
        'balance_before',
        'balance_after',
        'reference_type',
        'reference_id',
        'created_by',
        'note',
    ];

    protected $casts = [
        'change_amount'  => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after'  => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}