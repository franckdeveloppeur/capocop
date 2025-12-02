<section class="bg-white py-12 md:py-24 lg:py-32" x-data="{ showContent: false }">
        <div class="container px-4 mx-auto">
          <div class="flex flex-wrap -mx-4 mb-14 justify-between">
            <div class="w-full md:w-1/2 px-4 mb-12 md:mb-0">
              <h2 class="text-4xl font-heading font-semibold text-rhino-600 tracking-xs" data-config-id="text9">les meilleures ventes</h2>
            </div>
            <div class="w-full md:w-1/2 px-4 md:text-right"><a x-on:click.prevent="showContent=true" :class="{ 'hidden': showContent }" class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-purple-500 hover:text-white bg-white border border-purple-500 rounded-sm hover:bg-purple-500 transition duration-200" href="#" data-config-id="text30">Show more</a></div>
          </div>
          <div class="flex flex-wrap -mx-4">
            <div class="w-full xl:w-2/3 px-4 mb-8">
              <div class="flex flex-wrap -mx-4">
                <div class="w-full sm:w-1/2 px-4 mb-8">
                  <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150" href="#">
                    <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full" data-config-id="text10">Sale</span>
                    <img class="absolute top-0 left-1/2 mt-16 transform -translate-x-1/2" src="coleos-assets/product-blocks/product-no-bg2.png" alt="" data-config-id="image1">
                    <div class="relative z-10 w-full px-8 mt-auto text-center">
                      <span class="block text-base text-rhino-500 mb-1" data-config-id="text11">Summer Slim Shorts</span>
                      <span class="block text-base text-rhino-300" data-config-id="text12">$ 79.00</span>
                    </div>
                  </a>
                </div>
                <div class="w-full sm:w-1/2 px-4 mb-8">
                  <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150" href="#">
                    <img class="absolute top-0 left-1/2 mt-4 transform -translate-x-1/2" src="coleos-assets/product-blocks/product-no-bg3.png" alt="" data-config-id="image2">
                    <div class="relative z-10 w-full px-8 mt-auto text-center">
                      <span class="block text-base text-rhino-500 mb-1" data-config-id="text13">White Label Cap</span>
                      <span class="block text-base text-rhino-300" data-config-id="text14">$ 199.00</span>
                    </div>
                  </a>
                </div>
                <div class="w-full px-4">
                  <div class="relative rounded-xl bg-yellow-300 overflow-hidden">
                    <div class="relative z-10 px-6 py-20 sm:py-6">
                      <h3 class="text-4xl font-heading font-semibold mb-14">
                        <span class="block" data-config-id="text15">Brand new</span>
                        <span class="block" data-config-id="text16">2026</span>
                        <span class="block" data-config-id="text17">collection</span>
                      </h3>
                      <a class="inline-flex h-12 py-2 px-4 items-center justify-center text-sm font-medium text-white hover:text-purple-500 bg-purple-500 hover:bg-white rounded-sm transition duration-200" href="#" data-config-id="text31">Go to collection</a>
                    </div>
                    <img class="absolute top-0 right-0 m-2" src="coleos-assets/product-blocks/arrow.png" alt="" data-config-id="image3">
                    <img class="hidden sm:block absolute top-0 h-full right-0 mr-8 lg:mr-32" src="coleos-assets/product-blocks/man-bg-center.png" alt="" data-config-id="image4">
                  </div>
                </div>
              </div>
            </div>
            <div class="w-full xl:w-1/3 px-4 mb-8">
              <a class="relative flex flex-col items-start h-128 xl:h-full py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150" href="#">
                <div>
                  <span class="relative z-10 inline-block py-1 px-3 mr-2 text-2xs text-white font-bold bg-orange-500 uppercase rounded-full" data-config-id="text18">New</span>
                  <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full" data-config-id="text19">Sale</span>
                </div>
                <img class="absolute top-0 left-1/2 mt-5 transform -translate-x-1/2 h-128 object-contain" src="coleos-assets/product-blocks/product-circle-2.png" alt="" data-config-id="image5">
                <div class="relative z-10 w-full mt-auto">
                  <span class="block text-base text-rhino-500 mb-1" data-config-id="text20">Blue High School Hoodie</span>
                  <span class="block text-base text-rhino-300" data-config-id="text21">$ 199.00</span>
                </div>
              </a>
            </div>
            <div :class="{'hidden': !showContent}" class="w-full xl:w-1/3 px-4 mb-8 hidden">
              <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150" href="#">
                <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full" data-config-id="text22">Sale</span>
                <img class="absolute top-0 left-1/2 mt-16 transform -translate-x-1/2" src="coleos-assets/product-blocks/product-no-bg2.png" alt="" data-config-id="image6">
                <div class="relative z-10 w-full px-8 mt-auto text-center">
                  <span class="block text-base text-rhino-500 mb-1" data-config-id="text23">Summer Slim Shorts</span>
                  <span class="block text-base text-rhino-300" data-config-id="text24">$ 79.00</span>
                </div>
              </a>
            </div>
            <div :class="{'hidden': !showContent}" class="w-full xl:w-1/3 px-4 mb-8 hidden">
              <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150" href="#">
                <img class="absolute top-0 left-1/2 mt-4 transform -translate-x-1/2" src="coleos-assets/product-blocks/product-no-bg3.png" alt="" data-config-id="image7">
                <div class="relative z-10 w-full px-8 mt-auto text-center">
                  <span class="block text-base text-rhino-500 mb-1" data-config-id="text25">White Label Cap</span>
                  <span class="block text-base text-rhino-300" data-config-id="text26">$ 199.00</span>
                </div>
              </a>
            </div>
            <div :class="{'hidden': !showContent}" class="w-full xl:w-1/3 px-4 mb-8 hidden">
              <a class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl border-2 border-transparent hover:border-purple-500 transition duration-150" href="#">
                <span class="relative z-10 inline-block py-1 px-3 text-2xs text-rhino-700 font-bold bg-white uppercase rounded-full" data-config-id="text27">Sale</span>
                <img class="absolute top-0 left-1/2 mt-16 transform -translate-x-1/2" src="coleos-assets/product-blocks/product-no-bg2.png" alt="" data-config-id="image8">
                <div class="relative z-10 w-full px-8 mt-auto text-center">
                  <span class="block text-base text-rhino-500 mb-1" data-config-id="text28">Summer Slim Shorts</span>
                  <span class="block text-base text-rhino-300" data-config-id="text29">$ 79.00</span>
                </div>
              </a>
            </div>
          </div>
        </div>
      </section>