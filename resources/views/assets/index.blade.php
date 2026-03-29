@extends('layouts.app')

@section('title', 'Assets')
@section('header', 'My Assets')

@section('header_actions')
    <button onclick="document.getElementById('addAssetModal').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-lg shadow-emerald-500/25">
        <span>📈</span> Add Asset
    </button>
@endsection

@section('content')
<div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 rounded-3xl p-6 shadow-xl mb-6">
    <div class="flex items-center gap-4">
        <div class="w-16 h-16 rounded-2xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl">🏦</div>
        <div>
            <p class="text-slate-400 font-medium">Total Assets Value</p>
            <h2 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">
                {{ number_format($assets->sum('value'), 0) }} <span class="text-lg text-slate-500">{{ auth()->user()->currency }}</span>
            </h2>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($assets as $asset)
        <div class="bg-slate-800 border border-slate-700 rounded-2xl p-6 shadow-lg group hover:border-emerald-500/50 transition">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $asset->name }}</h3>
                    <div class="flex items-center gap-2 mt-0.5">
                        <p class="text-xs text-slate-400 uppercase tracking-wider">{{ str_replace('_', ' ', $asset->type) }}</p>
                        <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                        <p class="text-xs text-slate-500">{{ $asset->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
                <form action="{{ route('assets.destroy', $asset) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="opacity-0 group-hover:opacity-100 p-1 text-slate-500 hover:text-red-400 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
            </div>
            
            <div class="mt-4">
                <h2 class="text-2xl font-bold text-emerald-400">
                    {{ number_format($asset->value, 0) }}
                </h2>
            </div>
        </div>
    @empty
        <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-10 opacity-50">
            <div class="text-5xl mb-4">🏛️</div>
            <p class="text-slate-400">No assets recorded yet. Start building your portfolio!</p>
        </div>
    @endforelse
</div>

<!-- Add Asset Modal -->
<div id="addAssetModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-md p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-xl font-bold text-white">Add Asset</h3>
            <button onclick="document.getElementById('addAssetModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl">&times;</button>
        </div>
        <form action="{{ route('assets.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Name</label>
                    <input type="text" name="name" required class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white placeholder-slate-600" placeholder="e.g. City Bank Savings">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Type</label>
                    <select name="type" required class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white">
                        <option value="cash">Cash</option>
                        <option value="bank">Bank Balance</option>
                        <option value="property">Property</option>
                        <option value="investment">Investment</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-1">Value ({{ auth()->user()->currency }})</label>
                    <input type="number" step="0.01" name="value" required class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-500 rounded-xl px-4 py-3 text-white">
                </div>
            </div>
            <div class="mt-8 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('addAssetModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-medium">Save Asset</button>
            </div>
        </form>
    </div>
</div>
@endsection
