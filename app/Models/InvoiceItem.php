<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id', 'item_id', 'sub_category_id', 'qty', 'unit_amount', 'total_amount'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'unit_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function item() { return $this->belongsTo(Item::class); }
    public function subCategory() { return $this->belongsTo(SubCategory::class, 'sub_category_id'); }
}
