@extends('layouts.guest')

@section('title', 'Register')

@section('content')
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
    <!-- Glow Effect -->
    <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-teal-500/20 blur-3xl rounded-full"></div>
    
    <div class="text-center mb-8 relative z-10">
        <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-teal-400 to-indigo-400 mb-2">Create Account</h1>
        <p class="text-slate-400">Start your financial journey today</p>
    </div>

    @if ($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" class="space-y-5 relative z-10">
        @csrf

        <!-- Name -->
        <div class="space-y-1">
            <label for="name" class="text-sm font-medium text-slate-300">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 transition-all outline-none"
                placeholder="John Doe">
        </div>

        <!-- Email Address -->
        <div class="space-y-1">
            <label for="email" class="text-sm font-medium text-slate-300">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 transition-all outline-none"
                placeholder="you@example.com">
        </div>

        <!-- Currency -->
        <div class="space-y-1">
            <label for="currency" class="text-sm font-medium text-slate-300">Preferred Currency</label>
            <select id="currency" name="currency" required
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl px-4 py-2.5 text-white outline-none">
                <option value="BDT">Bangladeshi Taka (BDT)</option>
                <option value="USD">US Dollar (USD)</option>
                <option value="EUR">Euro (EUR)</option>
                <option value="GBP">British Pound (GBP)</option>
                <option value="INR">Indian Rupee (INR)</option>
            </select>
        </div>

        <!-- Password -->
        <div class="space-y-1">
            <label for="password" class="text-sm font-medium text-slate-300">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 transition-all outline-none"
                placeholder="••••••••">
        </div>

        <!-- Confirm Password -->
        <div class="space-y-1">
            <label for="password_confirmation" class="text-sm font-medium text-slate-300">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-teal-500 focus:ring-1 focus:ring-teal-500 rounded-xl px-4 py-2.5 text-white placeholder-slate-500 transition-all outline-none"
                placeholder="••••••••">
        </div>

        <button type="submit" 
            class="w-full mt-2 bg-gradient-to-r from-teal-500 to-teal-600 hover:from-teal-400 hover:to-teal-500 text-white font-semibold rounded-xl px-4 py-3 shadow-[0_0_15px_rgba(20,184,166,0.5)] hover:shadow-[0_0_25px_rgba(20,184,166,0.6)] transition-all cursor-pointer">
            Create Account
        </button>

        <div class="text-center text-sm text-slate-400 pt-2">
            Already have an account? 
            <a href="{{ route('login') }}" class="text-teal-400 hover:text-teal-300 font-medium transition-colors">Sign in</a>
        </div>
    </form>
</div>
@endsection
