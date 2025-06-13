<div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-3">
    @foreach($products as $product)
        <div class="col">
            <div class="card h-100">
                <img src="{{ $product->photo ? asset('storage/' . $product->photo) : 'https://png.pngtree.com/png-vector/20221125/ourmid/pngtree-no-image-available-icon-flatvector-illustration-pic-design-profile-vector-png-image_40966566.jpg' }}"
                     class="card-img-top" style="object-fit: contain; height: 140px; background-color: #f8f9fa;">

                <div class="card-body">
                    <h5 class="card-title">{{ $product->name }}</h5>
                    <p class="card-text">Цена: {{ $product->price }} грн</p>
                    <form method="POST" action="{{ route('products.addToCart') }}">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="category" value="{{ $selectedType }}">
                        @if ($product->type == 'culinary')
                            <input type="text" name="comment" class="form-control mb-2" placeholder="Комментарий">
                        @endif
                        <button type="submit" class="btn btn-sm btn-success w-100">Добавить</button>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
</div>
