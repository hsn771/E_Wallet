<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::where('user_id', Auth::id())->get();
        $wallets = Wallet::where('user_id', Auth::id())->get();
        $bankAssets = Asset::where('user_id', Auth::id())->where('type', 'bank')->get();
        return view('assets.index', compact('assets', 'wallets', 'bankAssets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,property,investment,accounts_receivable,other',
            'value' => 'required|numeric|min:0',
            'source_type' => 'nullable|string|in:wallet,asset',
            'source_id' => 'nullable|integer',
        ]);

        \DB::transaction(function () use ($request) {
            $value = $request->value;

            if ($request->type === 'accounts_receivable' && $request->source_type && $request->source_id) {
                if ($request->source_type === 'wallet') {
                    $source = Wallet::where('id', $request->source_id)->where('user_id', Auth::id())->first();
                    if (!$source || $source->balance < $value) {
                         throw new \Exception('Insufficient wallet balance.');
                    }
                    $source->decrement('balance', $value);
                } else if ($request->source_type === 'asset') {
                    $source = Asset::where('id', $request->source_id)->where('user_id', Auth::id())->first();
                    if (!$source || $source->value < $value) {
                         throw new \Exception('Insufficient asset value.');
                    }
                    $source->decrement('value', $value);
                }
            }

            Asset::create([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'type' => $request->type,
                'value' => $request->value,
                'source_type' => $request->type === 'accounts_receivable' ? $request->source_type : null,
                'source_id' => $request->type === 'accounts_receivable' ? $request->source_id : null,
                'description' => $request->description,
            ]);
        });

        return redirect()->route('assets.index')->with('success', 'Asset recorded successfully.');
    }

    public function update(Request $request, Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,property,investment,accounts_receivable,other',
            'value' => 'required|numeric|min:0',
            'source_type' => 'nullable|string|in:wallet,asset',
            'source_id' => 'nullable|integer',
        ]);

        \DB::transaction(function () use ($request, $asset) {
            $newValue = $request->value;
            $oldValue = $asset->value;

            // Handle balance adjustments if it was/is accounts_receivable
            if ($asset->type === 'accounts_receivable' && $asset->source_type && $asset->source_id) {
                // Return old value to source
                if ($asset->source_type === 'wallet') {
                    Wallet::where('id', $asset->source_id)->increment('balance', $oldValue);
                } else if ($asset->source_type === 'asset') {
                    Asset::where('id', $asset->source_id)->increment('value', $oldValue);
                }
            }

            if ($request->type === 'accounts_receivable' && $request->source_type && $request->source_id) {
                // Deduct new value from new source
                if ($request->source_type === 'wallet') {
                    $source = Wallet::where('id', $request->source_id)->where('user_id', Auth::id())->first();
                    if (!$source || $source->balance < $newValue) {
                         throw new \Exception('Insufficient wallet balance.');
                    }
                    $source->decrement('balance', $newValue);
                } else if ($request->source_type === 'asset') {
                    $source = Asset::where('id', $request->source_id)->where('user_id', Auth::id())->first();
                    if (!$source || $source->value < $newValue) {
                         throw new \Exception('Insufficient asset value.');
                    }
                    $source->decrement('value', $newValue);
                }
            }

            $asset->update([
                'name' => $request->name,
                'type' => $request->type,
                'value' => $request->value,
                'source_type' => $request->type === 'accounts_receivable' ? $request->source_type : null,
                'source_id' => $request->type === 'accounts_receivable' ? $request->source_id : null,
                'description' => $request->description,
            ]);
        });

        return redirect()->route('assets.index')->with('success', 'Asset updated successfully.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) abort(403);
        
        \DB::transaction(function () use ($asset) {
            if ($asset->type === 'accounts_receivable' && $asset->source_type && $asset->source_id) {
                if ($asset->source_type === 'wallet') {
                    Wallet::where('id', $asset->source_id)->increment('balance', $asset->value);
                } else if ($asset->source_type === 'asset') {
                    Asset::where('id', $asset->source_id)->increment('value', $asset->value);
                }
            }
            $asset->delete();
        });

        return redirect()->route('assets.index')->with('success', 'Asset removed successfully.');
    }
}
