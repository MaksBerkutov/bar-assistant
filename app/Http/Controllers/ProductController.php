<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = \App\Models\Product::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('barcode', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $products = $query->get()->map(function($product) {
            $product->photo_url = $product->photo ? asset('storage/' . $product->photo) : null;
            return $product;
        });

        return view('products.operator', [
            'products' => $products,
            'cart' => session()->get('cart', []),
            'search' => $request->search,
            'selectedType' => $request->type
        ]);
    }

    public function create(){
        return view('products.create');
    }
    public function addToCart(Request $request)
    {
        $product = \App\Models\Product::findOrFail($request->product_id);

        $cart = session()->get('cart', []);

        $cart[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'comment' => $request->comment ?? null,
            'seat_number' => $request->seat_number ?? null,
            'type' => $product->type,
        ];

        session()->put('cart', $cart);

        return redirect()->route('products.index', ['type' => $request->category]);
    }
    public function clearCart() {
        session()->forget('cart');
        return redirect()->back();
    }
    public function removeFromCart($index)
    {
        $cart = session()->get('cart', []);
        unset($cart[$index]);
        session()->put('cart', array_values($cart)); // переиндексируем
        return back();
    }

    public function createOrder() {
        // Здесь логика создания заказа из сессии
        $cart = session('cart', []);
        // Сохраняешь заказ, очищаешь корзину
        session()->forget('cart');
        return redirect()->back()->with('success', 'Заказ создан');
    }
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'barcode' => 'string|nullable',
            'price' => 'required|numeric',
            'type' => 'required|string',
            'stock_quantity' => 'numeric|nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('resources/products', 'public');
            $data['photo'] = $photoPath; // сохраняем относительный путь
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Товар успешно добавлен');
    }



}
