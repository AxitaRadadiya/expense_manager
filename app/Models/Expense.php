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
        'vendor_id',
        'start_date',
        'end_date',
        'total_labour',
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
        'total_labour' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'projects_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }
}
