@extends('frontend.layouts.app')

@section('content')
    <!-- ================ Collection section start ================= -->
    <section class="section-margin--small mb-5">
      <!-- NAV CATEGORY -->
      @include('frontend.layouts.navcategory', ['categories' => $categories])
      <!-- END NAV CATEGORY -->

      <!-- FILTER BY PRICE -->
      @include('frontend.layouts.filterprice')
      <!-- END FILTER BY PRICE -->
        </div>
          <div class="col-xl-9 col-lg-8 col-md-7">
            <!-- Start Best Seller -->
            <section class="lattest-product-area pb-40 category-list">
              <div class="row">
              @foreach ($products as $product)
              <div class="col-md-6 col-lg-4">
                <div class="card text-center card-product">
                  <div class="card-product__img">
                    <img class="card-img" src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" />
                    <ul class="card-product__imgOverlay">
                      </li>
                        <form action="{{ route('cart.add') }}" method="POST">
                          @csrf
                          <input type="hidden" name="product_id" value="{{ $product->id }}">
                          <input type="hidden" name="quantity" value="1">
                          <button type="submit"><i class="ti-shopping-cart"></i></button>
                      </form>
                    </ul>
                  </div>
                  <div class="card-body">
                    <p>{{ $product->category->name ?? 'Uncategorized' }}</p>
                    <h4 class="card-product__title">
                    <a href="{{ route('product.detail', $product->slug) }}">{{ $product->name }}</a></h4>
                    <p class="card-product__price">Rp {{ number_format($product->price, 2) }}</p>
                  </div>
                </div>
              </div>
              @endforeach
              </div>
            </section>
            <!-- End Best Seller -->
          </div>
        </div>
      </div>
    </section>
    <!-- ================ Collection section end ================= -->
@endsection