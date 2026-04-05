<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Asset;
use App\Models\Liability;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // 1. Total Balance from Wallets
        $totalBalance = Wallet::where('user_id', $user->id)->sum('balance');

        // 2. Monthly Income vs Expense
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $monthlyIncome = Transaction::where('user_id', $user->id)
            ->where('type', 'income')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        $monthlyExpense = Transaction::where('user_id', $user->id)
            ->where('type', 'expense')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->sum('amount');

        // 3. Assets & Liabilities for Net Worth
        $totalAssets = Asset::where('user_id', $user->id)->sum('value');
        $totalLiabilities = Liability::where('user_id', $user->id)->sum('amount');
        
        $netWorth = $totalAssets - $totalLiabilities;

        // Total lifetime income & expense for Net Balance calculation
        $totalAllIncome = Transaction::where('user_id', $user->id)->where('type', 'income')->sum('amount');
        $totalAllExpense = Transaction::where('user_id', $user->id)->where('type', 'expense')->sum('amount');
        
        // Fetch Main Wallet explicitly
        $mainWalletBalance = Wallet::where('user_id', $user->id)->where('is_default', true)->value('balance') ?? 0;

        // Net Balance = (main wallet + total assets)
        $netBalance = $mainWalletBalance + $totalAssets;

        // 4. Fetch lists for breakdown
        $assets = Asset::where('user_id', $user->id)->get();
        $liabilities = Liability::where('user_id', $user->id)->get();

        // 5. Recent Transactions
        $recentTransactions = Transaction::with(['category', 'wallet', 'asset'])
            ->where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        // 6. Chart Data (Last 6 Months Income vs Expense)
        $chartData = $this->getChartData($user->id);

        return view('dashboard.index', compact(
            'totalBalance', 
            'monthlyIncome', 
            'monthlyExpense', 
            'netWorth',
            'netBalance',
            'totalAssets',
            'totalLiabilities',
            'recentTransactions',
            'chartData',
            'mainWalletBalance',
            'totalAllIncome',
            'totalAllExpense',
            'assets',
            'liabilities'
        ));
    }

    private function getChartData($userId)
    {
        $months = [];
        $incomes = [];
        $expenses = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $months[] = $date->format('M Y');

            $incomes[] = Transaction::where('user_id', $userId)
                ->where('type', 'income')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');

            $expenses[] = Transaction::where('user_id', $userId)
                ->where('type', 'expense')
                ->whereMonth('date', $date->month)
                ->whereYear('date', $date->year)
                ->sum('amount');
        }

        return [
            'labels' => $months,
            'income' => $incomes,
            'expense' => $expenses,
        ];
    }
}
