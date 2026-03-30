<?php

namespace App\Http\Controllers;

use App\Models\Liability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiabilityController extends Controller
{
    public function index()
    {
        $liabilities = Liability::where('user_id', Auth::id())->with('transactions')->get();
        $wallets = \App\Models\Wallet::where('user_id', Auth::id())->get();
        $assets = \App\Models\Asset::where('user_id', Auth::id())->get();
        return view('liabilities.index', compact('liabilities', 'wallets', 'assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:loan,credit_due,borrowed,other',
            'amount' => 'required|numeric|min:0',
        ]);

        Liability::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('liabilities.index')->with('success', 'Liability recorded successfully.');
    }

    public function pay(Request $request, Liability $liability)
    {
        if ($liability->user_id !== Auth::id()) abort(403);

        $request->validate([
            'payment_source' => 'required|string',
            'amount' => 'required|numeric|min:0.01|max:' . $liability->amount,
        ]);

        $parts = explode('_', $request->payment_source);
        $type = $parts[0];
        $id = $parts[1];

        if ($type === 'wallet') {
            $wallet = \App\Models\Wallet::findOrFail($id);
            if ($wallet->user_id !== Auth::id()) abort(403);
            if ($wallet->balance < $request->amount) return back()->with('error', 'Insufficient balance in wallet.');
            $wallet->balance -= $request->amount;
            $wallet->save();
            $walletId = $wallet->id;
            $sourceName = "Wallet: " . $wallet->name;
        } else {
            $asset = \App\Models\Asset::findOrFail($id);
            if ($asset->user_id !== Auth::id()) abort(403);
            if ($asset->value < $request->amount) return back()->with('error', 'Insufficient asset value.');
            $asset->value -= $request->amount;
            $asset->save();
            $walletId = null;
            $sourceName = "Asset: " . $asset->name;
        }

        $liability->amount -= $request->amount;
        $liability->save();

        \App\Models\Transaction::create([
            'user_id' => Auth::id(),
            'wallet_id' => $walletId,
            'liability_id' => $liability->id,
            'type' => 'expense',
            'amount' => $request->amount,
            'date' => now(),
            'description' => "Repayment for " . $liability->name . " from " . $sourceName,
        ]);

        return redirect()->route('liabilities.index')->with('success', 'Liability reduced successfully.');
    }

    public function update(Request $request, Liability $liability)
    {
        if ($liability->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:loan,credit_due,borrowed,other',
            'amount' => 'required|numeric|min:0',
        ]);

        $liability->update([
            'name' => $request->name,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
        ]);

        return redirect()->route('liabilities.index')->with('success', 'Liability updated successfully.');
    }

    public function destroy(Liability $liability)
    {
        if ($liability->user_id !== Auth::id()) abort(403);
        
        $liability->delete();
        return redirect()->route('liabilities.index')->with('success', 'Liability removed successfully.');
    }
}
