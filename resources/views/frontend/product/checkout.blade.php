@extends('frontend.layouts.app')

@section('title', 'Checkout')

@section('content')

<!--================Checkout Area =================-->
<section class="checkout_area section-margin--small">
  <div class="container">
    <form action="{{ route('checkout.process') }}" method="POST">
      @csrf
      <div class="row">
        <!-- ADDRESS SELECTION -->
        <div class="col-12 mb-4">
          <h3>Pilih Alamat Pengiriman</h3>
          <div class="d-flex flex-wrap gap-3">
            @foreach($user->addresses as $address)
              <label class="btn btn-outline-dark">
                <input type="radio" name="address_id" value="{{ $address->id }}" required hidden>
                <!-- <input type="radio" name="selected_address_id" value="{{ $address->id }}" required hidden> -->
                <div>{{ $address->label }}<br><small>{{ $address->address }}</small></div>
              </label>
            @endforeach
            <a href="{{ route('account.index') }}" class="btn btn-outline-primary">Tambah Alamat</a>
          </div>
        </div>

        <!-- ORDER DETAILS -->
        <div class="col-lg-8">
          <h3>Detail Pesanan</h3>
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Produk</th>
                <th>Harga / pcs</th>
                <th>Qty</th>
                <th>Subtotal</th>
              </tr>
            </thead>
            <tbody>
              @php $total = 0; @endphp
              @foreach ($cartItems as $item)
                @php
                  $lineTotal = $item->product->price * $item->quantity;
                  $total += $lineTotal;
                @endphp
                <tr>
                  <td class="d-flex gap-2 align-items-center">
                    <img src="{{ asset('storage/products/' . $item->product->image) }}" width="60">
                    <div>{{ $item->product->name }}</div>
                  </td>
                  <td>Rp {{ number_format($item->product->price, 0, ',', '.') }}</td>
                  <td>{{ $item->quantity }}</td>
                  <td>Rp {{ number_format($lineTotal, 0, ',', '.') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>

          <!-- SHIPPING & PAYMENT -->
          <div class="form-group mt-4">
            <label for="shipping_method">Pilih Pengiriman</label>
            <select class="form-control" name="shipping_method" required>
              <option value="jne">JNE (Rp 20.000)</option>
              <option value="pos">POS (Rp 25.000)</option>
              <option value="tiki">TIKI (Rp 18.000)</option>
            </select>
          </div>

          <div class="form-group mt-3">
            <label for="payment_method">Metode Pembayaran</label>
            <select class="form-control" name="payment_method" required>
              <option value="midtrans">Midtrans Payment</option>
              <option value="cod">Bayar di Tempat (COD)</option>
            </select>
          </div>
        </div>

        <!-- RINGKASAN PEMBAYARAN -->
        <div class="col-lg-4">
          <div class="order_box p-4 border">
            <h4>Ringkasan</h4>
            <ul class="list-unstyled">
              <li class="d-flex justify-content-between">
                <span>Subtotal Pesanan</span>
                <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
              </li>
              <li class="d-flex justify-content-between">
                <span>Ongkir</span>
                <strong id="shipping-cost">Rp 20.000</strong>
              </li>
              <li class="d-flex justify-content-between mt-2 border-top pt-2">
                <span>Total</span>
                <strong id="total-cost">Rp {{ number_format($total + 20000, 0, ',', '.') }}</strong>
              </li>
            </ul>

            <div class="form-check mt-3">
              <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
              <label class="form-check-label" for="terms">
                Saya menyetujui <a href="#">syarat & ketentuan*</a>
              </label>
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary btn-block">Bayar Sekarang</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</section>
<!--================End Checkout Area =================-->

@push('scripts')
<script>
  const shippingDropdown = document.querySelector('[name="shipping_method"]');
  const shippingCost = document.getElementById('shipping-cost');
  const totalCost = document.getElementById('total-cost');
  const subtotal = {{ $total }};

  function updateTotal() {
    const selected = shippingDropdown.value;
    let cost = 20000;
    if (selected === 'pos') cost = 25000;
    if (selected === 'tiki') cost = 18000;

    shippingCost.innerText = 'Rp ' + cost.toLocaleString('id-ID');
    totalCost.innerText = 'Rp ' + (subtotal + cost).toLocaleString('id-ID');
  }
  if (window.location.href.includes('payment_status')) {
      const status = new URLSearchParams(window.location.search).get('payment_status');
      if (status === 'success') {
          window.location.href = "{{ route('payment.finish') }}";
      } else if (status === 'pending') {
          window.location.href = "{{ route('payment.unfinish') }}";
      } else {
          window.location.href = "{{ route('payment.error') }}";
      }
  }

  shippingDropdown.addEventListener('change', updateTotal);
</script>
@endpush


@endsection
