<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Project;
use App\Models\SubCategory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'project_id', 'sub_category_id', 'amount', 'due_amount', 'note', 'invoice_date', 'status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'due_amount' => 'decimal:2',
    ];
    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function project() { return $this->belongsTo(Project::class, 'project_id'); }
    public function subCategory() { return $this->belongsTo(SubCategory::class, 'sub_category_id'); }
    public function invoiceItems() { return $this->hasMany(InvoiceItem::class); }
}
