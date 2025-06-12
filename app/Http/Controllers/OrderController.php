<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function store(Request $request){
        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Корзина пуста.');
        }

        $request->validate([
            'payment_type' => 'required|in:cash,card',
        ]);
        DB::beginTransaction();

        try {
            // Подсчёт общей суммы
            $total = collect($cart)->sum(function ($item) {
                return $item['price'] ;
            });

            // Создание заказа
            $order = Order::create([
                'total_price' => $total,
                'payment_type' => $request->payment_type,
            ]);

            // Добавление товаров

            foreach ($cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['product_id'],
                    'price' => $item['price'],
                    'comment' => $item['comment'] ?? null,
                ]);
                $product = Product::find($item['product_id']);

                // Снижение количества на складе, если товар учитывается
                if ($product->type === 'inventory' && $product->stock_quantity !== null) {
                    $product->stock_quantity -= 1;
                    $product->save();
                }
            }
            DB::commit();

            // Очистка корзины
            session()->forget('cart');

            return redirect()->back()->with('success', 'Заказ успешно создан!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            DB::rollBack();
            return redirect()->back()->with('error', 'Ошибка при создании заказа: ' . $e->getMessage());
        }
    }
    public function details(Order $order)
    {
        $order->load('items.product');
        return response()->json($order);
    }

    public function report(Request $request)
    {
        $date = $request->input('date') ?? now()->format('Y-m-d');

        $orders = Order::whereDate('created_at', $date)->with('items.product')->get();

        $total = $orders->sum('total_price');
        $totalCash = $orders->where('payment_type', 'cash')->sum('total_price');
        $totalCard = $orders->where('payment_type', 'card')->sum('total_price');

        return view('orders.report', compact('orders', 'date', 'total', 'totalCash', 'totalCard'));
    }
}
