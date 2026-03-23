<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory, LogsActivity;               // ✅ LogsActivity added

    protected $fillable = [
        'user_id',
        'created_by',
        'start_date',
        'amount',
    ];

    protected $casts = [
        'start_date' => 'date',
        'amount'     => 'decimal:2',
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