<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{

    public function login()
    {

        if (Auth::check()) {
            return redirect()->route('home');
        } else {
            return view('Auth.login');
        }
    }
    public function customLogin(Request $request)
    {
        $request->validate([
            'phone' => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('phone', 'password');

        if (Auth::attempt($credentials)) {
            return redirect()->route('home')
                ->with('success', 'Signed in');
        }
        return redirect()->back()->with('error', 'Phone number or password is incorrect');
    }
    public function register()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        } else {
            return view('dashboard.Auth.register');
        }
    }
    public function customRegistration(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'role' => 'provider',
            'password' => Hash::make($request->password)
        ]);
        auth()->login($user);

        return redirect()->route('home')->withSuccess('You have signed-in');
    }

    public function signOut()
    {
        Session::flush();
        Auth::logout();
        return Redirect('login');
    }

    public function profile($userId)
    {
        $user = User::find($userId);
        return view('Auth.profile', compact('user'));
    }
    public function update(Request $request, $userId)
    {
        $user = User::find($userId);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
            'sallary' => $request->sallary,
            'password' => $request->password,
        ]);

        // Find the UserInfo instance associated with the user
        // $userinfo = UserInfo::where('user_id', $userId)->first();

        // If UserInfo instance doesn't exist, create it
        // if (!$userinfo) {
        //     $userinfo = new UserInfo();
        //     $userinfo->user_id = $userId;
        // }

        // Handle image upload
        if ($request->hasFile('image')) {
            $image = $request->file('image')->store('user_images', 'public');
        } else {
            $image = null;
        }

        // $userinfo->update([
        //     'gender' => $request->gender,
        //     'number_residence' => $request->number_residence,
        //     'age' => $request->age,
        //     'date_runer' => $request->date_runer,
        //     'nationality' => $request->nationality,
        //     'marital_status' => $request->marital_status,
        //     'expire_residence' => $request->expire_residence,
        //     'image' => $image,
        // ]);

        return redirect()->route('getEmployee')->with('success', 'تم تحديث البيانات بنجاح');
    }


}
