<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use Illuminate\Http\Request;

class KitchenController extends Controller
{
    public function index(Request $request)
    {
        $showCompleted = $request->get('show_completed') === '1';

        $items = OrderItem::whereHas('product', fn($q) => $q->where('type', 'culinary'))
            ->when(!$showCompleted, fn($q) => $q->where('culinary_status', '!=', 'Completed'))
            ->with('product', 'order')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('kitchen.index', compact('items', 'showCompleted'));
    }

    public function updateStatus(OrderItem $item, Request $request)
    {
        $item->update(['culinary_status' => $request->status]);
        return redirect()->back()->with('success', 'Статус обновлен.');
    }

}
