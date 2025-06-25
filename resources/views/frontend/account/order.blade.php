{{-- resources/views/frontend/account/orders.blade.php --}}
@extends('frontend.layouts.app')
@section('title', 'Pesanan Saya')
@section('content')
<section class="section-margin--small">
  <div class="container">
    <h3 class="mb-4">Pesanan Saya</h3>
    @forelse($orders as $order)
      <div class="card mb-3">
        <div class="card-body">
          <h5>Pesanan #{{ $order->id }} - {{ $order->created_at->format('d M Y') }}</h5>
          <p>Status: <strong>{{ ucfirst($order->status) }}</strong></p>
          <ul>
            @foreach($order->orderItems as $item)
              <li>{{ $item->product->name }} x {{ $item->quantity }}</li>
            @endforeach
          </ul>
          <form action="{{ route('account.orders.complete', $order->id) }}" method="POST">
            @csrf
            <button class="btn btn-sm btn-success">Pesanan Selesai</button>
          </form>
        </div>
      </div>
    @empty
      <p>Tidak ada pesanan aktif.</p>
    @endforelse
  </div>
</section>
@endsection
