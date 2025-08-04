<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // Create this view
    }

    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = [
            'username' => $request->name,
            'password' => $request->password,
        ];

        // Attempt login
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/people'); // Redirect after login
        }

        // Login failed
        return back()->withErrors([
            'name' => 'Invalid credentials.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
