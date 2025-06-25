@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center py-5">
            <div class="alert alert-danger">
                <h2>Pembayaran Gagal</h2>
                <p>Silakan coba lagi atau hubungi kami.</p>
                <a href="{{ route('checkout.show') }}" class="btn btn-primary">Coba Lagi</a>
            </div>
        </div>
    </div>
</div>
@endsection