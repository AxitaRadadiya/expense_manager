<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Spatie\Permission\Models\Role;
use Illuminate\View\View;
use App\Models\User;
use App\Models\Address;
use App\Models\BankDetails;
use App\Models\Invoice;
use App\Models\PaymentReceived;
use Hash;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Spatie\Activitylog\Models\Activity;


class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:user-create|user-edit|user-view|user-delete', ['only' => ['index','show']]);
        $this->middleware('permission:user-create', ['only' => ['create','store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }


     /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // return view('admin.users.index', [
        //     'users' => User::orderBy('id')->paginate(15)
        // ]);

        return view('admin.users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.users.create', [
            'roles' => Role::pluck('name')->all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'salutatior' => ['required'],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'company_name' => ['required'],
            // 'display_name' => ['required'],
            // 'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            // 'work_phone' => ['required', 'digits:10'],
            // 'mobile' => ['required', 'digits:10'],
            // 'website' => ['required'],
            // 'payment_terms' => ['required'],
            // 'gst_treatment' => ['required'],
            // 'place_of_supply' => ['required'],
            // 'opening_balance' => ['required'],
            // 'pan_number' => ['required'],
            // 'discount' => ['required'],
            // 'credit_limit' => ['required'],
            // 'billing_attention' => ['required'],
            // 'billing_street' => ['required'],
            // 'billing_city' => ['required'],
            // 'billing_state' => ['required'],
            // 'billing_pin_code' => ['required'],
            // 'billing_country' => ['required'],
            // 'billing_gst_number' => ['required'],
            // 'shipping_attention' => ['required'],
            // 'shipping_street' => ['required'],
            // 'shipping_city' => ['required'],
            // 'shipping_state' => ['required'],
            // 'shipping_pin_code' => ['required'],
            // 'shipping_country' => ['required'],
            // 'shipping_gst_number' => ['required'],
            // 'bank_name' => ['required'],
            // 'branch_name' => ['required'],
            // 'ifsc_code' => ['required'],
            // 'account_no' => ['required'],
        ]);

        $loginUser = Auth::user();

        $data = $request->all();

        $role = Role::where('name', 'user')->first();
        $roleId = $role->id;

        $data['user_type'] = 'customer';
        $data['name'] = $data['salutatior'] ." ".$data['firstname']." ".$data['lastname'];
        $data['password'] = Hash::make(12345678);
        $data['role_id'] = $roleId;
        $data['status'] = $loginUser->hasRole('super-admin') ? 1 : 0;
        $data['email'] = $request->email;
        $data['same_as'] = isset($data['same_as']) ? 1 : '';

        // $userInput['name'] = $request->name;
        // $userInput['company_name'] = $request->company_name;
        // $userInput['phone'] = $request->phone;
        // $userInput['password'] = Hash::make(12345678);
        // $userInput['role_id'] = 2;
        // $userInput['status'] = $loginUser->hasRole('super-admin') ? 1 : 0;

        $user = User::create($data);
        $user->assignRole('user');

        $data['user_id'] = $user->id;

        Address::create($data);
        BankDetails::create($data);

        Activity::create([
            'log_name' => 'Customer Log',
            'description' => $loginUser->name.' added Customer: '.$user->name. ' And ID is '.$user->id,
            'subject_type' => User::class,
            'subject_id' => $user->id,
            'causer_type' => User::class,
            'causer_id' => $loginUser->id,
            'event' => 'created',
            'properties' => $request->except(['_token','_method']), 
        ]);

        return redirect()->route('users.index')
                ->withSuccess('New user is added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        $user=User::find($user->id);

        return view('admin.users.show', [
            'user' => $user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        // Check Only Super Admin can update his own Profile
        $user = Auth::user();
        if ($user->hasRole('Super Admin')){
            if($user->id != auth()->user()->id){
                abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
            }
        }

        $user=User::find($id);

        return view('admin.users.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id): RedirectResponse{
        $request->validate([
            'salutatior' => ['required'],
            'firstname' => ['required'],
            'lastname' => ['required'],
            'company_name' => ['required'],
            // 'display_name' => ['required'],
            // 'work_phone' => ['required', 'digits:10'],
            // 'mobile' => ['required', 'digits:10'],
            // 'website' => ['required'],
            // 'payment_terms' => ['required'],
            // 'gst_treatment' => ['required'],
            // 'place_of_supply' => ['required'],
            // 'opening_balance' => ['required'],
            // 'pan_number' => ['required'],
            // 'discount' => ['required'],
            // 'credit_limit' => ['required'],
            // 'billing_attention' => ['required'],
            // 'billing_street' => ['required'],
            // 'billing_city' => ['required'],
            // 'billing_state' => ['required'],
            // 'billing_pin_code' => ['required'],
            // 'billing_country' => ['required'],
            // 'billing_gst_number' => ['required'],
            // 'shipping_attention' => ['required'],
            // 'shipping_street' => ['required'],
            // 'shipping_city' => ['required'],
            // 'shipping_state' => ['required'],
            // 'shipping_pin_code' => ['required'],
            // 'shipping_country' => ['required'],
            // 'shipping_gst_number' => ['required'],
            // 'bank_name' => ['required'],
            // 'branch_name' => ['required'],
            // 'ifsc_code' => ['required'],
            // 'account_no' => ['required'],
        ]);

        $data = $request->except(['_token','_method']);
        $loginUser = Auth::user();

        $user = User::find($id);

        // Detect changes and prepare log message
        $logMessageParts = [];
        $originalUserData = $user->getOriginal();
        $originalAddressData = $user->address ? $user->address->getOriginal() : [];
        $originalBankData = $user->bankDetails ? $user->bankDetails->getOriginal() : [];

        $name = $data['salutatior'] ." ".$data['firstname']." ".$data['lastname'];

        $userdataArr = array();
        $userdataArr['salutatior'] = $data['salutatior'];
        $userdataArr['name'] = $name;
        $userdataArr['firstname'] = $data['firstname'];
        $userdataArr['lastname'] = $data['lastname'];
        $userdataArr['company_name'] = $data['company_name'];
        $userdataArr['display_name'] = $data['display_name'];
        $userdataArr['email'] = $data['email'];
        $userdataArr['work_phone'] = $data['work_phone'];
        $userdataArr['mobile'] = $data['mobile'];
        $userdataArr['website'] = $data['website'];
        $userdataArr['payment_terms'] = $data['payment_terms'];
        $userdataArr['gst_treatment'] = $data['gst_treatment'];
        $userdataArr['gst_number'] = $data['gst_number'];
        $userdataArr['place_of_supply'] = $data['place_of_supply'];
        $userdataArr['opening_balance'] = $data['opening_balance'];
        $userdataArr['pan_number'] = $data['pan_number'];
        $userdataArr['discount'] = $data['discount'];
        $userdataArr['credit_limit'] = $data['credit_limit'];
        $user->update($userdataArr);
        
        $addressArr = array();
        $addressArr['billing_attention'] = $data['billing_attention'];
        $addressArr['billing_street'] = $data['billing_street'];
        $addressArr['billing_city'] = $data['billing_city'];
        $addressArr['billing_state'] = $data['billing_state'];
        $addressArr['billing_pin_code'] = $data['billing_pin_code'];
        $addressArr['billing_country'] = $data['billing_country'];
        $addressArr['billing_gst_number'] = $data['billing_gst_number'];
        $addressArr['same_as'] = isset($data['same_as']) ? 1 : '';
        $addressArr['shipping_attention'] = $data['shipping_attention'];
        $addressArr['shipping_street'] = $data['shipping_street'];
        $addressArr['shipping_city'] = $data['shipping_city'];
        $addressArr['shipping_state'] = $data['shipping_state'];
        $addressArr['shipping_pin_code'] = $data['shipping_pin_code'];
        $addressArr['shipping_country'] = $data['shipping_country'];
        $addressArr['shipping_gst_number'] = $data['shipping_gst_number'];
        // Address::where('user_id',$id)->update($addressArr);
        Address::updateOrCreate(
            ['user_id' => $id],  // Condition to check if the record exists
            $addressArr      // Data to update or create
        );
        
        $bankDetailsArr = array();
        $bankDetailsArr['bank_name'] = $data['bank_name'];
        $bankDetailsArr['branch_name'] = $data['branch_name'];
        $bankDetailsArr['ifsc_code'] = $data['ifsc_code'];
        $bankDetailsArr['account_no'] = $data['account_no'];
        // BankDetails::where('user_id',$id)->update($bankDetailsArr);
        BankDetails::updateOrCreate(
            ['user_id' => $id],  // Condition to check if the record exists
            $bankDetailsArr      // Data to update or create
        );

        $changes = [
            'user_data' => [],
            'address_data' => [],
            'bank_data' => [],
        ];

        // Loop through user data changes
        foreach ($userdataArr as $field => $newValue) {
            if (isset($originalUserData[$field]) && $originalUserData[$field] !== $newValue) {
                $changes['user_data'][$field] = [
                    'old' => $originalUserData[$field],
                    'new' => $newValue,
                ];
            }
        }

        // Loop through address data changes
        foreach ($addressArr as $field => $newValue) {
            if (isset($originalAddressData[$field]) && $originalAddressData[$field] !== $newValue) {
                $changes['address_data'][$field] = [
                    'old' => $originalAddressData[$field],
                    'new' => $newValue,
                ];
            }
        }

        // Loop through bank data changes
        foreach ($bankDetailsArr as $field => $newValue) {
            if (isset($originalBankData[$field]) && $originalBankData[$field] !== $newValue) {
                $changes['bank_data'][$field] = [
                    'old' => $originalBankData[$field],
                    'new' => $newValue,
                ];
            }
        }

        // Construct the log message
        if (!empty($changes['user_data'])) {
            $logMessageParts[] = "User data updated: " . implode(", ", array_keys($changes['user_data']));
        }
        if (!empty($changes['address_data'])) {
            $logMessageParts[] = "Address data updated: " . implode(", ", array_keys($changes['address_data']));
        }
        if (!empty($changes['bank_data'])) {
            $logMessageParts[] = "Bank details updated: " . implode(", ", array_keys($changes['bank_data']));
        }

        // Prepare final log message
        $logMessage = "{$loginUser->name} updated customer data for Customer ID {$user->id}. " . implode("; ", $logMessageParts);

        if (!empty($logMessageParts)) {
            activity()
                ->performedOn($user)
                ->causedBy($loginUser)
                ->withProperties($changes)
                ->useLog('user-updates')
                ->log($logMessage);
        }

        // Redirect with success message
        return redirect()->route('users.index')
                ->withSuccess('User is updated successfully.'); 

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        // About if user is Super Admin or User ID belongs to Auth User
        // if ($user->hasRole('super-admin') || $user->id == auth()->user()->id)
        // {
        //     abort(403, 'USER DOES NOT HAVE THE RIGHT PERMISSIONS');
        // }

        $user = User::findOrFail($user->id); // Find the user by ID with userInfo

        // Log the deletion activity
        $loginUser = Auth::user();
        $logMessage = "{$loginUser->name} deleted Customer with ID {$user->id} (Name: {$user->name})";

        // Log the deletion in activity log
        activity()
            ->performedOn($user)
            ->causedBy($loginUser)
            ->log($logMessage);

        
        $user->syncRoles([]);
        $user->delete();

        return redirect()->route('users.index')->withSuccess('User is deleted successfully.');
    }

    public function getUserStatement($id){

        $customer = User::find($id);

        return view('admin.users.statement2', [
            'customer' => $customer,
        ]);
    }

    public function userList(Request $request){
        $columns = array( 
            0 =>'id', 
            1 =>'name',
            2 =>'email',
            3 => 'work_phone',
            4 =>'company_name',
            5 =>'company_name',
            6 =>'company_name',
            7 =>'status',
            8 =>'action'         
        );
        $totalData = User::where('user_type','customer')->count();      
              
        $totalFiltered = $totalData;
        $limit = $request->input('length');
        $start = $request->input('start');
        $order = $columns[$request->input('order.0.column')];
        $dir = $request->input('order.0.dir');
        
        $search = $request->input('search.value');
        if(empty($search))
        {   
            $institutes = User::where('user_type','customer')->where('status','!=' , 0)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }else{
            $institutes = User::where('user_type','customer')->where('status','!=' , 0)
                        ->offset($start)
                        ->limit($limit)
                        ->orderBy($order,$dir)
                        ->get();
        }


        $data = array();
        if(!empty($institutes))
        {
            $i = 1;
            foreach ($institutes as $key=>$institute){

                $nestedDataStatement = $institute->name;
                $nestedData['id'] = $i;

                if(auth()->user()->can('user-statement')){
                    $nestedDataStatement = '<a href="'.route('users.statement',$institute->id).'" id="userInfo" data-userid="'.$institute->id.'">'.$institute->name.'</i></a> &nbsp;';
                }

                $nestedData['name'] = $nestedDataStatement;
                $nestedData['email'] = $institute->email;
                $nestedData['work_phone'] = $institute->work_phone;
                $nestedData['company_name'] = $institute->company_name;

                $i++;
                
                // $nestedDataShow = '';
                // $nestedDataEdit = '';
                // $nestedDataDelete = '';

                // Define the dropdown action button
                $actions = '<div class="btn-group">
                            <i class="fas fa-ellipsis-v" data-toggle="dropdown"></i>
                            <div class="dropdown-menu action-dropdown" role="menu">';

                if (auth()->user()->can('user-view')) {
                    $actions .= '<a class="dropdown-item" href="'.route('users.show',$institute->id).'" id="userInfo" data-userid="'.$institute->id.'">View</a>';
                }

                if (auth()->user()->can('user-edit')) {
                    $actions .= '<a class="dropdown-item" href="'.route('users.edit', $institute->id).'">Edit</a>';
                }

                if (auth()->user()->can('user-delete')) {
                    $actions .= '
                        <form action="'.route('users.destroy', $institute->id).'" method="POST" class="deleteForm">
                            '.csrf_field().'
                            <input type="hidden" name="_method" value="DELETE">
                            <button type="submit" class="dropdown-item deleteButton">Delete</button>
                        </form>
                    ';
                }

                $actions .= '</ul></div>';

                $nestedData['action'] = $actions;

                // if (auth()->user()->can('user-view')) {
                //     $nestedDataShow = '<a href="javascript:;" class="mt-2 mb-2 " id="userInfo" data-userid="'.$institute->id.'"><i class="fas fa-ellipsis-v"></i></a> &nbsp;';
                //     // $nestedDataShow  = '<a href="#"><i class="fas fa-ellipsis-v"></i></a>';

                // } 
                // if (auth()->user()->can('user-view')) {

                //     // $nestedDataShow = '<a href="'.route('users.show',$institute->id).'" class="btn btn-info btn-sm mt-2 mb-2" id="userInfo" data-userid="'.$institute->id.'"><i class="fa fa-eye"></i></a> &nbsp;';
                // }          
                
                // if (auth()->user()->can('user-edit')) {

                //     $nestedDataEdit = '<a href="'.route('users.edit',$institute->id).'" class="btn btn-success btn-sm mt-2 mb-2"><i class="fa fa-edit"></i></a> &nbsp;';
                // }
                
                // if (auth()->user()->can('user-delete')) {

                //     $nestedDataDelete = '<form action="' . route('users.destroy', $institute->id) . '" method="POST" class="deleteForm d-inline">
                //                     ' . csrf_field() . '
                //                     <input type="hidden" name="_method" value="DELETE">
                //                     <button type="submit" class="btn btn-danger btn-sm mt-2 mb-2 deleteButton">
                //                         <i class="fa fa-trash"></i>
                //                     </button>
                //                 </form>
                //                 &nbsp;';
                // }


                // if($institute->status == 2){
                //     $nestedDataStatus = '<a href="#" class="btn btn-success btn-sm approveButton" data-action="approve" data-userid='.$institute->id.' >Approve</a> &nbsp;';

                // }else{
                //     $nestedDataStatus = '<a href="#" class="btn btn-danger btn-sm rejectButton" data-action="reject" data-userid='.$institute->id.'>Reject</a>';
                // }
               

                // $nestedData['action'] ="$nestedDataShow"."$nestedDataEdit"."$nestedDataDelete";
                
                $data[] = $nestedData;
            }
 
        }

        $json_data = array(
        "draw"            => intval($request->input('draw')),  
        "recordsTotal"    => intval($totalData),  
        "recordsFiltered" => intval($totalFiltered), 
        "data"            => $data
        );            

        echo json_encode($json_data);
    }

}
