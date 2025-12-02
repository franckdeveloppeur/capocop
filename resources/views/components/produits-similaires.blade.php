 <section  class="py-12 md:py-24 lg:py-32" x-data="{ showContent: false }">
        <div class="container px-4 mx-auto">
          <div class="max-w-xs mx-auto md:max-w-7xl">
            <div class="flex flex-wrap -mx-4 mb-14 justify-between">
              <div class="w-full md:w-1/2 px-4 mb-12 md:mb-0">
                <h2 class="text-4xl font-heading font-semibold text-rhino-600 tracking-xs" >Produits similaires</h2>
              </div>
              <div class="w-full md:w-1/2 px-4 md:text-right"><a x-on:click.prevent="showContent=true" :class="{ 'hidden': showContent }" class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-purple-500 hover:text-white bg-white border border-purple-500 rounded-sm hover:bg-purple-500 transition duration-200" href="#" data-config-id="txt-679c65-2">voir plus</a></div>
            </div>
            <div class="flex flex-wrap -mx-4 -mb-8">
              <div class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8">
                <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                  <div class="flex items-center h-80">
                    <img class="block w-full h-80 rounded-xl" src="coleos-assets/product-blocks/vertical-product-2.png" alt="" data-config-id="img-679c65-1">
                  </div>
                  <div class="relative py-8 text-center">
                    <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full group-hover:bg-purple-500 transition duration-200" data-config-id="txt-679c65-3">New</span>
                    <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-4">Gray sport bag</span>
                    <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-5">$ 65.90</span>
                  </div>
                </a>
              </div>
              <div class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8">
                <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                  <div class="flex items-center justify-center h-80">
                    <img class="block w-auto h-40 mx-auto rounded-xl" src="coleos-assets/product-blocks/medium-product-no-bg-5.png" alt="" data-config-id="img-679c65-2">
                  </div>
                  <div class="relative py-8 mt-auto text-center">
                    <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-rhino-700 uppercase rounded-full group-hover:bg-purple-500 transition duration-200" data-config-id="txt-679c65-6">Limited</span>
                    <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-7">Set of colorful t-shirts</span>
                    <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-8">$ 65.90</span>
                  </div>
                </a>
              </div>
              <div class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8">
                <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                  <div class="flex items-center justify-center h-80">
                    <img class="block w-full h-full rounded-xl" src="coleos-assets/product-blocks/vertical-product-3.png" alt="" data-config-id="img-679c65-3">
                  </div>
                  <div class="relative py-8 text-center">
                    <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full group-hover:bg-purple-500 transition duration-200" data-config-id="txt-679c65-9">New</span>
                    <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-10">Gray sport bag</span>
                    <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-11">$ 65.90</span>
                  </div>
                </a>
              </div>
              <div class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8">
                <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                  <div class="flex items-center justify-center h-80">
                    <img class="block w-full h-full rounded-xl" src="coleos-assets/product-blocks/vertical-product-1.png" alt="" data-config-id="img-679c65-4">
                  </div>
                  <div class="relative py-8 text-center">
                    <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full group-hover:bg-purple-500 group-hover:text-white transition duration-200" data-config-id="txt-679c65-12">Sale</span>
                    <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-13">Nike Sport Backpack</span>
                    <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-14">$ 65.90</span>
                  </div>
                </a>
              </div>
              <div :class="{'hidden': !showContent}" class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8 hidden">
                  <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                    <div class="flex items-center h-80">
                      <img class="block w-full h-80 rounded-xl" src="coleos-assets/product-blocks/vertical-product-2.png" alt="" data-config-id="img-679c65-5">
                    </div>
                    <div class="relative py-8 text-center">
                      <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full group-hover:bg-purple-500 transition duration-200" data-config-id="txt-679c65-15">New</span>
                      <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-16">Gray sport bag</span>
                      <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-17">$ 65.90</span>
                    </div>
                  </a>
                </div>
                <div :class="{'hidden': !showContent}" class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8 hidden">
                  <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                    <div class="flex items-center justify-center h-80">
                      <img class="block w-auto h-40 mx-auto rounded-xl" src="coleos-assets/product-blocks/medium-product-no-bg-5.png" alt="" data-config-id="img-679c65-6">
                    </div>
                    <div class="relative py-8 mt-auto text-center">
                      <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-rhino-700 uppercase rounded-full group-hover:bg-purple-500 transition duration-200" data-config-id="txt-679c65-18">Limited</span>
                      <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-19">Set of colorful t-shirts</span>
                      <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-20">$ 65.90</span>
                    </div>
                  </a>
                </div>
                <div :class="{'hidden': !showContent}" class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8 hidden">
                  <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                    <div class="flex items-center justify-center h-80">
                      <img class="block w-full h-full rounded-xl" src="coleos-assets/product-blocks/vertical-product-3.png" alt="" data-config-id="img-679c65-7">
                    </div>
                    <div class="relative py-8 text-center">
                      <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full group-hover:bg-purple-500 transition duration-200" data-config-id="txt-679c65-21">New</span>
                      <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-22">Gray sport bag</span>
                      <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-23">$ 65.90</span>
                    </div>
                  </a>
                </div>
                <div :class="{'hidden': !showContent}" class="w-full md:w-1/2 lg:w-1/4 px-4 mb-8 hidden">
                  <a class="relative group block max-w-xs mx-auto md:max-w-none bg-coolGray-100 rounded-xl overflow-hidden" href="#">
                    <div class="flex items-center justify-center h-80">
                      <img class="block w-full h-full rounded-xl" src="coleos-assets/product-blocks/vertical-product-1.png" alt="" data-config-id="img-679c65-8">
                    </div>
                    <div class="relative py-8 text-center">
                      <span class="absolute top-0 left-1/2 transform -translate-x-1/2 -translate-y-1/2 inline-block py-1 px-3 mr-2 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full group-hover:bg-purple-500 group-hover:text-white transition duration-200" data-config-id="txt-679c65-24">Sale</span>
                      <span class="block text-xl font-semibold text-rhino-800 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-25">Nike Sport Backpack</span>
                      <span class="block text-base text-rhino-300 group-hover:text-purple-500 transition duration-200" data-config-id="txt-679c65-26">$ 65.90</span>
                    </div>
                  </a>
                </div>
            </div>
          </div>
        </div>
      </section>