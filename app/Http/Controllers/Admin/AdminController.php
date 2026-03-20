<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
    }

    public function edit(Request $request): View
    {
        return view('admin.profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'name' => 'required',
        ]);

        $loginId = Auth()->id();
        $userInput = [
            'name' => $request->name,
        ];

        User::where('id',$loginId)->update($userInput);

        return Redirect::route('admin.profile.edit')->withSuccess('profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);
        
        $loginId = Auth()->id();
        $userInput = [
            'password' => Hash::make($validatedData['password'])
        ];

        User::where('id',$loginId)->update($userInput);
        
        return Redirect::route('admin.profile.edit')->withSuccess('password-updated');
    }
}
