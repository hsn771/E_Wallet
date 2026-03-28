@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
    <!-- Glow Effect -->
    <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/20 blur-3xl rounded-full"></div>
    
    <div class="text-center mb-8 relative z-10">
        <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-teal-400 mb-2">Welcome Back</h1>
        <p class="text-slate-400">Sign in to manage your finances</p>
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

    <form method="POST" action="{{ route('login.post') }}" class="space-y-6 relative z-10">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-slate-300">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-500 transition-all outline-none"
                placeholder="you@example.com">
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="text-sm font-medium text-slate-300">Password</label>
            <input id="password" type="password" name="password" required
                class="w-full bg-slate-900/50 border border-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-500 transition-all outline-none"
                placeholder="••••••••">
        </div>

        <button type="submit" 
            class="w-full bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-400 hover:to-indigo-500 text-white font-semibold rounded-xl px-4 py-3 shadow-[0_0_15px_rgba(99,102,241,0.5)] hover:shadow-[0_0_25px_rgba(99,102,241,0.6)] transition-all cursor-pointer">
            Sign In
        </button>

        <div class="text-center text-sm text-slate-400 pt-4">
            Don't have an account? 
            <a href="{{ route('register') }}" class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">Create one</a>
        </div>
    </form>
</div>
@endsection
