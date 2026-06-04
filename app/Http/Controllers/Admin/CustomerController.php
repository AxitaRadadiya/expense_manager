<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Address;
use App\Models\BankDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    public function index()
    {
        $roleId = Role::where('name', 'customer')->value('id');

        $customers = User::where('role_id', $roleId)->orderBy('id')->paginate(15);

        return view('admin.customers.index', compact('customers'));
    }

    public function create()
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('customer-create')) {
            abort(403);
        }

        return view('admin.customers.create');
    }
    
    public function store(Request $request)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('customer-create')) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'mobile' => 'required|digits:10|unique:users',
        ]);

        $roleId = Role::where('name', 'customer')->value('id');

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name'=> trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'company_name' => $request->company_name,
            'website' => $request->website,
            'pan_number' => $request->pan_number,
            'gst_number' => $request->gst_number,
            'password' => Hash::make('12345678'),
            'role_id' => $roleId,
        ]);

        $user->assignRole((int) $roleId);

        // Save address
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

        // Save bank details
        $bankData = $request->only(['bank_name','ifsc_code','branch_name','account_no']);
        if (array_filter($bankData)) {
            $bankData['user_id'] = $user->id;
            BankDetail::create($bankData);
        }

        return redirect()->route('customer.index')->with('success', 'Customer created successfully');
    }

    public function show(User $customer)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('customer-view')) {
            abort(403);
        }

        return view('admin.customers.show', compact('customer'));
    }

    public function edit(User $customer)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('customer-edit')) {
            abort(403);
        }

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('customer-edit')) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $customer->id,
            'mobile' => 'required|digits:10|unique:users,mobile,' . $customer->id,
            'company_name' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'pan_number' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:255',
        ]);

        $roleId = Role::where('name', 'customer')->value('id');

        $customer->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'name' => trim($request->first_name . ' ' . $request->last_name),
            'email' => $request->email,
            'mobile' => $request->mobile,
            'company_name' => $request->company_name,
            'website' => $request->website,
            'pan_number' => $request->pan_number,
            'gst_number' => $request->gst_number,
            'role_id' => $roleId,
        ]);
        $customer->assignRole((int) $roleId);

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
            $address = $customer->address()->first();
            if ($address) {
                $address->update($addressData);
            } else {
                $addressData['user_id'] = $customer->id;
                Address::create($addressData);
            }
        }

        // Update or create bank details
        $bankData = $request->only(['bank_name','ifsc_code','branch_name','account_no']);
        if (array_filter($bankData)) {
            $bank = $customer->bankDetail()->first();
            if ($bank) {
                $bank->update($bankData);
            } else {
                $bankData['user_id'] = $customer->id;
                BankDetail::create($bankData);
            }
        }

        return redirect()->route('customer.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(User $customer)
    {
        $auth = auth()->user();
        if (! $auth || ! $auth->hasPermission('customer-delete')) {
            abort(403);
        }

        $customer->delete();
        return redirect()->route('customer.index')->with('success', 'Customer deleted successfully.');
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

            $roleId = Role::where('name', 'customer')->value('id');

            $auth = auth()->user();
            $canView = $auth?->hasPermission('customer-view') ?? false;
            $canEdit = $auth?->hasPermission('customer-edit') ?? false;
            $canDelete = $auth?->hasPermission('customer-delete') ?? false;
            $query = User::where('role_id', $roleId);
            if (!empty($search)) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
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
                $nested['name'] = trim($row->first_name . ' ' . $row->last_name) ?? $row->name;
                $nested['company_name'] = $row->company_name ?? '-';
                $nested['mobile'] = $row->mobile;
                $nested['email'] = $row->email;

                $actions = '<div class="btn-group">';
                $actions .= "<i class=\"fas fa-ellipsis-v\" data-toggle=\"dropdown\" style=\"cursor:pointer;\"></i>";
                $actions .= '<div class="dropdown-menu dropdown-menu-right" style="min-width: 50px; padding: 0;">';

                if ($canView) {
                    $actions .= '<a href="' . route('customer.show', $row->id) . '" class="table-action-btn is-view" title="View"><i class="fa fa-eye"></i></a>';
                }
                if ($canEdit) {
                    $actions .= '<a href="' . route('customer.edit', $row->id) . '" class="table-action-btn is-edit" title="Edit"><i class="fa fa-edit"></i></a>';
                }
                if ($canDelete) {
                    $actions .= '<form action="' . route('customer.destroy', $row->id) . '" method="POST" class="table-action-form">' . csrf_field() . '<input type="hidden" name="_method" value="DELETE">' . '<button type="button" class="table-action-btn is-delete deleteButton" title="Delete"><i class="fa fa-trash"></i></button></form>';
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
            \Log::error('Customer list error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error', 'message' => $e->getMessage()], 500);
        }
    }
    
}
