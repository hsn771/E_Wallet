@extends('layouts.app')

@section('title', 'Transaction History')
@section('header', 'Virtual Passbook')
@section('header_actions')
    <button onclick="document.getElementById('addTransactionModal').classList.remove('hidden')" class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-lg hover:shadow-indigo-500/25">
        <span>🧾</span> Record Transaction
    </button>
@endsection

@section('content')
<div class="bg-slate-800/50 backdrop-blur-md border border-slate-700 rounded-3xl p-6 shadow-xl relative">
    
    <!-- Filters & Actions -->
    <div class="flex flex-wrap items-center justify-between gap-4 mb-8">
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('transactions.index', ['month' => $selectedMonth, 'year' => $selectedYear]) }}" class="px-4 py-2 rounded-xl border {{ !request('type') ? 'bg-indigo-500/20 border-indigo-500 text-indigo-400' : 'border-slate-700 text-slate-400 hover:bg-slate-700' }}">All</a>
            <a href="{{ route('transactions.index', ['type' => 'income', 'month' => $selectedMonth, 'year' => $selectedYear]) }}" class="px-4 py-2 rounded-xl border {{ request('type') == 'income' ? 'bg-emerald-500/20 border-emerald-500 text-emerald-400' : 'border-slate-700 text-slate-400 hover:bg-slate-700' }}">Income</a>
            <a href="{{ route('transactions.index', ['type' => 'expense', 'month' => $selectedMonth, 'year' => $selectedYear]) }}" class="px-4 py-2 rounded-xl border {{ request('type') == 'expense' ? 'bg-rose-500/20 border-rose-500 text-rose-400' : 'border-slate-700 text-slate-400 hover:bg-slate-700' }}">Expense</a>
        </div>

        <form action="{{ route('transactions.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            @if(request('type'))
                <input type="hidden" name="type" value="{{ request('type') }}">
            @endif
            
            <div class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-slate-300 rounded-xl px-3 py-2 focus:border-indigo-500 outline-none">
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endfor
                </select>

                <select name="year" onchange="this.form.submit()" class="bg-slate-900 border border-slate-700 text-slate-300 rounded-xl px-3 py-2 focus:border-indigo-500 outline-none">
                    @php $startYear = date('Y') - 5; @endphp
                    @for($y = $startYear; $y <= date('Y') + 1; $y++)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <a href="{{ route('transactions.downloadPdf', request()->all()) }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-xl transition flex items-center gap-2 shadow-lg hover:shadow-emerald-500/25">
                <span>📄</span> Print PDF
            </a>
        </form>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="border-b border-slate-700 text-slate-400 text-sm tracking-wide">
                    <th class="pb-3 px-4 font-medium h-12">Date</th>
                    <th class="pb-3 px-4 font-medium h-12">Details</th>
                    <th class="pb-3 px-4 font-medium h-12">Wallet</th>
                    <th class="pb-3 px-4 font-medium text-right h-12">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700/50">
                @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-700/30 transition-colors group">
                        <td class="py-4 px-4 text-slate-300 whitespace-nowrap">
                            {{ $tx->date->format('M d, Y') }}
                        </td>
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg
                                    {{ $tx->type == 'income' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                    {{ $tx->category ? $tx->category->icon : ($tx->type == 'income' ? '💵' : '💸') }}
                                </div>
                                <div>
                                    <p class="text-white font-medium">{{ $tx->category ? $tx->category->name : 'Uncategorized' }}</p>
                                    @if($tx->description)
                                        <p class="text-xs text-slate-500 mt-0.5 truncate max-w-[200px]">{{ $tx->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-500/10 text-indigo-400 border border-indigo-500/20">
                                {{ $tx->wallet->name }}
                            </span>
                        </td>
                        <td class="py-4 px-4 text-right">
                            <span class="font-bold text-lg {{ $tx->type == 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $tx->type == 'income' ? '+' : '-' }}{{ number_format($tx->amount, 0) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-slate-500">
                            <div class="text-5xl mb-4 opacity-30">📭</div>
                            <p>No transactions found for this period.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($transactions->hasPages())
        <div class="mt-8 border-t border-slate-700 pt-6">
            {{ $transactions->links() }}
        </div>
    @endif
</div>

<!-- Add Transaction Modal -->
<div id="addTransactionModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-800 border border-slate-700 rounded-3xl w-full max-w-lg p-6 relative shadow-2xl overflow-y-auto max-h-[90vh]">
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-500/20 blur-3xl rounded-full pointer-events-none"></div>
        
        <div class="flex justify-between items-center mb-6 relative z-10">
            <h3 class="text-2xl font-bold text-white">Record Transaction</h3>
            <button onclick="document.getElementById('addTransactionModal').classList.add('hidden')" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
        </div>

        <form action="{{ route('transactions.store') }}" method="POST" class="relative z-10">
            @csrf
            
            <div class="grid grid-cols-2 gap-4 mb-4">
                {{-- Type Selection Toggle --}}
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Type</label>
                    <div class="flex bg-slate-900 rounded-xl p-1 border border-slate-700" x-data="{ type: 'expense' }">
                        <label class="flex-1 text-center py-2 rounded-lg cursor-pointer transition"
                               :class="type === 'expense' ? 'bg-rose-500 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                               @click="type = 'expense'">
                            <input type="radio" name="type" value="expense" class="hidden" checked>
                            Expense
                        </label>
                        <label class="flex-1 text-center py-2 rounded-lg cursor-pointer transition"
                               :class="type === 'income' ? 'bg-emerald-500 text-white shadow-md' : 'text-slate-400 hover:text-white'"
                               @click="type = 'income'">
                            <input type="radio" name="type" value="income" class="hidden">
                            Income
                        </label>
                    </div>
                </div>

                <div class="col-span-1">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Amount</label>
                    <input type="number" step="0.01" name="amount" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                </div>
                
                <div class="col-span-1">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Date</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white [color-scheme:dark]">
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Wallet</label>
                    <select name="wallet_id" required class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}">{{ $wallet->name }} (Balance: {{ number_format($wallet->balance, 0) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Category</label>
                    <select name="category_id" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white">
                        <option value="">Select Category...</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->icon }} {{ $category->name }} ({{ ucfirst($category->type) }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-1">Description/Note</label>
                    <input type="text" name="description" class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-500 rounded-xl px-4 py-3 text-white placeholder-slate-600" placeholder="Optional details...">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                <button type="button" onclick="document.getElementById('addTransactionModal').classList.add('hidden')" class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-700 cursor-pointer transition">Cancel</button>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-medium shadow-lg hover:shadow-indigo-500/25 transition flex items-center gap-2 cursor-pointer">
                    Save Transaction
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
