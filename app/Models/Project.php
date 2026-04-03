<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory, LogsActivity;               // ✅ LogsActivity added

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'amount',
        'note',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'amount'     => 'decimal:2',
    ];

    // ── Relationships ─────────────────────────────

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_user', 'project_id', 'user_id');
    }

    public function primaryUsers()
    {
        return $this->hasMany(User::class, 'project_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'projects_id');
    }

    public function credits()
    {
        return $this->hasMany(Credit::class, 'projects_id');
    }
}
