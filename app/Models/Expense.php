<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use LogsActivity;                           // ✅ LogsActivity added

    protected $table = 'expense';

    protected $fillable = [
        'projects_id',
        'users_id',
        'expense_date',
        'category',
        'sub_category',
        'amount',
        'description',
        'note',
        'bill_path',
        'bill_original_name',
        'payment_mode',
        'reference_number',
        'status',
    ];

    // ── Relationships ─────────────────────────────

    protected $casts = [
        'expense_date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projects_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }
}
