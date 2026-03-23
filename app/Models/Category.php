<?php

namespace App\Models;

use App\Traits\LogsActivity;                    // ✅ added
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use LogsActivity;                           // ✅ LogsActivity added

    protected $fillable = ['name'];
}