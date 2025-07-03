<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                ->orWhere('barcode', 'like', '%' . $request->search . '%');
        }

        $products = $query->orderBy('name')->paginate(20);

        return view('products.index', compact('products'));
    }

    public function edit(Product $product)
    {
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:100',
            'type' => 'required|string',
            'purchase_price' => 'nullable|numeric',
            'stock_quantity' => 'nullable|integer',
            'price' => 'required|numeric',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('photo')) {
            // Удалим старое фото если есть
            if ($product->photo) {
                Storage::delete($product->photo);
            }
            $validated['photo'] = $request->file('photo')->store('products');
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Товар обновлён');
    }
    public function operator(Request $request)
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

        $userAgent = $request->header('User-Agent');
        $isMobile = preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $userAgent);

        return view(
            $isMobile ? 'products.mobile-operator' : 'products.operator',
            [
                'products' => $products,
                'cart' => session()->get('cart', []),
                'search' => $request->search,
                'selectedType' => $request->type,
            ]
        );
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

        return redirect()->route('products.operator', ['type' => $request->category]);
    }
    public function clearCart() {
        session()->forget('cart');
        return redirect()->back();
    }
    public function removeFromCart($index)
    {
        $cart = session()->get('cart', []);
        unset($cart[$index]);
        session()->put('cart', array_values($cart));
        return back();
    }

    public function createOrder() {
        $cart = session('cart', []);
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
            'purchase_price' => 'numeric|nullable',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('resources/products', 'public');
            $data['photo'] = $photoPath;
        }

        Product::create($data);

        return redirect()->route('products.index')->with('success', 'Товар успешно добавлен');
    }



}
