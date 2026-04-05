<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Category;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['wallet', 'asset', 'category'])
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('type') && in_array($request->type, ['income', 'expense', 'transfer'])) {
            $query->where('type', $request->type);
        }

        // Monthly Filter
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        
        $query->whereMonth('date', $selectedMonth)
              ->whereYear('date', $selectedYear);

        $transactions = $query->paginate(15);
        $wallets = Wallet::where('user_id', Auth::id())->get();
        $assets = Asset::where('user_id', Auth::id())->get();
        $categories = Category::where('user_id', Auth::id())->orWhereNull('user_id')->get();

        return view('transactions.index', compact('transactions', 'wallets', 'assets', 'categories', 'selectedMonth', 'selectedYear'));
    }

    public function downloadPdf(Request $request)
    {
        $selectedMonth = $request->get('month', now()->month);
        $selectedYear = $request->get('year', now()->year);
        $type = $request->get('type');

        $query = Transaction::with(['wallet', 'asset', 'category'])
            ->where('user_id', Auth::id())
            ->whereMonth('date', $selectedMonth)
            ->whereYear('date', $selectedYear)
            ->orderBy('date', 'asc');

        if ($type && in_array($type, ['income', 'expense', 'transfer'])) {
            $query->where('type', $type);
        }

        $transactions = $query->get();

        // Calculate Totals
        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        $data = [
            'transactions' => $transactions,
            'monthName' => date('F', mktime(0, 0, 0, $selectedMonth, 1)),
            'year' => $selectedYear,
            'totalIncome' => $totalIncome,
            'totalExpense' => $totalExpense,
            'netBalance' => $netBalance,
            'user' => Auth::user(),
        ];

        $pdf = Pdf::loadView('transactions.pdf', $data);
        return $pdf->download("Transactions_{$data['monthName']}_{$selectedYear}.pdf");
    }

    public function store(Request $request)
    {
        $request->validate([
            'account' => 'required|string',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
        ]);

        list($accountType, $accountId) = explode(':', $request->account);

        $walletId = null;
        $assetId = null;

        if ($accountType === 'wallet') {
            $wallet = Wallet::findOrFail($accountId);
            if ($wallet->user_id !== Auth::id()) abort(403);

            if ($request->type === 'expense') {
                if ($wallet->balance < $request->amount) {
                    return back()->with('error', 'Insufficient balance in selected wallet.');
                }
                $wallet->balance -= $request->amount;
            } elseif ($request->type === 'income') {
                $wallet->balance += $request->amount;
            }
            $wallet->save();
            $walletId = $wallet->id;
        } elseif ($accountType === 'asset') {
            $asset = Asset::findOrFail($accountId);
            if ($asset->user_id !== Auth::id()) abort(403);

            if ($request->type === 'expense') {
                if ($asset->value < $request->amount) {
                    return back()->with('error', 'Insufficient balance in selected asset.');
                }
                $asset->value -= $request->amount;
            } elseif ($request->type === 'income') {
                $asset->value += $request->amount;
            }
            $asset->save();
            $assetId = $asset->id;
        }

        Transaction::create([
            'user_id' => Auth::id(),
            'wallet_id' => $walletId,
            'asset_id' => $assetId,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }
}
