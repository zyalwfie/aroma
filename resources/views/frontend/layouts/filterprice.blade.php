<!-- FILTER BY PRICE -->
<div class="sidebar-filter">
    <div class="top-filter-head">Product Filters</div>
    <div class="common-filter">
        <div class="head">Price</div>
        <div class="price-range-area">
            <form id="price-filter-form" method="GET" action="{{ route('product.collection') }}">
                {{-- Simpan kategori aktif juga jika ada --}}
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <input type="hidden" name="price_min" id="price_min" value="{{ request('price_min', 0) }}">
                <input type="hidden" name="price_max" id="price_max" value="{{ request('price_max', 10000000) }}">

                <div id="price-range"></div>
                <div class="value-wrapper d-flex mt-2">
                    <div class="price">Price:</div>
                    <span>Rp</span>
                    <div id="lower-value" class="mx-1">{{ number_format(request('price_min', 0)) }}</div>
                    <div class="to mx-1">to</div>
                    <span>Rp</span>
                    <div id="upper-value" class="mx-1">{{ number_format(request('price_max', 10000000)) }}</div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- END FILTER BY PRICE -->

@push('scripts')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
    $(function () {
        const minPrice = 0;
        const maxPrice = 1000000;

        const currentMin = {{ request('price_min', 0) }};
        const currentMax = {{ request('price_max', 1000000) }};

        $("#price-range").slider({
            range: true,
            min: minPrice,
            max: maxPrice,
            values: [currentMin, currentMax],
            slide: function (event, ui) {
                $("#lower-value").text(ui.values[0].toLocaleString('id-ID'));
                $("#upper-value").text(ui.values[1].toLocaleString('id-ID'));
                $("#price_min").val(ui.values[0]);
                $("#price_max").val(ui.values[1]);
            },
            change: function () {
                $("#price-filter-form").submit();
            }
        });
    });
</script>
@endpush
