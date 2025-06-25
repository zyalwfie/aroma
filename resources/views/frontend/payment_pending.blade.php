@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center py-5">
            <div class="alert alert-warning">
                <h2>Pembayaran Pending</h2>
                <p>Silakan selesaikan pembayaran Anda.</p>
                <a href="{{ route('home') }}" class="btn btn-primary">Kembali ke Beranda</a>
            </div>
        </div>
    </div>
</div>
@endsection