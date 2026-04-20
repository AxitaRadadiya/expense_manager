<?php

namespace App\Models;

use App\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use LogsActivity;
    protected $fillable = ['name'];
}
