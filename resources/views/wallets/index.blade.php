@extends('layouts.app')

@section('title', 'My Wallets')
@section('header', 'Wallets')
@section('header_actions')
    <button onclick="document.getElementById('addWalletModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2">
        <span>➕</span> New Wallet
    </button>
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($wallets as $wallet)
        <div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 rounded-3xl p-6 shadow-xl relative overflow-hidden group hover:border-indigo-500/50 transition-all">
            @if($wallet->is_default)
                <div class="absolute top-0 right-0 bg-indigo-500 text-xs px-3 py-1 rounded-bl-xl font-medium">Default</div>
            @endif
            
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $wallet->name }}</h3>
                    <p class="text-xs text-slate-400 uppercase tracking-wider">{{ $wallet->type }}</p>
                </div>
                <div class="text-3xl bg-slate-700/50 p-3 rounded-2xl group-hover:bg-indigo-500/20 transition-colors">
                    {{ $wallet->type === 'savings' ? '🏦' : ($wallet->type === 'business' ? '💼' : '👛') }}
                </div>
            </div>
            
            <div class="mt-6 mb-8">
                <p class="text-sm text-slate-400 mb-1">Available Balance</p>
                <h2 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-400 to-teal-400">
                    {{ number_format($wallet->balance, 0) }} <span class="text-sm font-medium text-slate-500">{{ auth()->user()->currency }}</span>
                </h2>
            </div>
            
            <div class="flex gap-2 border-t border-slate-700/50 pt-4 mt-auto">
                <button onclick="document.getElementById('addBalanceModal_{{ $wallet->id }}').classList.remove('hidden')" class="flex-1 bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-400 rounded-xl py-2 text-sm font-medium transition cursor-pointer">
                    + Add Funds
                </button>
            </div>
        </div>

        <!-- Add Balance Modal -->
        <div id="addBalanceModal_{{ $wallet->id }}" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
            <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-white">Add Funds to {{ $wallet->name }}</h3>
                    <button onclick="document.getElementById('addBalanceModal_{{ $wallet->id }}').classList.add('hidden')" class="text-slate-400 hover:text-white cursor-pointer">&times;</button>
                </div>
                <form action="{{ route('wallets.addBalance', $wallet) }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Amount ({{ auth()->user()->currency }})</label>
                            <input type="number" step="0.01" name="amount" required class="w-full bg-slate-900/50 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        </div>
                    </div>
                    <div class="mt-8 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('addBalanceModal_{{ $wallet->id }}').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 cursor-pointer">Cancel</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium cursor-pointer">Confirm Addition</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>

<!-- Add Wallet Modal -->
<div id="addWalletModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl relative">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/20 blur-3xl rounded-full pointer-events-none"></div>
        <div class="flex justify-between items-center mb-6 relative z-10">
            <h3 class="text-xl font-bold text-white">Create New Wallet</h3>
            <button onclick="document.getElementById('addWalletModal').classList.add('hidden')" class="text-slate-400 hover:text-white cursor-pointer text-2xl leading-none">&times;</button>
        </div>
        <form action="{{ route('wallets.store') }}" method="POST" class="relative z-10">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Wallet Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-500" placeholder="e.g. Dream Vacation Trip">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                    <select name="type" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        <option value="personal">Personal</option>
                        <option value="savings">Savings</option>
                        <option value="business">Business</option>
                    </select>
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addWalletModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 cursor-pointer transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-lg hover:shadow-indigo-500/25 transition cursor-pointer">Create Wallet</button>
            </div>
        </form>
    </div>
</div>
@endsection
