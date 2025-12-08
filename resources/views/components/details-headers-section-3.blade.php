      <section class="bg-white py-12 md:py-24 lg:py-32" x-data="{ showContent: false }">
        <div class="container px-4 mx-auto">
          <h2 class="text-4xl text-center font-heading font-semibold text-rhino-600 tracking-xs mb-14">produits similaires</h2>
          <div class="flex flex-wrap -mx-4 -mb-8">
            @php
              $visible = 8;
            @endphp

            @foreach(($products ?? collect())->slice(0, $visible) as $p)
              <div class="w-full md:w-1/2 lg:w-1/4 px-4 pb-8">
                <a href="{{ route('products.show', $p->slug) }}" class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150">
                  @if(!empty($p->price_promo))
                    <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full">Sale</span>
                  @endif
                  <img class="absolute top-0 left-1/2 mt-5 transform -translate-x-1/2" src="{{ $p->image }}" alt="{{ $p->title }}" loading="lazy">
                  <div class="relative z-10 w-full px-8 mt-auto text-center">
                    <span class="block text-base text-rhino-500 mb-1">{{ $p->title }}</span>
                    @if(!empty($p->price_promo))
                      <span class="block text-base text-rhino-300">
                        <span class="line-through mr-2">${{ number_format($p->base_price, 2) }}</span>
                        <span class="text-rhino-600 font-semibold">${{ number_format($p->price_promo, 2) }}</span>
                      </span>
                    @else
                      <span class="block text-base text-rhino-300">${{ number_format($p->base_price, 2) }}</span>
                    @endif
                  </div>
                </a>
              </div>
            @endforeach

            @if(($products ?? collect())->count() > $visible)
              @foreach(($products ?? collect())->slice($visible) as $p)
                <div :class="{'hidden': !showContent}" class="w-full md:w-1/2 lg:w-1/4 px-4 pb-8 hidden">
                  <a href="{{ route('products.show', $p->slug) }}" class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150">
                    @if(!empty($p->price_promo))
                      <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full">Sale</span>
                    @endif
                    <img class="absolute top-0 left-1/2 mt-5 transform -translate-x-1/2" src="{{ $p->image }}" alt="{{ $p->title }}" loading="lazy">
                    <div class="relative z-10 w-full px-8 mt-auto text-center">
                      <span class="block text-base text-rhino-500 mb-1">{{ $p->title }}</span>
                      @if(!empty($p->price_promo))
                        <span class="block text-base text-rhino-300">
                          <span class="line-through mr-2">${{ number_format($p->base_price, 2) }}</span>
                          <span class="text-rhino-600 font-semibold">${{ number_format($p->price_promo, 2) }}</span>
                        </span>
                      @else
                        <span class="block text-base text-rhino-300">${{ number_format($p->base_price, 2) }}</span>
                      @endif
                    </div>
                  </a>
                </div>
              @endforeach
            @endif
          </div>
          <div class="mt-12 text-center"><a x-on:click.prevent="showContent = true" :class="{ 'hidden': showContent }" class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-purple-500 hover:text-white bg-white border border-purple-500 rounded-sm hover:bg-purple-500 transition duration-200" href="#">Show more</a></div>
        </div>
      </section>