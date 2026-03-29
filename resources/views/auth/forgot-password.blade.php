@extends('layouts.guest')

@section('title', 'Forgot Password')

@section('content')
    <div
        class="bg-slate-800/50 backdrop-blur-xl border border-slate-700 p-8 rounded-3xl shadow-2xl relative overflow-hidden">
        <!-- Glow Effect -->
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/20 blur-3xl rounded-full"></div>

        <div class="text-center mb-8 relative z-10">
            <h1
                class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-teal-400 mb-2">
                Reset Password</h1>
            <p class="text-slate-400">Enter your email to receive a reset link</p>
        </div>

        @if (session('status'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-6 relative z-10">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-slate-300">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                    class="w-full bg-slate-900/50 border border-slate-700 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-500 transition-all outline-none"
                    placeholder="you@example.com">
            </div>

            <button type="submit"
                class="w-full bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-400 hover:to-indigo-500 text-white font-semibold rounded-xl px-4 py-3 shadow-[0_0_15px_rgba(99,102,241,0.5)] hover:shadow-[0_0_25px_rgba(99,102,241,0.6)] transition-all cursor-pointer">
                Send Link
            </button>

            <div class="text-center text-sm text-slate-400 pt-4">
                Remember your password?
                <a href="{{ route('login') }}"
                    class="text-indigo-400 hover:text-indigo-300 font-medium transition-colors">Sign in</a>
            </div>
        </form>
    </div>
@endsection
