<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseLabour extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id', 'labour', 'numbers', 'date_start', 'date_end', 'amount', 'total_amount', 'note'
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
