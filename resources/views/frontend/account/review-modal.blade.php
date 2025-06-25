<div class="modal fade" id="reviewModal{{ $order->id }}" tabindex="-1" role="dialog" aria-labelledby="reviewModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <form action="{{ route('account.reviews.submit', $order->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="reviewModalLabel">Beri Ulasan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          @foreach ($order->orderItems as $item)
          <div class="card mb-3 p-3">
            <div class="d-flex align-items-center mb-2">
              <img src="{{ asset('storage/' . $item->product->image) }}" width="60" class="mr-3">
              <strong>{{ $item->product->name }}</strong>
            </div>

            <input type="hidden" name="reviews[{{ $item->product->id }}][product_id]" value="{{ $item->product->id }}">

            <div class="form-group">
              <label>Rating:</label><br>
              @for ($i = 1; $i <= 5; $i++)
                <label class="mr-1">
                  <input type="radio" name="reviews[{{ $item->product->id }}][rating]" value="{{ $i }}" required>
                  ⭐
                </label>
              @endfor
            </div>

            <div class="form-group">
              <label>Review:</label>
              <textarea class="form-control" name="reviews[{{ $item->product->id }}][comment]" rows="3"></textarea>
            </div>
          </div>
          @endforeach
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>
