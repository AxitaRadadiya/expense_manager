<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        // Follow Item Management UX: default to Vendors list
        return redirect()->route('vendor.index');
    }
}
