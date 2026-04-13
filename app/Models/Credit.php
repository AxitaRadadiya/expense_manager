<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Credit extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'credit';

    protected $fillable = [
        'projects_id',
        'users_id',
        'credit_date',
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

    protected $casts = [
        'credit_date' => 'date',
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
