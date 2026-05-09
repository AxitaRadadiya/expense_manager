<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Purchase;
use App\Models\Project;
use App\Models\User;
use App\Models\SubCategory;
use App\Models\Category;

class PurchaseController extends Controller
{
    public function index(): View
    {
        $purchases = Purchase::with(['vendor', 'project', 'subCategory'])->latest()->get();
        $payments = \App\Models\Payment::with(['vendor','project'])->latest()->get();
        return view('admin.purchase.index', compact('purchases','payments'));
    }

    public function create(): View
    {
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();

        $expenseCategory = Category::where('name', 'Expense')->first();
        $expenseSubCategories = $expenseCategory ? SubCategory::where('category_id', $expenseCategory->id)->orderBy('name')->get() : collect();

        return view('admin.purchase.create', compact('vendors', 'projects', 'expenseSubCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'note' => 'required|string',
            'purchase_date' => 'required|date|after_or_equal:today',
        ]);

        Purchase::create($validated);

        return redirect()->route('purchase.index')->with('success', 'Purchase created successfully');
    }

    public function edit($id): View
    {
        $purchase = Purchase::findOrFail($id);
        $vendorRoleId = \App\Models\Role::where('name', 'vendor')->value('id');
        $vendors = User::where('role_id', $vendorRoleId)->orderBy('name')->get();
        $projects = Project::orderBy('name')->get();
        $expenseCategory = Category::where('name', 'Expense')->first();
        $expenseSubCategories = $expenseCategory ? SubCategory::where('category_id', $expenseCategory->id)->orderBy('name')->get() : collect();

        return view('admin.purchase.edit', compact('purchase','vendors','projects','expenseSubCategories'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $purchase = Purchase::findOrFail($id);

        $validated = $request->validate([
            'vendor_id' => 'required|exists:users,id',
            'project_id' => 'required|exists:projects,id',
            'sub_category_id' => 'required|exists:sub_categories,id',
            'amount' => 'required|numeric|min:0.01',
            'quantity' => 'required|integer|min:1',
            'note' => 'required|string',
            'purchase_date' => 'required|date|after_or_equal:today',
        ]);

        $purchase->update($validated);

        return redirect()->route('purchase.index')->with('success', 'Purchase updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        $purchase = Purchase::findOrFail($id);
        $purchase->delete();
        return redirect()->route('purchase.index')->with('success', 'Purchase deleted');
    }
}
