<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'E-Wallet') }} - @yield('title')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased overflow-x-hidden min-h-screen flex" x-data="{ sidebarOpen: false }">
    
    <!-- Background Decorators -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0">
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-900/20 to-transparent"></div>
    </div>

    <!-- Sidebar Wrapper -->
    <div :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 border-r border-slate-800 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:block">
        <div class="flex items-center justify-center h-16 border-b border-slate-800">
            <span class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-indigo-500 to-teal-400">E-Wallet</span>
        </div>
        <nav class="p-4 space-y-2">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-indigo-400 transition-colors {{ request()->routeIs('dashboard') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300' }}">Dashboard</a>
            <a href="{{ route('wallets.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-indigo-400 transition-colors {{ request()->routeIs('wallets.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300' }}">Wallets</a>
            <a href="{{ route('transactions.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-indigo-400 transition-colors {{ request()->routeIs('transactions.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300' }}">Transactions</a>
            <a href="{{ route('assets.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-indigo-400 transition-colors {{ request()->routeIs('assets.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300' }}">Assets</a>
            <a href="{{ route('liabilities.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-indigo-400 transition-colors {{ request()->routeIs('liabilities.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300' }}">Liabilities</a>
            <a href="{{ route('categories.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 hover:text-indigo-400 transition-colors {{ request()->routeIs('categories.*') ? 'bg-slate-800 text-indigo-400' : 'text-slate-300' }}">Categories</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1 flex flex-col min-h-screen content-wrapper z-10 w-full">
        <!-- Header View -->
        <header class="h-16 flex items-center justify-between px-6 border-b border-slate-800 bg-slate-900/50 backdrop-blur-md sticky top-0 z-30">
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-slate-300 hover:text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <div class="flex-1"></div>
            <div class="flex items-center space-x-4">
                <span class="text-sm font-medium text-slate-300">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-red-500/20 hover:text-red-400 border border-slate-700 transition">Logout</button>
                </form>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 md:p-8">
            <div class="max-w-6xl mx-auto">
                <div class="flex justify-between items-center mb-8">
                    <h1 class="text-3xl font-bold text-white">@yield('header')</h1>
                    @yield('header_actions')
                </div>
                
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ session('error') }}
                    </div>
                @endif
                
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Overlay for mobile sidebar -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden"></div>
</body>
</html>
