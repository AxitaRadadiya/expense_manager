<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Expense;
use App\Models\ItemExpense;
use App\Models\Address;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class VendorController extends Controller
{
    public function index()
    {
        $roleId = Role::where('name', 'vendor')->value('id');

        $vendors = User::where('role_id', $roleId)->orderBy('id')->paginate(15);

        return view('admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('admin.vendors.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|digits:10|unique:users',
        ]);

        $roleId = Role::where('name', 'vendor')->value('id');

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'website' => $request->website,
            'company_name' => $request->company_name,
            'password' => Hash::make('12345678'),
            'role_id' => $roleId,
        ]);

        $user->assignRole((int) $roleId);

        // Save bank details if provided
        $bankData = $request->only(['bank_name','ifsc_code','branch_name','account_no']);
        if (array_filter($bankData)) {
            $bankData['user_id'] = $user->id;
            BankDetail::create($bankData);
        }

        // Save address (billing/shipping)
        $addressData = $request->only([
            'billing_attention','billing_street','billing_city','billing_state','billing_pin_code','billing_country',
            'same_as','shipping_attention','shipping_street','shipping_city','shipping_state','shipping_pin_code','shipping_country',
        ]);

        if ($request->filled('same_as')) {
            $addressData['same_as'] = 1;
            $addressData['shipping_attention'] = $addressData['billing_attention'] ?? null;
            $addressData['shipping_street'] = $addressData['billing_street'] ?? null;
            $addressData['shipping_city'] = $addressData['billing_city'] ?? null;
            $addressData['shipping_state'] = $addressData['billing_state'] ?? null;
            $addressData['shipping_pin_code'] = $addressData['billing_pin_code'] ?? null;
            $addressData['shipping_country'] = $addressData['billing_country'] ?? null;
        }

        if (array_filter($addressData)) {
            $addressData['user_id'] = $user->id;
            Address::create($addressData);
        }

        return redirect()->route('vendor.index')->with('success', 'Vendor created successfully');
    }

    public function show(User $vendor)
    {
        $labourEntries = Expense::where('vendor_id', $vendor->id)
            ->where('category', 'Labour')
            ->with(['project'])
            ->orderByDesc('start_date')
            ->get();

        $itemExpenses = ItemExpense::where('vendor_id', $vendor->id)
            ->with(['item', 'project', 'user'])
            ->orderByDesc('start_date')
            ->get();

        return view('admin.vendors.show', compact('vendor', 'labourEntries', 'itemExpenses'));
    }

    public function edit(User $vendor)
    {
        return view('admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, User $vendor)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $vendor->id,
            'mobile' => 'required|digits:10|unique:users,mobile,' . $vendor->id,
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
        ]);

        $roleId = Role::where('name', 'vendor')->value('id');

        $vendor->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'website' => $request->website,
            'company_name' => $request->company_name,
            'role_id' => $roleId,
        ]);
        $vendor->assignRole((int) $roleId);

        // Update or create bank details
        $bankData = $request->only(['bank_name','ifsc_code','branch_name','account_no']);
        if (array_filter($bankData)) {
            $bank = $vendor->bankDetail()->first();
            if ($bank) {
                $bank->update($bankData);
            } else {
                $bankData['user_id'] = $vendor->id;
                BankDetail::create($bankData);
            }
        }

        // Update or create address
        $addressData = $request->only([
            'billing_attention','billing_street','billing_city','billing_state','billing_pin_code','billing_country',
            'same_as','shipping_attention','shipping_street','shipping_city','shipping_state','shipping_pin_code','shipping_country',
        ]);

        if ($request->filled('same_as')) {
            $addressData['same_as'] = 1;
            $addressData['shipping_attention'] = $addressData['billing_attention'] ?? null;
            $addressData['shipping_street'] = $addressData['billing_street'] ?? null;
            $addressData['shipping_city'] = $addressData['billing_city'] ?? null;
            $addressData['shipping_state'] = $addressData['billing_state'] ?? null;
            $addressData['shipping_pin_code'] = $addressData['billing_pin_code'] ?? null;
            $addressData['shipping_country'] = $addressData['billing_country'] ?? null;
        }

        if (array_filter($addressData)) {
            $address = $vendor->address()->first();
            if ($address) {
                $address->update($addressData);
            } else {
                $addressData['user_id'] = $vendor->id;
                Address::create($addressData);
            }
        }

        return redirect()->route('vendor.index')->with('success', 'Vendor updated successfully.');
    }

    public function destroy(User $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendor.index')->with('success', 'Vendor deleted successfully.');
    }

    public function list(Request $request)
    {
        try {
            $columns = [0 => 'id', 1 => 'name', 2 => 'company_name', 3 => 'mobile', 4 => 'email', 5 => 'action'];

            $limit = intval($request->input('length', 10));
            $start = intval($request->input('start', 0));
            $orderColumnIndex = intval($request->input('order.0.column', 1));
            $order = $columns[$orderColumnIndex] ?? 'name';
            $dir = $request->input('order.0.dir', 'asc');
            $search = $request->input('search.value');

            $roleId = Role::where('name', 'vendor')->value('id');

            $query = User::where('role_id', $roleId);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('mobile', 'like', "%{$search}%")
                      ->orWhere('company_name', 'like', "%{$search}%");
                });
            }

            $totalData = User::where('role_id', $roleId)->count();
            $totalFiltered = $query->count();

            $rows = $query->offset($start)
                ->limit($limit)
                ->orderBy($order, $dir)
                ->get();

            $data = [];
            $i = $start + 1;
            foreach ($rows as $row) {
                $nested = [];
                $nested['id'] = $i;
                $nested['name'] = $row->name;
                $nested['company_name'] = $row->company_name ?? '-';
                $nested['mobile'] = $row->mobile;
                $nested['email'] = $row->email;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';

                if (auth()->check()) {
                    $actions .= '<a href="' . route('vendor.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                    $actions .= '<a href="' . route('vendor.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                    $actions .= '<form action="' . route('vendor.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
                }

                $actions .= '</div></div>';

                $nested['action'] = $actions;
                $data[] = $nested;
                $i++;
            }

            $json_data = [
                'draw' => intval($request->input('draw')),
                'recordsTotal' => intval($totalData),
                'recordsFiltered' => intval($totalFiltered),
                'data' => $data,
            ];

            return response()->json($json_data);
        } catch (\Exception $e) {
            \Log::error('Vendor list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
    
}
