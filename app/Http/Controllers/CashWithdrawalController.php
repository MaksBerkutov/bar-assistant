<?php

namespace App\Http\Controllers;

use App\Models\CashWithdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CashWithdrawalController extends Controller
{
    public function index()
    {
        $withdrawals = CashWithdrawal::latest()->paginate(20);
        return view('withdrawals.index', compact('withdrawals'));
    }

    public function create()
    {
        return view('withdrawals.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'comment' => 'nullable|string|max:255',
        ]);

        CashWithdrawal::create([
            'amount' => $validated['amount'],
            'comment' => $validated['comment'],
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('withdrawals.index')->with('success', 'Снятие наличных сохранено');
    }
}
