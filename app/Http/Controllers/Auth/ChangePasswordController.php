<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ChangePasswordController extends Controller
{
    public function index()
    {
        return view('auth.passwords.change');
    }

    public function update(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);

        $user = Auth::user();
        $currentPassword = $request->input('current_password');
        $newPassword = $request->input('new_password');

        if (Hash::check($currentPassword, $user->password)) {
            $user->password = Hash::make($newPassword);
            $user->save();

            session()->flash('status', ['type' => 'primary', 'message' => 'Password successfully changed!']);
            return redirect()->back();
        }

        return redirect()->back()->withErrors(['current_password' => 'The password you entered does not match your password on file']);
    }
}
