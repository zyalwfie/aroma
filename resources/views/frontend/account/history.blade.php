@extends('frontend.layouts.app')

@section('title', 'Riwayat Pesanan')

@section('content')
<div class="container mt-5">
    <h3>Riwayat Pesanan</h3>
    @foreach ($orders as $order)
        <div class="card mb-3">
            <div class="card-header">
                Order #{{ $order->id }} - {{ $order->created_at->format('d M Y') }}
            </div>
            <div class="card-body">
                <ul>
                    @foreach ($order->orderItems as $item)
                        <li>{{ $item->product->name }} x {{ $item->quantity }}</li>
                    @endforeach
                </ul>
                <p>Total: <strong>Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>

                <a href="{{ route('home') }}" class="btn btn-sm btn-outline-primary">Beli Lagi</a>
                <a href="{{ route('account.reviews.form', $order->id) }}" class="btn btn-sm btn-outline-warning">Beri Nilai</a>
            </div>
        </div>
    @endforeach
</div>
@endsection
