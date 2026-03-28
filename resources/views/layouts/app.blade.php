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
<body class="bg-slate-900 text-slate-100 font-sans antialiased overflow-x-hidden min-h-screen flex" x-data="{ sidebarOpen: false, passwordModalOpen: {{ $errors->has('current_password') || $errors->has('password') ? 'true' : 'false' }}, dropdownOpen: false }" @open-password-modal.window="passwordModalOpen = true">
    
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
            <div class="flex items-center space-x-3 relative" x-data="{ openMenu: false }">
                <span class="text-sm font-medium text-slate-300">{{ auth()->user()->name }}</span>
                
                <button @click="openMenu = !openMenu" type="button" class="text-slate-400 hover:text-white transition p-1.5 rounded-full hover:bg-slate-800 focus:outline-none">
                    <svg class="w-5 h-5 pointer-events-none" fill="currentColor" viewBox="0 0 24 24"><path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/></svg>
                </button>

                <!-- Dropdown Menu -->
                <div x-show="openMenu" @click.outside="openMenu = false"
                     x-transition:enter="transition ease-out duration-100" 
                     x-transition:enter-start="transform opacity-0 scale-95" 
                     x-transition:enter-end="transform opacity-100 scale-100" 
                     x-transition:leave="transition ease-in duration-75" 
                     x-transition:leave-start="transform opacity-100 scale-100" 
                     x-transition:leave-end="transform opacity-0 scale-95" 
                     class="absolute right-0 bg-slate-800 border border-slate-700 rounded-xl shadow-2xl z-50 py-1.5 ring-1 ring-black ring-opacity-5"
                     style="display: none; min-width: 13rem; top: 100%; margin-top: 0.75rem; transform-origin: top right;">
                    
                    <button @click="$dispatch('open-password-modal'); openMenu = false" type="button" class="w-full text-left px-4 py-2.5 text-sm text-slate-300 hover:bg-slate-700/50 hover:text-white transition flex items-center space-x-3 whitespace-nowrap group">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.1rem; height: 1.1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path></svg>
                        <span class="font-medium">Change Password</span>
                    </button>
                    
                    <form method="POST" action="{{ route('logout') }}" class="block w-full mt-1 border-t border-slate-700/50 pt-1">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-slate-300 hover:bg-rose-500/10 hover:text-rose-400 transition flex items-center space-x-3 whitespace-nowrap group">
                            <svg class="w-4 h-4 text-slate-400 group-hover:text-rose-400 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width: 1.1rem; height: 1.1rem;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                            <span class="font-medium">Logout</span>
                        </button>
                    </form>
                </div>
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
    <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 lg:hidden" style="display: none;"></div>

    <!-- Password Change Modal -->
    <div x-show="passwordModalOpen" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="passwordModalOpen = false" class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Change Password</h3>
                <button @click="passwordModalOpen = false" class="text-slate-400 hover:text-white text-2xl">&times;</button>
            </div>
            
            <form action="{{ route('password.update') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Current Password</label>
                        <input type="password" name="current_password" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        @error('current_password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">New Password</label>
                        <input type="password" name="password" required minlength="8" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        @error('password') <span class="text-red-400 text-xs mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required minlength="8" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                    </div>
                </div>
                <div class="mt-8 flex justify-end gap-3">
                    <button type="button" @click="passwordModalOpen = false" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 transition">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-lg shadow-indigo-500/30 transition">Update Password</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
