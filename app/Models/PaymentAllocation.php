<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentAllocation extends Model
{
    use HasFactory;

    protected $table = 'purchase_paymentmade';

    protected $fillable = [
        'payment_id', 'purchase_id', 'amount'
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
