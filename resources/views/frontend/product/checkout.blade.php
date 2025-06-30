@extends("frontend.layouts.app")

@section("title", "Checkout")

@section("content")

	<!--================Checkout Area =================-->
	<section class="checkout_area section-margin--small">
		<div class="container">
			<form action="{{ route("checkout.process") }}" method="POST">
				@csrf
				<div class="row">
					<!-- ADDRESS SELECTION -->
					<div class="col-12 mb-4">
						<h3>Pilih Alamat Pengiriman</h3>
						<div class="d-flex flex-wrap gap-3">
							@foreach ($user->addresses as $address)
								<label class="btn btn-outline-dark address-item" for="address">
									<input data-destination="{{ $address->destination_id }}" hidden id="address" name="address_id" required
										type="radio" value="{{ $address->id }}">
									<div>
										<strong>{{ $address->label }}</strong><br>
										<small>{{ $address->destination_name ?? $address->city_name }}{{ $address->province_name }}</small><br>
										<small class="text-muted">{{ $address->address }}</small>
									</div>
								</label>
							@endforeach
							<a class="btn btn-outline-primary" href="{{ route("account.index") }}">Tambah Alamat</a>
						</div>
						@error("address_id")
							<small class="text-danger">{{ $message }}</small>
						@enderror
					</div>

					<!-- ORDER DETAILS -->
					<div class="col-lg-8">
						<h3>Detail Pesanan</h3>
						<table class="table table-bordered">
							<thead>
								<tr>
									<th>Produk</th>
									<th>Harga / pcs</th>
									<th>Qty</th>
									<th>Subtotal</th>
								</tr>
							</thead>
							<tbody>
								@php $total = 0; @endphp
								@foreach ($cartItems as $item)
									@php
										$lineTotal = $item->product->price * $item->quantity;
										$total += $lineTotal;
									@endphp
									<tr>
										<td class="d-flex gap-2 align-items-center">
											<img src="{{ asset("storage/products/" . $item->product->image) }}" width="60">
											<div>{{ $item->product->name }}</div>
										</td>
										<td>Rp {{ number_format($item->product->price, 0, ",", ".") }}</td>
										<td>{{ $item->quantity }}</td>
										<td>Rp {{ number_format($lineTotal, 0, ",", ".") }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>

						<!-- SHIPPING & PAYMENT -->
						<div class="form-group mt-4">
							<label for="shipping_method">Pilih Pengiriman</label>
							<div class="form-control shipping-loading p-3 text-center d-none">
								<div class="spinner-border text-primary" role="status">
									<span class="visually-hidden">Loading...</span>
								</div>
								<p class="mt-2">Mengambil data ongkos kirim...</p>
							</div>
							<div class="shipping-container">
								<select class="form-control" disabled id="shipping_method" name="shipping_method" required>
									<option value="">Pilih alamat pengiriman terlebih dahulu</option>
								</select>
							</div>
							@error("shipping_method")
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>

						<div class="form-group mt-3">
							<label for="payment_method">Metode Pembayaran</label>
							<select class="form-control" name="payment_method" required>
								<option value="midtrans">Midtrans Payment</option>
								<option value="cod">Bayar di Tempat (COD)</option>
							</select>
							@error("payment_method")
								<small class="text-danger">{{ $message }}</small>
							@enderror
						</div>
					</div>

					<!-- RINGKASAN PEMBAYARAN -->
					<div class="col-lg-4">
						<div class="order_box p-4 border">
							<h4>Ringkasan</h4>
							<ul class="list-unstyled">
								<li class="d-flex justify-content-between">
									<span>Subtotal Pesanan</span>
									<strong>Rp {{ number_format($total, 0, ",", ".") }}</strong>
								</li>
								<li class="d-flex justify-content-between">
									<span>Ongkir</span>
									<strong id="shipping-cost">Rp 0</strong>
								</li>
								<li class="d-flex justify-content-between mt-2 border-top pt-2">
									<span>Total</span>
									<strong id="total-cost">Rp {{ number_format($total, 0, ",", ".") }}</strong>
								</li>
							</ul>

							<div class="form-check mt-3">
								<input class="form-check-input" id="terms" name="terms" required type="checkbox">
								<label class="form-check-label" for="terms">
									Saya menyetujui <a href="#">syarat & ketentuan*</a>
								</label>
								@error("terms")
									<small class="text-danger d-block">{{ $message }}</small>
								@enderror
							</div>

							<div class="text-center mt-4">
								<button class="btn btn-primary btn-block w-100" type="submit">Bayar Sekarang</button>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</section>
	<!--================End Checkout Area =================-->

	@push("scripts")
		<script>
			document.addEventListener('DOMContentLoaded', function() {
				const addressItems = document.querySelectorAll('.address-item input[type="radio"]');
				const shippingSelect = document.getElementById('shipping_method');
				const shippingCostEl = document.getElementById('shipping-cost');
				const totalCostEl = document.getElementById('total-cost');
				const subtotal = {{ $total }};
				const totalWeight = 1000;
				const shippingLoading = document.querySelector('.shipping-loading');
				const shippingContainer = document.querySelector('.shipping-container');
                const address = document.querySelectorAll('input[type="radio"][name="address_id"]');

                console.log(address);

				let selectedDestinationId = null;

				function showLoading() {
					shippingLoading.classList.remove('d-none');
					shippingContainer.classList.add('d-none');
				}

				function hideLoading() {
					shippingLoading.classList.add('d-none');
					shippingContainer.classList.remove('d-none');
				}

				async function fetchShippingCost(destinationId) {
					if (!destinationId) {
						console.error('No destination ID provided');
						return;
					}

					selectedDestinationId = destinationId;
					showLoading();

					try {
						console.log('Fetching shipping cost for destination:', destinationId);

						const response = await fetch('/api/cost', {
							method: 'POST',
							headers: {
								'Content-Type': 'application/json',
								'Accept': 'application/json',
								'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
								'X-Requested-With': 'XMLHttpRequest'
							},
							body: JSON.stringify({
								destination: destinationId,
								weight: totalWeight,
								courier: 'jne,jnt,sicepat'
							})
						});

						if (!response.ok) {
							const errorText = await response.text();
							console.error('Response not OK:', response.status, errorText);
							throw new Error(`HTTP error! status: ${response.status}`);
						}

						const contentType = response.headers.get("content-type");
						if (!contentType || !contentType.includes("application/json")) {
							throw new Error("Response is not JSON");
						}

						const result = await response.json();
						console.log('Shipping cost response:', result);

						if (result.success && result.data) {
							updateShippingOptions(result.data);
						} else {
							throw new Error(result.message || 'Failed to fetch shipping costs');
						}
					} catch (error) {
						console.error('Error fetching shipping costs:', error);
						shippingSelect.innerHTML = '<option value="">Error: Gagal mengambil data ongkir</option>';

						// Show more specific error message
						if (error.message.includes('not JSON')) {
							shippingSelect.innerHTML = '<option value="">Error: Server response invalid</option>';
						} else if (error.message.includes('401') || error.message.includes('403')) {
							shippingSelect.innerHTML = '<option value="">Error: Authentication failed</option>';
						}
					} finally {
						hideLoading();
						shippingSelect.disabled = false;
					}
				}

				function updateShippingOptions(data) {
					shippingSelect.innerHTML = '<option value="">Pilih metode pengiriman</option>';

					if (!data || data.length === 0) {
						shippingSelect.innerHTML +=
							'<option value="" disabled>Tidak ada layanan pengiriman tersedia</option>';
						return;
					}

					data.forEach(courier => {
						const courierName = courier.name || courier.code;

						if (courier.costs && courier.costs.length > 0) {
							courier.costs.forEach(service => {
								if (service.cost && service.cost.length > 0) {
									const serviceName = service.service;
									const cost = service.cost[0].value;
									const etd = service.cost[0].etd || 'N/A';

									const etdText = etd.includes('HARI') ? etd : `${etd} hari`;
									const optionText =
										`${courierName} ${serviceName} (${etdText}) - Rp ${cost.toLocaleString('id-ID')}`;
									const optionValue = `${courier.code}:${cost}`;

									const option = new Option(optionText, optionValue);
									shippingSelect.add(option);
								}
							});
						}
					});

					if (shippingSelect.options.length === 1) {
						shippingSelect.innerHTML +=
							'<option value="" disabled>Tidak ada layanan pengiriman tersedia</option>';
					}
				}

				function updateTotal() {
					const selectedOption = shippingSelect.options[shippingSelect.selectedIndex];

					if (!selectedOption || !selectedOption.value) {
						shippingCostEl.innerText = 'Rp 0';
						totalCostEl.innerText = `Rp ${subtotal.toLocaleString('id-ID')}`;
						return;
					}

					const costPart = selectedOption.value.split(':')[1];
					const cost = parseInt(costPart, 10);

					shippingCostEl.innerText = `Rp ${cost.toLocaleString('id-ID')}`;
					totalCostEl.innerText = `Rp ${(subtotal + cost).toLocaleString('id-ID')}`;
				}

				address.forEach(function(item) {
					item.addEventListener('change', function() {
						if (this.checked) {
							const destinationId = this.dataset.destination;

							if (destinationId) {
								console.log('Selected address with destination ID:', destinationId);
								fetchShippingCost(destinationId);
							} else {
								console.error('No destination ID found for selected address');
								shippingSelect.innerHTML =
									'<option value="">Error: Alamat tidak memiliki ID destinasi</option>';
								shippingSelect.disabled = false;
							}
						}
					});
				});

				shippingSelect.addEventListener('change', updateTotal);

				// Handle Midtrans callback
				if (window.location.href.includes('payment_status')) {
					const status = new URLSearchParams(window.location.search).get('payment_status');
					if (status === 'success') {
						window.location.href = "{{ route("payment.finish") }}";
					} else if (status === 'pending') {
						window.location.href = "{{ route("payment.unfinish") }}";
					} else {
						window.location.href = "{{ route("payment.error") }}";
					}
				}
			});
		</script>
	@endpush

@endsection
