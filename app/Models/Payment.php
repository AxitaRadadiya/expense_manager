<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id', 'project_id', 'amount', 'payment_date'
    ];

    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class, 'payment_id');
    }

    public function purchases()
    {
        return $this->hasManyThrough(Purchase::class, PaymentAllocation::class, 'payment_id', 'id', 'id', 'purchase_id');
    }
}
