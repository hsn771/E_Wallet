@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-6">

    <!-- Top Stats Row -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-6">

        <!-- Net Balance -->
        <div class="bg-gradient-to-br from-teal-500 to-teal-700 rounded-2xl p-6 shadow-lg shadow-teal-500/30 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-30 text-6xl transform group-hover:scale-110 transition-transform">⚖️</div>
            <p class="text-teal-100 font-medium mb-1 relative z-10">Net Balance</p>
            <h3 class="text-3xl font-bold relative z-10 truncate">{{ number_format($netBalance, 2) }} <span class="text-lg font-medium">{{ auth()->user()->currency }}</span></h3>
            <div class="mt-4 relative z-10 text-xs text-teal-200 opacity-80 uppercase tracking-widest">(M.Wallet + Net + In) - Out</div>
        </div>
        
        <!-- Total Balance -->
        <div class="bg-gradient-to-br from-indigo-500 to-indigo-700 rounded-2xl p-6 shadow-lg shadow-indigo-500/30 text-white relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-50 text-6xl transform group-hover:scale-110 transition-transform">💰</div>
            <p class="text-indigo-100 font-medium mb-1 relative z-10">Total Balance</p>
            <h3 class="text-3xl font-bold relative z-10">{{ number_format($totalBalance, 2) }} <span class="text-lg">{{ auth()->user()->currency }}</span></h3>
            <div class="mt-4 relative z-10">
                <a href="{{ route('wallets.index') }}" class="text-sm border border-indigo-400/50 rounded-lg px-3 py-1 hover:bg-white/10 transition">View Wallets</a>
            </div>
        </div>

        <!-- Net Worth -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-2xl p-6 shadow-lg relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl transform group-hover:scale-110 transition-transform text-white">💎</div>
            <p class="text-slate-400 font-medium mb-1 relative z-10">Net Worth</p>
            <h3 class="text-3xl font-bold text-white relative z-10">{{ number_format($netWorth, 2) }} <span class="text-lg text-slate-500">{{ auth()->user()->currency }}</span></h3>
            <div class="mt-4 relative z-10 flex gap-2">
                <span class="text-xs text-emerald-400 bg-emerald-400/10 px-2 py-1 rounded">A: {{ number_format($totalAssets, 0) }}</span>
                <span class="text-xs text-rose-400 bg-rose-400/10 px-2 py-1 rounded">L: {{ number_format($totalLiabilities, 0) }}</span>
            </div>
        </div>

        <!-- Monthly Income -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-2xl p-6 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl transform group-hover:scale-110 transition-transform text-emerald-500">📈</div>
            <p class="text-slate-400 font-medium mb-1 relative z-10">Monthly Income</p>
            <h3 class="text-3xl font-bold text-emerald-400 relative z-10">+{{ number_format($monthlyIncome, 2) }}</h3>
            <div class="mt-4 relative z-10 truncate text-xs text-slate-500">This Month</div>
        </div>

        <!-- Monthly Expense -->
        <div class="bg-gradient-to-br from-slate-800 to-slate-900 border border-slate-700 rounded-2xl p-6 shadow-lg relative overflow-hidden group hover:border-rose-500/50 transition-colors">
            <div class="absolute top-0 right-0 p-4 opacity-10 text-6xl transform group-hover:scale-110 transition-transform text-rose-500">📉</div>
            <p class="text-slate-400 font-medium mb-1 relative z-10">Monthly Expense</p>
            <h3 class="text-3xl font-bold text-rose-400 relative z-10">-{{ number_format($monthlyExpense, 2) }}</h3>
            <div class="mt-4 relative z-10 truncate text-xs text-slate-500">This Month</div>
        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Chart Section -->
        <div class="col-span-1 lg:col-span-2 bg-slate-800/50 border border-slate-700 rounded-3xl p-6 shadow-xl relative backdrop-blur-md">
            <h3 class="text-xl font-bold text-white mb-6">Income vs Expense (6 Months)</h3>
            <div class="relative h-72 w-full">
                <canvas id="financialChart"></canvas>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-span-1 bg-slate-800/50 border border-slate-700 rounded-3xl p-6 shadow-xl relative backdrop-blur-md flex flex-col">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-white">Recent Activity</h3>
                <a href="{{ route('transactions.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">View All</a>
            </div>
            
            <div class="flex-1 overflow-y-auto pr-2 space-y-4">
                @forelse($recentTransactions as $tx)
                    <div class="flex items-center justify-between p-3 rounded-xl hover:bg-slate-700/50 transition border border-transparent hover:border-slate-600">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl shadow-inner
                                {{ $tx->type == 'income' ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                {{ $tx->category ? $tx->category->icon : ($tx->type == 'income' ? '💵' : '💸') }}
                            </div>
                            <div>
                                <p class="text-slate-200 font-medium">{{ $tx->category ? $tx->category->name : 'Uncategorized' }}</p>
                                <p class="text-xs text-slate-500">{{ $tx->date->format('M d, Y') }} &bull; {{ $tx->wallet->name }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $tx->type == 'income' ? 'text-emerald-400' : 'text-rose-400' }}">
                                {{ $tx->type == 'income' ? '+' : '-' }}{{ number_format($tx->amount, 2) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-slate-500 py-10 flex flex-col items-center">
                        <span class="text-4xl mb-3 opacity-50">📭</span>
                        <p>No recent transactions.</p>
                        <a href="{{ route('transactions.create') }}" class="mt-2 text-indigo-400 border border-indigo-400/30 px-3 py-1 rounded hover:bg-indigo-400/10 text-sm transition">Add One</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        
        // Define colors
        const incomeColor = 'rgba(16, 185, 129, 0.8)';
        const incomeBorder = 'rgb(16, 185, 129)';
        const expenseColor = 'rgba(244, 63, 94, 0.8)';
        const expenseBorder = 'rgb(244, 63, 94)';
        
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($chartData['labels']) !!},
                datasets: [
                    {
                        label: 'Income',
                        data: {!! json_encode($chartData['income']) !!},
                        backgroundColor: incomeColor,
                        borderColor: incomeBorder,
                        borderWidth: 1,
                        borderRadius: 6,
                    },
                    {
                        label: 'Expense',
                        data: {!! json_encode($chartData['expense']) !!},
                        backgroundColor: expenseColor,
                        borderColor: expenseBorder,
                        borderWidth: 1,
                        borderRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: '#94a3b8',
                            font: {
                                family: "'Inter', sans-serif"
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(51, 65, 85, 0.5)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: 'rgba(51, 65, 85, 0.2)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b'
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(51, 65, 85, 0.2)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            callback: function(value) {
                                return value >= 1000 ? (value/1000) + 'k' : value;
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    });
</script>
@endsection
