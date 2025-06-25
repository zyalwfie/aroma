@extends('layouts.app')

@section('content')
<div class="product-detail">
    <img src="{{ asset('uploads/products/' . $product->image) }}" alt="{{ $product->name }}" width="300">
    <h1>{{ $product->name }}</h1>
    <p>Harga: Rp {{ number_format($product->price, 0, ',', '.') }}</p>
    <p>{{ $product->description }}</p>
    <p>Stok: {{ $product->stock }}</p>
</div>
@endsection
