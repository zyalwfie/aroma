@extends('frontend.layouts.app')

@section('title', $product->name)

@section('content')
<!--================Detail Product Area =================-->
<div class="product_image_area">
    <div class="container">
        <div class="row s_product_inner">
            <div class="col-lg-6">
                <div class="owl-carousel owl-theme s_Product_carousel">
                    <div class="single-prd-item">
                        <img class="img-fluid"
                             src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                             alt="{{ $product->name }}">
                    </div>
                </div>
            </div>

            <div class="col-lg-5 offset-lg-1">
                <div class="s_product_text">
                    <h3>{{ $product->name }}</h3>
                    <h2>Rp {{ number_format($product->price, 0, ',', '.') }}</h2>
                    <ul class="list">
                        <li><span>Kategori</span>: {{ $product->category->name ?? '-' }}</li>
                        <li><span>Status</span>: {{ $product->stock > 0 ? 'Tersedia' : 'Stok Habis' }}</li>
                    </ul>

                    <div class="product_count mt-4">
                        <label for="quantity" class="mb-2 d-block">Jumlah:</label>

                        @auth
                        <form action="{{ route('cart.add') }}" method="POST" class="d-flex flex-wrap align-items-center gap-2">
                            @csrf
                            <div class="input-group" style="max-width: 140px;">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">-</button>
                                </div>
                                <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control text-center">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>

                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="button primary-btn">Add to Cart</button>
                        </form>
                        @else
                        <div class="d-flex align-items-center gap-2">
                            <div class="input-group" style="max-width: 140px;">
                                <div class="input-group-prepend">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">-</button>
                                </div>
                                <input type="number" id="quantity" name="quantity" min="1" value="1" class="form-control text-center" disabled>
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>

                            <!-- Tombol akan memunculkan modal -->
                            <button type="button" class="button primary-btn" data-toggle="modal" data-target="#guestModal">
                                Add to Cart
                            </button>
                        </div>
                        @endauth
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<!--================End Detail Product Area =================-->

<!-- Modal Guest -->
<div class="modal fade" id="guestModal" tabindex="-1" role="dialog" aria-labelledby="guestModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 class="modal-title">Login atau Register</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <p class="mb-3">Sudah punya akun?</p>
        <div class="d-flex justify-content-center gap-3">
          <a href="{{ route('login') }}" class="btn btn-outline-primary">Ya</a>
          <a href="{{ route('register') }}" class="btn btn-outline-success">Belum</a>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
    function changeQuantity(amount) {
        const qtyInput = document.getElementById('quantity');
        let currentQty = parseInt(qtyInput.value);
        if (!isNaN(currentQty)) {
            currentQty += amount;
            if (currentQty < 1) currentQty = 1;
            qtyInput.value = currentQty;
        }
    }
</script>


<!--================Product Description Area =================-->
<section class="product_description_area">
    <div class="container">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="desc-tab" data-toggle="tab" href="#desc" role="tab" aria-controls="desc" aria-selected="true">Description</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="review-tab" data-toggle="tab" href="#review" role="tab" aria-controls="review" aria-selected="false">Reviews</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            {{-- Description Tab --}}
            <div class="tab-pane fade show active" id="desc" role="tabpanel" aria-labelledby="desc-tab">
                <p>{{ $product->description }}</p>
            </div>

            {{-- Reviews Tab --}}
            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                {{-- Dummy Reviews bisa kamu ganti nanti --}}
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="review_item">
                            <h4>Blake Ruiz</h4>
                            <div>
                                <i class="fa fa-star"></i><i class="fa fa-star"></i>
                                <i class="fa fa-star"></i><i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                            <p>Produk ini sangat berkualitas. Saya suka!</p>
                        </div>
                    </div>
                    <div class="product_reviews mt-5">
  <h4>Ulasan Produk</h4>
  @forelse($product->reviews as $review)
    <div class="border p-2 mb-2">
      <strong>{{ $review->user->name }}</strong> - ⭐ {{ $review->rating }}/5
      <p>{{ $review->comment }}</p>
    </div>
  @empty
    <p>Belum ada ulasan.</p>
  @endforelse
</div>

                    <div class="col-md-6">
                        <form class="form-review">
                            <div class="form-group">
                                <input type="text" class="form-control" name="name" placeholder="Your Name">
                            </div>
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email">
                            </div>
                            <div class="form-group">
                                <textarea class="form-control" name="message" rows="4" placeholder="Your Review"></textarea>
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-primary">Submit Review</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!--================End Product Description Area =================-->

@endsection
