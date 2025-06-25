<section class="section-margin--small mb-5">
      <div class="container">
        <div class="row">
          <div class="col-xl-3 col-lg-4 col-md-5">
            <div class="sidebar-categories">
              <div class="head">Browse Categories</div>
              <ul class="main-categories">
                <li class="common-filter">
                  <form action="#">
                    <ul>
                     @foreach ($categories as $category)
                        <li class="filter-list">
                          <input class="pixel-radio" type="radio" id="category{{ $category->id }}"
                                name="category"
                                value="{{ $category->id }}"
                                onchange="this.form.submit()"
                                {{ request('category') == $category->id ? 'checked' : '' }} />
                          <label for="category{{ $category->id }}">
                            {{ $category->name }} <span>({{ $category->products_count ?? 0 }})</span>
                          </label>
                        </li>
                      @endforeach
                    </ul>
                  </form>
                </li>
              </ul>
            </div>
