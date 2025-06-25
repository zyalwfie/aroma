@extends('frontend.layouts.app')

@section('title', 'Ulasan Saya')

@section('content')
<div class="container mt-5">
  <h3>Ulasan Saya</h3>
  @foreach ($reviews as $review)
    <div class="card mb-3">
      <div class="card-body">
        <h5>{{ $review->product->name }}</h5>
        <p>Rating: {{ $review->rating }}/5</p>
        <p>{{ $review->comment }}</p>
        <small>{{ $review->created_at->format('d M Y') }}</small>
      </div>
    </div>
  @endforeach
</div>
@endsection
