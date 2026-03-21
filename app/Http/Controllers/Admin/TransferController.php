<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transfer;
use App\Models\User;

class TransferController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

     public function index()
    {
        $transfers = Transfer::with(['user', 'creator'])->latest()->get();
        return view('admin.transfer.index', compact('transfers'));
    }
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('admin.transfer.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'start_date' => 'nullable|date',
            'amount' => 'required|numeric',
        ]);

        $data['created_by'] = auth()->id();

        $transfer = Transfer::create($data);

        return redirect()->back()->with('success', 'Transfer saved successfully.');
    }

    // optional index/show/edit/destroy can be added later
}
