<?php

namespace App\Http\Controllers;

use App\Models\CashWithdrawal;
use App\Models\Client;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Корзина пуста.');
        }

        $request->validate([
            'payment_type' => 'required|in:cash,card,debt,mixed',
            'phone' => 'required_if:payment_type,debt|string|nullable',
            'name' => 'string|nullable',
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
        ]);


        DB::beginTransaction();

        try {
            $total = collect($cart)->sum(function ($item) {
                return $item['price'] * ($item['quantity'] ?? 1);
            });

            // Обработка смешанных оплат
            $cash = null;
            $card = null;
            $client = null;
            if ($request->payment_type === 'mixed') {
                $cash = $request->cash_amount ?? 0;
                $card = $request->card_amount ?? 0;

                if ($cash + $card !== $total) {
                    return redirect()->back()->with('error', 'Сумма наличных и карты должна соответствовать общей сумме.');
                }
            }
            else if( $request->payment_type==='debt'){
                $client = Client::firstOrCreate(
                    ['phone' => $request['phone']],
                    ['name' => $request['name'] ?? '']
                );
            }
            $order = Order::create([
                'total_price' => $total,
                'payment_type' => $request->payment_type,
                'phone' => $client == null? null:$client->phone,
                'client_id' => $client == null? null:$client->id,
                'cash_amount' => $cash,
                'card_amount' => $card,
                'user_id' => auth()->id(),

            ]);

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'] ?? 1,
                    'price' => $item['price'],
                    'comment' => $item['comment'] ?? null,
                    'culinary_status' => $item['type'] === 'culinary' ? 'new' : null,
                    'seat_number' => $item['seat_number'] ?? null,
                ]);

                $product = Product::find($item['product_id']);

                if ($product->type === 'inventory' && $product->stock_quantity !== null) {
                    $product->stock_quantity -= ($item['quantity'] ?? 1);
                    $product->save();
                }
            }

            DB::commit();
            session()->forget('cart');

            return redirect()->back()->with('success', 'Заказ успешно создан!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при создании заказа: ' . $e->getMessage());
        }
    }

    public function details(Order $order)
    {

        $order->load('items.product');
        $order->load(['items.product', 'client']);

        return response()->json($order);
    }

    public function report(Request $request)
    {
        $date = $request->input('date') ?? now()->format('Y-m-d');

        $orders = Order::whereDate('created_at', $date)->with('items.product')->get();

        $total = $orders->sum('total_price');
        $totalCash = $orders->where('payment_type', 'cash')->sum('total_price');
        $totalCard = $orders->where('payment_type', 'card')->sum('total_price');
        $totalMixedCash = $orders->where('payment_type', 'mixed')->sum('cash_amount');
        $totalMixedCard = $orders->where('payment_type', 'mixed')->sum('card_amount');
        $totalDebt = $orders->where('payment_type', 'debt')->sum('total_price');
        $cashWithdrawnSum = CashWithdrawal::whereDate('created_at', today())->sum('amount');
        $cashWithdrawn = CashWithdrawal::whereDate('created_at', today())->get();

        return view('orders.report', compact(
            'orders', 'date', 'total', 'totalCash', 'totalCard',
            'totalMixedCash', 'totalMixedCard', 'totalDebt','cashWithdrawnSum','cashWithdrawn'
        ));
    }

    public function debtorsToday()
    {
        $date = now()->format('Y-m-d');
        $debts = Order::where('payment_type', 'debt')
            ->whereDate('created_at', $date)
            ->with('items.product')
            ->get();

        return view('orders.debtors', compact('debts', 'date'));
    }

    public function payDebtForm(Order $order)
    {
        return view('orders.pay_debt', compact('order'));
    }

    public function payDebt(Request $request, Order $order)
    {
        $request->validate([
            'cash_amount' => 'nullable|numeric|min:0',
            'card_amount' => 'nullable|numeric|min:0',
        ]);

        $total = $order->total_price;
        $cash = $request->cash_amount ?? 0;
        $card = $request->card_amount ?? 0;

        if ($cash + $card != $total) {
            return redirect()->back()->with('error', 'Сумма оплаты должна соответствовать общей сумме заказа.');
        }
        if($cash == 0){
            $order->update([
                'payment_type' => 'card',
                'total_price' => $card,
            ]);
        }
        else if($card == 0){
            $order->update([
                'payment_type' => 'cash',
                'total_price' => $cash,
            ]);
        }
        else{
            $order->update([
                'payment_type' => 'mixed',
                'cash_amount' => $cash,
                'card_amount' => $card,
            ]);
        }


        return redirect()->route('orders.debtors')->with('success', 'Долг погашен!');
    }
    public function index(Request $request)
    {
        $query = Order::with(['client', 'user', 'items.product']);

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $orders = $query->orderByDesc('created_at')->paginate(20);
        if ($search = $request->input('search')) {
            $query->where('id', $search)
                ->orWhere('phone', 'like', "%$search%");
        }

        $orders = $query->paginate(20);

        return view('orders.index', compact('orders', 'search'));
    }

    public function destroy(\App\Models\Order $order)
    {
        $order->items()->delete();
        $order->delete();

        return redirect()->route('orders.index')->with('success', 'Заказ удалён');
    }

    public function edit(Order $order)
    {
        $order->load('items.product');
        $products = Product::all();

        return view('orders.edit', compact('order', 'products'));
    }

    public function update(Request $request, Order $order)
    {
        DB::transaction(function () use ($request, $order) {
            // Обновляем общие поля заказа
            $order->update([
                'payment_type' => $request->payment_type,
                'cash_amount' => $request->cash_amount,
                'card_amount' => $request->card_amount,
            ]);

            // Обновляем существующие позиции
            foreach ($request->items ?? [] as $itemData) {
                if (!empty($itemData['delete'])) {
                    OrderItem::where('id', $itemData['id'])->delete();
                    continue;
                }

                OrderItem::where('id', $itemData['id'])->update([
                    'product_id' => $itemData['product_id'],
                    'price' => $itemData['price'],
                    'quantity' => $itemData['quantity'],
                    'comment' => $itemData['comment'],
                ]);
            }

            // Добавление новой позиции
            if (!empty($request->new_item['product_id'])) {
                $order->items()->create([
                    'product_id' => $request->new_item['product_id'],
                    'price' => $request->new_item['price'],
                    'quantity' => $request->new_item['quantity'],
                    'comment' => $request->new_item['comment'],
                ]);
            }

            // Пересчёт общей суммы
            $total = $order->items()->sum(DB::raw('price * quantity'));
            $order->update(['total_price' => $total]);
        });

        return redirect()->route('orders.edit', $order)->with('success', 'Заказ обновлён');
    }
}
