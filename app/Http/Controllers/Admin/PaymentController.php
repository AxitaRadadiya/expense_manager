<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Payment;
use App\Models\User;

class PaymentController extends Controller
{
    public function index(): View
    {
        $payments = Payment::with(['vendor', 'customer'])->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    public function create(): View
    {
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();

        return view('admin.payments.create', compact('vendors','projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        Payment::create($validated);

        return redirect()->route('payment.index')->with('success', 'Payment recorded');
    }

    public function edit($id): View
    {
        $payment = Payment::findOrFail($id);
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = \App\Models\Project::orderBy('name')->get();

        return view('admin.payments.edit', compact('payment','vendors','projects'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_date' => 'required|date|after_or_equal:today',
        ]);

        $payment->update($validated);

        return redirect()->route('payment.index')->with('success', 'Payment updated');
    }

    public function destroy($id): RedirectResponse
    {
        $payment = Payment::findOrFail($id);
        $payment->delete();
        return redirect()->route('payment.index')->with('success', 'Payment deleted');
    }
}
