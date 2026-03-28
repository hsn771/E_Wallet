<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function index()
    {
        $wallets = Wallet::where('user_id', Auth::id())->get();
        return view('wallets.index', compact('wallets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:personal,savings,business',
        ]);

        Wallet::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'balance' => 0,
            'type' => $request->type,
            'is_default' => Wallet::where('user_id', Auth::id())->count() === 0,
        ]);

        return redirect()->route('wallets.index')->with('success', 'Wallet created successfully.');
    }

    public function update(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:personal,savings,business',
        ]);

        $wallet->update([
            'name' => $request->name,
            'type' => $request->type,
        ]);

        return redirect()->route('wallets.index')->with('success', 'Wallet updated successfully.');
    }

    public function destroy(Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) abort(403);
        if ($wallet->is_default) return back()->with('error', 'Cannot delete default wallet.');

        $wallet->delete();
        return redirect()->route('wallets.index')->with('success', 'Wallet deleted successfully.');
    }

    public function addBalance(Request $request, Wallet $wallet)
    {
        if ($wallet->user_id !== Auth::id()) abort(403);

        $request->validate([
            'amount' => 'required|numeric|min:0.01',
        ]);

        $wallet->balance += $request->amount;
        $wallet->save();

        // Create a transaction record for this
        $wallet->transactions()->create([
            'user_id' => Auth::id(),
            'type' => 'income',
            'amount' => $request->amount,
            'description' => 'Manual balance top-up',
            'date' => now(),
        ]);

        return redirect()->route('wallets.index')->with('success', 'Balance added successfully.');
    }
}
