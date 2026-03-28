<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::where('user_id', Auth::id())->get();
        return view('assets.index', compact('assets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:cash,bank,property,investment,other',
            'value' => 'required|numeric|min:0',
        ]);

        Asset::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'description' => $request->description,
        ]);

        return redirect()->route('assets.index')->with('success', 'Asset recorded successfully.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->user_id !== Auth::id()) abort(403);
        
        $asset->delete();
        return redirect()->route('assets.index')->with('success', 'Asset removed successfully.');
    }
}
