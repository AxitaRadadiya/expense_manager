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
        'customer_id', 'project_id', 'sub_category_id', 'amount', 'note', 'invoice_date'
    ];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function project() { return $this->belongsTo(Project::class, 'project_id'); }
    public function subCategory() { return $this->belongsTo(SubCategory::class, 'sub_category_id'); }
}
