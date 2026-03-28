<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Wallet;
use App\Models\Category;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard')->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'currency' => ['required', 'string', 'size:3'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'currency' => $request->currency,
        ]);

        // Create a default wallet for the new user
        Wallet::create([
            'user_id' => $user->id,
            'name' => 'Main Wallet',
            'balance' => 0,
            'type' => 'personal',
            'is_default' => true,
        ]);

        // Create some default categories
        $defaultCategories = [
            ['name' => 'Salary', 'type' => 'income', 'icon' => '💵', 'color' => '#10b981'],
            ['name' => 'Food', 'type' => 'expense', 'icon' => '🍔', 'color' => '#f59e0b'],
            ['name' => 'Transportation', 'type' => 'expense', 'icon' => '🚗', 'color' => '#6366f1'],
            ['name' => 'Utilities', 'type' => 'expense', 'icon' => '💡', 'color' => '#f43f5e'],
        ];

        foreach ($defaultCategories as $category) {
            Category::create(array_merge($category, ['user_id' => $user->id]));
        }

        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Account created successfully!');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
