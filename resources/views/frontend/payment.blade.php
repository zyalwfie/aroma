@extends('frontend.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Pembayaran</div>

                <div class="card-body">
                    <h4 class="text-center">Total Pembayaran: Rp {{ number_format($order->total, 0, ',', '.') }}</h4>
                    
                    <div id="snap-container" class="text-center mt-4"></div>
                    
                    <div class="text-center mt-4">
                        <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('services.midtrans.client_key') }}"></script>
<script>
    window.snap.pay('{{ $snapToken }}', {
        onSuccess: function(result){
            window.location.href = "{{ route('order.success') }}";
        },
        onPending: function(result){
            window.location.href = "{{ route('order.pending') }}";
        },
        onError: function(result){
            window.location.href = "{{ route('order.error') }}";
        }
    });
</script>
@endsection