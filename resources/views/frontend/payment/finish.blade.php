@extends('frontend.layouts.app')
@section('title', 'Pembayaran Berhasil')
@section('content')
<div class="container py-5 text-center">
  <h2>Pembayaran Berhasil!</h2>
  <p>Terima kasih, pesanan Anda sedang diproses.</p>
  <a href="{{ route('home') }}" class="btn btn-primary mt-3">Kembali ke Beranda</a>
</div>
@endsection
