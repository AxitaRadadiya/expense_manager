<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemReturn extends Model
{
    use HasFactory;

    protected $table = 'item_returns';

    protected $fillable = [
        'project_id',
        'item_id',
        'date',
        'total_number',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }
}
