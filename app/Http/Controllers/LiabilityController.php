<?php

namespace App\Http\Controllers;

use App\Models\Liability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LiabilityController extends Controller
{
    public function index()
    {
        $liabilities = Liability::where('user_id', Auth::id())->get();
        return view('liabilities.index', compact('liabilities'));
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

    public function destroy(Liability $liability)
    {
        if ($liability->user_id !== Auth::id()) abort(403);
        
        $liability->delete();
        return redirect()->route('liabilities.index')->with('success', 'Liability removed successfully.');
    }
}
