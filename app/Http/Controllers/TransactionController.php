<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['wallet', 'category'])
            ->where('user_id', Auth::id())
            ->orderBy('date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->has('type') && in_array($request->type, ['income', 'expense', 'transfer'])) {
            $query->where('type', $request->type);
        }

        $transactions = $query->paginate(15);
        $wallets = Wallet::where('user_id', Auth::id())->get();
        $categories = Category::where('user_id', Auth::id())->orWhereNull('user_id')->get();

        return view('transactions.index', compact('transactions', 'wallets', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:income,expense,transfer',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date',
            'category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string|max:255',
        ]);

        $wallet = Wallet::findOrFail($request->wallet_id);
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

        Transaction::create([
            'user_id' => Auth::id(),
            'wallet_id' => $request->wallet_id,
            'category_id' => $request->category_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'date' => $request->date,
            'description' => $request->description,
        ]);

        return redirect()->route('transactions.index')->with('success', 'Transaction recorded successfully.');
    }
}
