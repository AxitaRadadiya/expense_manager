<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Project;

class PaymentReceive extends Model
{
    use HasFactory;

    protected $table = 'payment_receives';

    protected $fillable = [
        'payment_type', 'customer_id', 'project_id', 'amount', 'payment_date'
    ];

    public function customer() { return $this->belongsTo(User::class, 'customer_id'); }
    public function project() { return $this->belongsTo(Project::class, 'project_id'); }
}
