@extends("frontend.layouts.app")

@section("title", "My Account")

@section("content")
	<section class="section-margin--small">
		<div class="container">
			<div class="row">
				<div class="col-lg-12">

					{{-- INFORMASI PROFIL --}}
					<div class="card p-4 mb-4">
						<div class="row">
							<div class="col-md-3 text-center">
								<img alt="Profile Picture" class="rounded-circle mb-3" height="100"
									src="{{ auth()->user()->profile_picture ? asset("storage/profile/" . auth()->user()->profile_picture) : "https://via.placeholder.com/100" }}"
									width="100">

								{{-- Upload & Delete --}}
								<form action="{{ route("account.picture") }}" enctype="multipart/form-data" method="POST">
									@csrf
									<input class="form-control mb-2" name="profile_picture" type="file">
									<button class="btn btn-outline-secondary btn-sm btn-block" type="submit">Upload Foto</button>
								</form>
								@if (auth()->user()->profile_picture)
									<form action="{{ route("account.delete-picture") }}" method="POST">
										@csrf @method("DELETE")
										<button class="btn btn-sm btn-danger btn-block mt-2" type="submit">Hapus Foto</button>
									</form>
								@endif
							</div>

							<div class="col-md-9">
								<form action="{{ route("account.update") }}" method="POST">
									@csrf
									<div class="form-group">
										<label>Nama</label>
										<input
											{{ auth()->user()->updated_name_at && now()->diffInDays(auth()->user()->updated_name_at) < 30 ? "readonly" : "" }}
											class="form-control" name="name" type="text" value="{{ auth()->user()->name }}">
										@if (auth()->user()->updated_name_at && now()->diffInDays(auth()->user()->updated_name_at) < 30)
											<small class="text-muted">Nama hanya bisa diubah sebulan sekali.</small>
										@endif
									</div>

									<div class="form-group">
										<label>Email</label>
										<input class="form-control" name="email" type="email" value="{{ auth()->user()->email }}">
									</div>

									<div class="form-group">
										<label>No. HP</label>
										<input class="form-control" name="phone" type="text" value="{{ auth()->user()->phone }}">
									</div>

									<button class="btn btn-primary btn-sm" type="submit">Simpan Perubahan</button>
								</form>

								<div class="form-group mt-3">
									<label>Password</label><br>
									<a class="btn btn-sm btn-outline-primary" data-target="#gantiPasswordModal" data-toggle="modal"
										href="#">Ganti Password</a>
								</div>

								<div class="form-group mt-3">
									<label>Alamat</label>
									<div class="d-flex flex-wrap gap-2">
										@foreach ($user->addresses as $address)
											<div class="d-flex mb-2 me-2">
												<button class="btn btn-outline-dark btn-sm" onclick="editAddress({{ $address->id }})"
													type="button">{{ $address->label }}</button>
												<form action="{{ route("account.address.delete", $address->id) }}" class="ms-1" method="POST">
													@csrf @method("DELETE")
													<button class="btn btn-outline-danger btn-sm" onclick="return confirm('Yakin ingin menghapus alamat ini?')"
														type="submit">×</button>
												</form>
											</div>
										@endforeach
									</div>
									<button class="btn btn-outline-dark btn-sm mt-2" onclick="addNewAddress()" type="button">Tambah Alamat</button>
								</div>
							</div>
						</div>
					</div>

					{{-- NAVIGASI TABS --}}
					<ul class="nav nav-tabs mb-3" id="accountTab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="tab" href="#pesanan" id="pesanan-tab" role="tab">Pesanan Saya</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#riwayat" id="riwayat-tab" role="tab">Riwayat Pesanan</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="tab" href="#ulasan" id="ulasan-tab" role="tab">Ulasan Saya</a>
						</li>
					</ul>

					{{-- ISI TABS --}}
					<div class="tab-content" id="accountTabContent">

						{{-- PESANAN SAYA --}}
						<div class="tab-pane fade show active" id="pesanan" role="tabpanel">
							@if ($orders->where("status", "!=", "completed")->count())
								@foreach ($orders->where("status", "!=", "completed") as $order)
									<div class="card p-3 mb-2">
										<strong>ID Pesanan: #{{ $order->id }}</strong>
										<p>{{ $order->created_at->format("d M Y") }} • Total: Rp {{ number_format($order->total, 0, ",", ".") }}</p>
										<p>Status: <span class="badge badge-warning">{{ ucfirst($order->status) }}</span></p>
										<form action="{{ route("account.orders.complete", $order->id) }}" method="POST"
											onsubmit="return confirm('Yakin pesanan ini sudah kamu terima?')">
											@csrf
											<button class="btn btn-sm btn-success" type="submit">Pesanan Selesai</button>
										</form>

									</div>
								@endforeach
							@else
								<p class="text-muted">Tidak ada pesanan dalam proses.</p>
							@endif
						</div>

						{{-- RIWAYAT PESANAN --}}
						<div class="tab-pane fade" id="riwayat" role="tabpanel">
							@if ($orders->where("status", "completed")->count())
								@foreach ($orders->where("status", "completed") as $order)
									<div class="card p-3 mb-2">
										<strong>ID Pesanan: #{{ $order->id }}</strong>
										<p>{{ $order->created_at->format("d M Y") }} • Total: Rp {{ number_format($order->total, 0, ",", ".") }}</p>
										<div class="d-flex gap-2">
											<a class="btn btn-sm btn-info"
												href="{{ route("product.detail", $order->orderItems->first()->product->slug ?? "#") }}">Beli Lagi</a>
											<button class="btn btn-sm btn-warning" data-target="#reviewModal{{ $order->id }}" data-toggle="modal">
												Beri Nilai
											</button>
										</div>
									</div>
									@include("frontend.account.review-modal", ["order" => $order])
								@endforeach
							@else
								<p class="text-muted">Belum ada riwayat pesanan.</p>
							@endif
						</div>

						{{-- ULASAN SAYA --}}
						<div class="tab-pane fade" id="ulasan" role="tabpanel">
							@if ($reviews->count())
								@foreach ($reviews as $review)
									<div class="card p-3 mb-2">
										<strong>{{ $review->product->name ?? "-" }}</strong>
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
						<form action="{{ route("logout") }}" id="logout-form" method="POST">
							@csrf
							<button class="btn btn-danger" type="submit">Log out</button>
						</form>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- MODAL GANTI PASSWORD --}}
	<div class="modal fade" id="gantiPasswordModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{ route("account.password") }}" method="POST">
					@csrf
					<div class="modal-header">
						<h5 class="modal-title">Ganti Password</h5>
					</div>
					<div class="modal-body">
						<input class="form-control mb-2" name="current_password" placeholder="Password Lama" required type="password">
						<input class="form-control mb-2" name="new_password" placeholder="Password Baru" required type="password">
						<input class="form-control" name="new_password_confirmation" placeholder="Konfirmasi Password Baru" required
							type="password">
					</div>
					<div class="modal-footer">
						<button class="btn btn-primary" type="submit">Simpan</button>
						<button class="btn btn-secondary" data-dismiss="modal" type="button">Batal</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	{{-- MODAL TAMBAH/EDIT ALAMAT --}}
	<div class="modal fade" id="addressModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="addressModalLabel">Tambah Alamat Baru</h5>
					<button aria-label="Close" class="close" data-dismiss="modal" type="button">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<form action="{{ route("account.address.add") }}" id="addressForm" method="POST">
					@csrf
					<input id="addressMethod" name="_method" type="hidden" value="POST">
					<input id="address_id" name="address_id" type="hidden">

					<div class="modal-body">
						<div class="form-group mb-3">
							<label for="label">Label Alamat</label>
							<input class="form-control" id="label" name="label" placeholder="Rumah, Kantor, dll" required
								type="text">
						</div>

						<div class="form-group mb-3">
							<label for="province_id">Provinsi</label>
							<select class="form-control" id="province_id" name="province_id" required>
								<option value="">Pilih Provinsi</option>
								<!-- Akan diisi dari API -->
							</select>
							<div class="invalid-feedback">Pilih provinsi terlebih dahulu</div>
						</div>

						<div class="form-group mb-3">
							<label for="city_id">Kota/Kabupaten</label>
							<select class="form-control" disabled id="city_id" name="city_id" required>
								<option value="">Pilih Kota/Kabupaten</option>
								<!-- Akan diisi dari API -->
							</select>
							<div class="invalid-feedback">Pilih kota/kabupaten</div>
						</div>

						<div class="form-group mb-3">
							<label for="address">Alamat Lengkap</label>
							<textarea class="form-control" id="address" name="address"
							 placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan" required rows="3"></textarea>
						</div>

						<div class="form-group mb-3">
							<label for="zip">Kode Pos</label>
							<input class="form-control" id="zip" name="zip" placeholder="Kode Pos" type="text">
						</div>

						<div class="form-group mb-3">
							<label for="phone">Nomor Telepon</label>
							<input class="form-control" id="phone" name="phone" placeholder="Nomor Telepon" type="text">
						</div>
					</div>

					<div class="modal-footer">
						<button class="btn btn-secondary" data-dismiss="modal" type="button">Batal</button>
						<button class="btn btn-primary" type="submit">Simpan</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	@push("scripts")
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const provinceSelect = document.getElementById('province_id');
				const citySelect = document.getElementById('city_id');
				const addressForm = document.getElementById('addressForm');
				const addressMethod = document.getElementById('addressMethod');
				const addressModalLabel = document.getElementById('addressModalLabel');

				async function fetchProvinces() {
					try {
						provinceSelect.innerHTML = '<option value="">Memuat provinsi...</option>';
						provinceSelect.disabled = true;

						const response = await fetch('/api/provinces');
						if (!response.ok) {
							throw new Error('Gagal mengambil data provinsi');
						}

						const provinces = await response.json();

						provinceSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
						provinces.forEach(province => {
							const option = document.createElement('option');
							option.value = province.province_id;
							option.textContent = province.province;
							provinceSelect.appendChild(option);
						});

						provinceSelect.disabled = false;
					} catch (error) {
						console.error('Error fetching provinces:', error);
						provinceSelect.innerHTML = '<option value="">Error: Gagal memuat provinsi</option>';
						provinceSelect.disabled = false;
					}
				}

				async function fetchCities(provinceId) {
					if (!provinceId) {
						citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
						citySelect.disabled = true;
						return;
					}

					try {
						citySelect.innerHTML = '<option value="">Memuat kota...</option>';
						citySelect.disabled = true;

						const response = await fetch(`/api/cities?province_id=${provinceId}`);
						if (!response.ok) {
							throw new Error('Gagal mengambil data kota');
						}

						const cities = await response.json();

						citySelect.innerHTML = '<option value="">Pilih Kota/Kabupaten</option>';
						cities.forEach(city => {
							const option = document.createElement('option');
							option.value = city.city_id;
							option.textContent = city.type + ' ' + city.city_name;
							citySelect.appendChild(option);
						});

						citySelect.disabled = false;
					} catch (error) {
						console.error('Error fetching cities:', error);
						citySelect.innerHTML = '<option value="">Error: Gagal memuat kota</option>';
						citySelect.disabled = false;
					}
				}

				provinceSelect.addEventListener('change', function() {
					fetchCities(this.value);
				});

				window.addNewAddress = function() {
					addressForm.reset();

					addressForm.action = "{{ route("account.address.add") }}";
					addressMethod.value = 'POST';

					addressModalLabel.textContent = 'Tambah Alamat Baru';

					fetchProvinces();

					$('#addressModal').modal('show');
				};

				window.editAddress = function(addressId) {
					addressForm.reset();

					addressForm.action = `{{ url("account/address") }}/${addressId}`;
					addressMethod.value = 'PUT';
					document.getElementById('address_id').value = addressId;

					addressModalLabel.textContent = 'Edit Alamat';

					fetch(`/account/address/${addressId}/data`)
						.then(response => response.json())
						.then(data => {
							document.getElementById('label').value = data.label;
							document.getElementById('address').value = data.address;
							document.getElementById('zip').value = data.zip || '';
							document.getElementById('phone').value = data.phone || '';

							fetchProvinces().then(() => {
								if (data.province_id) {
									provinceSelect.value = data.province_id;

									fetchCities(data.province_id).then(() => {
										if (data.city_id) {
											citySelect.value = data.city_id;
										}
									});
								}
							});
						})
						.catch(error => {
							console.error('Error fetching address data:', error);
							alert('Gagal mengambil data alamat.');
						});

					$('#addressModal').modal('show');
				};
			});
		</script>
	@endpush

@endsection
