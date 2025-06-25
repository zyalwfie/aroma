@extends('frontend.layouts.app')

@section('title', 'My Account')

@section('content')
<section class="section-margin--small">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">

        {{-- INFORMASI PROFIL --}}
        <div class="card p-4 mb-4">
          <div class="row">
            <div class="col-md-3 text-center">
              <img src="{{ auth()->user()->profile_picture ? asset('storage/profile/' . auth()->user()->profile_picture) : 'https://via.placeholder.com/100' }}" alt="Profile Picture" class="rounded-circle mb-3" width="100" height="100">

              {{-- Upload & Delete --}}
              <form action="{{ route('account.picture') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="profile_picture" class="form-control mb-2">
                <button type="submit" class="btn btn-outline-secondary btn-sm btn-block">Upload Foto</button>
              </form>
              @if(auth()->user()->profile_picture)
              <form action="{{ route('account.delete-picture') }}" method="POST">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger btn-block mt-2">Hapus Foto</button>
              </form>
              @endif
            </div>

            <div class="col-md-9">
              <form action="{{ route('account.update') }}" method="POST">
                @csrf
                <div class="form-group">
                  <label>Nama</label>
                  <input type="text" class="form-control" name="name" value="{{ auth()->user()->name }}" {{ auth()->user()->updated_name_at && now()->diffInDays(auth()->user()->updated_name_at) < 30 ? 'readonly' : '' }}>
                  @if(auth()->user()->updated_name_at && now()->diffInDays(auth()->user()->updated_name_at) < 30)
                    <small class="text-muted">Nama hanya bisa diubah sebulan sekali.</small>
                  @endif
                </div>

                <div class="form-group">
                  <label>Email</label>
                  <input type="email" class="form-control" name="email" value="{{ auth()->user()->email }}">
                </div>

                <div class="form-group">
                  <label>No. HP</label>
                  <input type="text" class="form-control" name="phone" value="{{ auth()->user()->phone }}">
                </div>

                <button type="submit" class="btn btn-primary btn-sm">Simpan Perubahan</button>
              </form>

              <div class="form-group mt-3">
                <label>Password</label><br>
                <a href="#" class="btn btn-sm btn-outline-primary" data-toggle="modal" data-target="#gantiPasswordModal">Ganti Password</a>
              </div>

              <div class="form-group mt-3">
                <label>Alamat</label>
                <div class="d-flex flex-wrap gap-2">
                  @foreach($user->addresses as $address)
                    <form action="{{ route('account.address.update', $address->id) }}" method="POST" class="mr-2">
                      @csrf @method('PUT')
                      <input type="hidden" name="label" value="{{ $address->label }}">
                      <input type="hidden" name="address" value="{{ $address->address }}">
                      <button class="btn btn-outline-dark btn-sm" type="submit">{{ $address->label }}</button>
                    </form>
                  @endforeach
                </div>
                <form action="{{ route('account.address.add') }}" method="POST" class="mt-2">
                  @csrf
                  <input type="text" name="label" class="form-control mb-2" placeholder="Label baru">
                  <input type="text" name="address" class="form-control mb-2" placeholder="Alamat baru">
                  <button type="submit" class="btn btn-outline-dark btn-sm">Tambah Alamat</button>
                </form>
              </div>
            </div>
          </div>
        </div>

        {{-- NAVIGASI TABS --}}
        <ul class="nav nav-tabs mb-3" id="accountTab" role="tablist">
          <li class="nav-item">
            <a class="nav-link active" id="pesanan-tab" data-toggle="tab" href="#pesanan" role="tab">Pesanan Saya</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="riwayat-tab" data-toggle="tab" href="#riwayat" role="tab">Riwayat Pesanan</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" id="ulasan-tab" data-toggle="tab" href="#ulasan" role="tab">Ulasan Saya</a>
          </li>
        </ul>

        {{-- ISI TABS --}}
        <div class="tab-content" id="accountTabContent">

          {{-- PESANAN SAYA --}}
          <div class="tab-pane fade show active" id="pesanan" role="tabpanel">
            @if($orders->where('status', '!=', 'completed')->count())
              @foreach($orders->where('status', '!=', 'completed') as $order)
              <div class="card p-3 mb-2">
                <strong>ID Pesanan: #{{ $order->id }}</strong>
                <p>{{ $order->created_at->format('d M Y') }} • Total: Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                <p>Status: <span class="badge badge-warning">{{ ucfirst($order->status) }}</span></p>
               <form action="{{ route('account.orders.complete', $order->id) }}" method="POST" onsubmit="return confirm('Yakin pesanan ini sudah kamu terima?')">
    @csrf
    <button type="submit" class="btn btn-sm btn-success">Pesanan Selesai</button>
</form>

              </div>
              @endforeach
            @else
              <p class="text-muted">Tidak ada pesanan dalam proses.</p>
            @endif
          </div>

          {{-- RIWAYAT PESANAN --}}
          <div class="tab-pane fade" id="riwayat" role="tabpanel">
            @if($orders->where('status', 'completed')->count())
              @foreach($orders->where('status', 'completed') as $order)
              <div class="card p-3 mb-2">
                <strong>ID Pesanan: #{{ $order->id }}</strong>
                <p>{{ $order->created_at->format('d M Y') }} • Total: Rp {{ number_format($order->total, 0, ',', '.') }}</p>
                <div class="d-flex gap-2">
                  <a href="{{ route('product.detail', $order->orderItems->first()->product->slug ?? '#') }}" class="btn btn-sm btn-info">Beli Lagi</a>
                  <!-- <a href="{{ route('account.reviews.form', $order->id) }}" class="btn btn-sm btn-warning">Beri Nilai</a> -->
                  <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#reviewModal{{ $order->id }}">
  Beri Nilai
</button>
                </div>
              </div>
              @include('frontend.account.review-modal', ['order' => $order])
              @endforeach
            @else
              <p class="text-muted">Belum ada riwayat pesanan.</p>
            @endif
          </div>

          {{-- ULASAN SAYA --}}
          <div class="tab-pane fade" id="ulasan" role="tabpanel">
            @if($reviews->count())
              @foreach($reviews as $review)
              <div class="card p-3 mb-2">
                <strong>{{ $review->product->name ?? '-' }}</strong>
                <p class="mb-1">Rating: {{ $review->rating }} / 5</p>
                <p>{{ $review->comment }}</p>
              </div>
              @endforeach
            @else
              <p class="text-muted">Belum ada ulasan yang Anda berikan.</p>
            @endif
          </div>
        </div>

        {{-- LOGOUT --}}
        <div class="text-center mt-4">
          <form id="logout-form" action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-danger">Log out</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- MODAL GANTI PASSWORD --}}
<div class="modal fade" id="gantiPasswordModal" tabindex="-1">
  <div class="modal-dialog"><div class="modal-content">
    <form method="POST" action="{{ route('account.password') }}">
      @csrf
      <div class="modal-header"><h5 class="modal-title">Ganti Password</h5></div>
      <div class="modal-body">
        <input type="password" name="current_password" class="form-control mb-2" placeholder="Password Lama" required>
        <input type="password" name="new_password" class="form-control mb-2" placeholder="Password Baru" required>
        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Konfirmasi Password Baru" required>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Simpan</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
      </div>
    </form>
  </div></div>
</div>
@endsection
