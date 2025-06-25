@extends('frontend.layouts.app')

@section('title', 'Keranjang Belanja')
@section('content')

<section class="cart_area">
    <div class="container">
        <div class="cart_inner">
            <!-- FORM CHECKOUT -->
            <form id="cart-form" method="GET" action="{{ route('checkout.show') }}">

                @csrf
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Pilih</th>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cartItems as $item)
                            <tr data-id="{{ $item->id }}">
                                <td>
                                    <input type="checkbox" class="cart-checkbox" name="selected_items[]" value="{{ $item->id }}">
                                </td>
                                <td>
                                    <div class="media">
                                        <img src="{{ asset('storage/' . $item->product->image) }}" width="70" alt="{{ $item->product->name }}">
                                        <div class="media-body">
                                            <p>{{ $item->product->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h5 class="price" data-price="{{ $item->product->price }}">
                                        Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                    </h5>
                                </td>
                                <td>
                                    <input type="number" name="quantities[{{ $item->id }}]" min="1" value="{{ $item->quantity }}"
                                        class="form-control quantity-input" data-id="{{ $item->id }}">
                                </td>
                                <td>
                                    <h5 class="item-total">
                                        Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}
                                    </h5>
                                </td>
                                <td>
                                    <form id="delete-form-{{ $item->id }}" action="{{ route('cart.destroy', $item->id) }}" method="POST" style="display:none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                    <button class="btn btn-danger" onclick="event.preventDefault(); document.getElementById('delete-form-{{ $item->id }}').submit();">Hapus</button>
                                </td>
                            </tr>
                            @endforeach

                            <tr>
                                <td colspan="4"><h5>Subtotal</h5></td>
                                <td><h5 id="subtotal-display">Rp 0</h5></td>
                                <td>
                                    <button type="submit" class="btn btn-primary">Check out</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </form>
        </div>
    </div>
</section>

@push('scripts')
<script>
document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('change', function () {
        const id = this.dataset.id;
        const qty = this.value;

        if (qty < 1) {
            if (confirm('Kuantitas kurang dari 1. Hapus produk ini dari keranjang?')) {
                document.getElementById('delete-form-' + id).submit();
            } else {
                this.value = 1;
            }
            return;
        }

        fetch("{{ route('cart.update') }}", {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id, quantity: qty })
        }).then(() => updateSubtotal());
    });
});

function updateSubtotal() {
    let subtotal = 0;
    document.querySelectorAll('input.cart-checkbox:checked').forEach(checkbox => {
        const row = checkbox.closest('tr');
        const price = parseInt(row.querySelector('.price').dataset.price);
        const qty = parseInt(row.querySelector('.quantity-input').value);
        subtotal += price * qty;
    });
    document.getElementById('subtotal-display').innerText = 'Rp ' + subtotal.toLocaleString('id-ID');
}

document.querySelectorAll('.cart-checkbox, .quantity-input').forEach(el => {
    el.addEventListener('change', updateSubtotal);
});
const form = document.getElementById('cart-form');
    form.addEventListener('submit', function(e) {
        const checked = document.querySelectorAll('.cart-checkbox:checked');
        if (checked.length === 0) {
            e.preventDefault();
            alert('Pilih minimal satu produk untuk checkout!');
        }
    });

document.addEventListener('DOMContentLoaded', updateSubtotal);
</script>
@endpush

@endsection
