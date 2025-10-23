    
      <section class="bg-white py-12 md:py-24 lg:py-32" x-data="{ size: 'XS' }" x-init="size = $refs.size1.textContent">
        <div class="container px-4 mx-auto">
          <div class="px-4 md:px-0 max-w-lg mx-auto lg:max-w-5xl xl:max-w-7xl">
            <div class="mb-8">
              <div class="flex items-center flex-wrap gap-2">
                <div class="flex items-center gap-2">
                  <img src="{{ Vite::asset('resources/coleos-assets/logos/logo-coleos.png') }}" alt="">
                  <a class="text-rhino-500 text-sm hover:text-rhino-500 transition duration-200" href="#">Homepage</a>
                </div>
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
                    <path d="M15.1211 12C15.1212 12.1313 15.0954 12.2614 15.0451 12.3828C14.9948 12.5041 14.9211 12.6143 14.8281 12.707L10.5859 16.9497C10.3984 17.1372 10.1441 17.2426 9.87889 17.2426C9.6137 17.2426 9.35937 17.1372 9.17186 16.9497C8.98434 16.7622 8.879 16.5079 8.879 16.2427C8.879 15.9775 8.98434 15.7232 9.17186 15.5357L12.707 12L9.17183 8.46437C8.98431 8.27686 8.87897 8.02253 8.87897 7.75734C8.87897 7.49215 8.98431 7.23782 9.17183 7.05031C9.35934 6.86279 9.61367 6.75744 9.87886 6.75744C10.144 6.75744 10.3984 6.86279 10.5859 7.0503L14.8281 11.293C14.9211 11.3857 14.9949 11.4959 15.0451 11.6173C15.0954 11.7386 15.1212 11.8687 15.1211 12Z" fill="#A0A5B8"></path>
                  </svg>
                </div>
                <a class="text-rhino-500 text-sm hover:text-rhino-500 transition duration-200" href="#">Catalogue</a>
                <div>
                  <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
                    <path d="M15.1211 12C15.1212 12.1313 15.0954 12.2614 15.0451 12.3828C14.9948 12.5041 14.9211 12.6143 14.8281 12.707L10.5859 16.9497C10.3984 17.1372 10.1441 17.2426 9.87889 17.2426C9.6137 17.2426 9.35937 17.1372 9.17186 16.9497C8.98434 16.7622 8.879 16.5079 8.879 16.2427C8.879 15.9775 8.98434 15.7232 9.17186 15.5357L12.707 12L9.17183 8.46437C8.98431 8.27686 8.87897 8.02253 8.87897 7.75734C8.87897 7.49215 8.98431 7.23782 9.17183 7.05031C9.35934 6.86279 9.61367 6.75744 9.87886 6.75744C10.144 6.75744 10.3984 6.86279 10.5859 7.0503L14.8281 11.293C14.9211 11.3857 14.9949 11.4959 15.0451 11.6173C15.0954 11.7386 15.1212 11.8687 15.1211 12Z" fill="#A4AFBB"></path>
                  </svg>
                </div>
                <a class="text-rhino-300 text-sm hover:text-rhino-500 transition duration-200" href="#">Product</a>
              </div>
            </div>
            <div class="relative border border-coolGray-200 rounded-lg p-4 sm:p-9">
              <div class="flex flex-wrap -mx-4">
                <div class="w-full lg:w-1/2 xl:w-3/5 px-4 mb-12 lg:mb-0">
                  <div class="max-w-xl relative">
                    <img class="block w-full h-128 mb-6 lg:mb-0 rounded-xl object-cover" src="{{ Vite::asset('resources/coleos-assets/product-details/product-large.png') }}" alt="">
                    <div class="lg:absolute left-0 bottom-0 lg:-mb-48 xl:-mb-40 w-full">
                      <div class="flex flex-wrap -mx-1">
                        <div class="w-1/2 lg:w-1/4 p-1">
                          <button class="block h-28 xl:h-32 w-full lg:p-1.5 bg-white rounded-xl">
                            <img class="block w-full h-full object-cover rounded-xl" src="{{ Vite::asset('resources/coleos-assets/product-details/product-small.png') }}" alt="">
                          </button>
                        </div>
                        <div class="w-1/2 lg:w-1/4 p-1">
                          <button class="block h-28 xl:h-32 w-full lg:p-1.5 bg-white rounded-xl">
                            <img class="block w-full h-full object-cover rounded-xl" src="{{ Vite::asset('resources/coleos-assets/product-details/product-small2.png') }}" alt="">
                          </button>
                        </div>
                        <div class="w-1/2 lg:w-1/4 p-1">
                          <button class="block h-28 xl:h-32 w-full lg:p-1.5 bg-white rounded-xl">
                            <img class="block w-full h-full object-cover rounded-xl" src="{{ Vite::asset('resources/coleos-assets/product-details/product-small3.png') }}" alt="">
                          </button>
                        </div>
                        <div class="w-1/2 lg:w-1/4 p-1">
                          <button class="block h-28 xl:h-32 w-full lg:p-1.5 bg-white rounded-xl">
                            <img class="block w-full h-full object-cover rounded-xl" src="{{ Vite::asset('resources/coleos-assets/product-details/product-small4.png') }}" alt="">
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="w-full lg:w-1/2 xl:w-2/5 relative px-4">
                  <div class="max-w-md ml-auto">
                    <div class="absolute top-0 right-0 -mr-6 md:-mr-11">
                      <a href="#">
                        <div class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white mb-4 text-coolGray-700 hover:bg-coolGray-700 hover:text-white transition duration-200">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
                            <path d="M20.16 4.99992C19.1 3.93713 17.6948 3.28846 16.1983 3.17109C14.7019 3.05372 13.2128 3.47539 12 4.35992C10.7277 3.41356 9.14399 2.98443 7.56792 3.15896C5.99185 3.33348 4.54044 4.0987 3.50597 5.30051C2.47151 6.50231 1.93082 8.05144 1.9928 9.63594C2.05478 11.2204 2.71482 12.7226 3.84 13.8399L10.05 20.0599C10.57 20.5717 11.2704 20.8585 12 20.8585C12.7296 20.8585 13.43 20.5717 13.95 20.0599L20.16 13.8399C21.3276 12.6652 21.9829 11.0762 21.9829 9.41992C21.9829 7.76365 21.3276 6.17465 20.16 4.99992ZM18.75 12.4599L12.54 18.6699C12.4693 18.7413 12.3852 18.7979 12.2925 18.8366C12.1999 18.8752 12.1004 18.8951 12 18.8951C11.8996 18.8951 11.8001 18.8752 11.7075 18.8366C11.6148 18.7979 11.5307 18.7413 11.46 18.6699L5.25 12.4299C4.46576 11.6283 4.02661 10.5514 4.02661 9.42992C4.02661 8.30846 4.46576 7.23158 5.25 6.42992C6.04916 5.64091 7.12697 5.19849 8.25 5.19849C9.37303 5.19849 10.4508 5.64091 11.25 6.42992C11.343 6.52365 11.4536 6.59804 11.5754 6.64881C11.6973 6.69958 11.828 6.72572 11.96 6.72572C12.092 6.72572 12.2227 6.69958 12.3446 6.64881C12.4664 6.59804 12.577 6.52365 12.67 6.42992C13.4692 5.64091 14.547 5.19849 15.67 5.19849C16.793 5.19849 17.8708 5.64091 18.67 6.42992C19.465 7.22107 19.9186 8.29211 19.9335 9.41361C19.9485 10.5351 19.5236 11.6179 18.75 12.4299V12.4599Z" fill="currentColor"></path>
                          </svg>
                        </div>
                      </a>
                      <a href="#">
                        <div class="border border-coolGray-200 rounded-sm w-12 h-12 flex items-center justify-center bg-white text-coolGray-700 hover:bg-coolGray-700 hover:text-white transition duration-200">
                          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewbox="0 0 24 24" fill="none">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.8735 11.2887C10.5255 11.0877 10.3111 10.7164 10.3111 10.3145L10.3111 10.3145C10.3111 9.19581 10.7556 8.1231 11.5464 7.33204C12.3375 6.541 13.4104 6.09653 14.529 6.09653H17.1554L15.9829 4.92123C15.6985 4.63682 15.5874 4.22258 15.6916 3.83421C15.7956 3.44582 16.099 3.14238 16.4874 3.03843C16.8757 2.93427 17.29 3.04529 17.5744 3.3297L19.7087 5.46398C20.1745 5.92966 20.4362 6.56125 20.4362 7.2201C20.4362 7.87875 20.1745 8.51037 19.7087 8.97602L17.5744 11.1103C17.29 11.3947 16.8757 11.5057 16.4874 11.4016C16.099 11.2976 15.7955 10.9942 15.6916 10.6058C15.5874 10.2174 15.6984 9.80318 15.9829 9.51877L17.1554 8.34618H14.529C14.0069 8.34618 13.5063 8.55348 13.1372 8.92263C12.768 9.2918 12.5607 9.79253 12.5607 10.3145C12.5607 10.7164 12.3463 11.0877 11.9984 11.2887C11.6502 11.4896 11.2214 11.4896 10.8735 11.2887ZM6.37455 3.0033H10.0299L10.0301 3.00336C10.432 3.00336 10.8033 3.21774 11.0043 3.5657C11.2052 3.91363 11.2052 4.34242 11.0043 4.69039C10.8032 5.03854 10.432 5.25293 10.0301 5.25293H6.37466C6.07631 5.25293 5.79014 5.3714 5.57928 5.58226C5.36823 5.79331 5.24975 6.0793 5.24975 6.37764V15.0947C5.24975 15.3929 5.36823 15.679 5.57928 15.8899C5.79013 16.101 6.07632 16.2194 6.37466 16.2194H8.2051C8.6525 16.2198 9.08128 16.3979 9.39747 16.7143L11.2365 18.5534C11.3691 18.6695 11.5396 18.7334 11.7159 18.7334C11.8923 18.7334 12.0625 18.6695 12.1953 18.5534L14.0344 16.7143C14.3504 16.3979 14.7793 16.2198 15.2265 16.2194H17.0599C17.3583 16.2194 17.6445 16.101 17.8553 15.8899C18.0664 15.6791 18.1849 15.3929 18.1849 15.0947V13.6887C18.1849 13.2868 18.3992 12.9155 18.7472 12.7145C19.0951 12.5136 19.5239 12.5136 19.8719 12.7145C20.22 12.9155 20.4344 13.2868 20.4344 13.6887V15.0947C20.4344 15.9896 20.0788 16.8479 19.446 17.4807C18.8132 18.1135 17.9549 18.4691 17.0599 18.4691L15.474 18.4689L13.7867 20.1561C13.2373 20.7028 12.4922 21.0065 11.7172 20.9998H11.5907C10.8281 20.9548 10.1104 20.6245 9.58018 20.0747L7.97451 18.4691H6.37455C5.47949 18.4691 4.62119 18.1135 3.9884 17.4807C3.35561 16.8479 3 15.9895 3 15.0947V6.37765C3 5.48259 3.35561 4.62449 3.9884 3.9917C4.62119 3.35871 5.47955 3.0033 6.37455 3.0033Z" fill="currentColor"></path>
                          </svg>
                        </div>
                      </a>
                    </div>
                    <div class="inline-block mb-4 bg-orange-500 rounded-full px-4 py-1 text-center uppercase text-white text-xs font-bold tracking-widest">In stock</div>
                    <h1 class="mb-4 font-heading text-3xl sm:text-4xl text-rhino-700 font-semibold">
      </h1>
                    <p class="mb-6 text-rhino-400 text-sm font-medium">Pariatur ex aliqua elit ut enim consequat amet non do ut. Ad aute deserunt fugiat qui Lorem in quis velit labore voluptate.</p>
                    <div class="mb-8">
                      <p class="uppercase text-xs font-bold text-rhino-500 mb-3">SIZE</p>
                      <div class="flex flex-wrap -mx-1 -mb-1">
                        <div class="w-1/3 md:w-1/6 px-1 mb-1">
                          <div x-ref="size1" :class="{'border-purple-500 text-purple-700': $refs.size1.textContent == size, 'text-coolGray-700 hover:text-purple-700 border-coolGray-200 hover:border-purple-500': $refs.size1.textContent != size}" class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 border-purple-500 text-purple-700" x-on:click="size = $refs.size1.textContent">XS</div>
                        </div>
                        <div class="w-1/3 md:w-1/6 px-1 mb-1">
                          <div x-ref="size2" :class="{'border-purple-500 text-purple-700': $refs.size2.textContent == size, 'text-coolGray-700 border-coolGray-200': $refs.size2.textContent != size}" class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 border-coolGray-200 hover:border-purple-500 text-coolGray-700 hover:text-purple-700" x-on:click="size = $refs.size2.textContent">S</div>
                        </div>
                        <div class="w-1/3 md:w-1/6 px-1 mb-1">
                          <div x-ref="size3" :class="{'border-purple-500 text-purple-700': $refs.size3.textContent == size, 'text-coolGray-700 border-coolGray-200': $refs.size3.textContent != size}" class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 border-coolGray-200 hover:border-purple-500 text-coolGray-700 hover:text-purple-700" x-on:click="size = $refs.size3.textContent">M</div>
                        </div>
                        <div class="w-1/3 md:w-1/6 px-1 mb-1">
                          <div x-ref="size4" :class="{'border-purple-500 text-purple-700': $refs.size4.textContent == size, 'text-coolGray-700 border-coolGray-200': $refs.size4.textContent != size}" class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 border-coolGray-200 hover:border-purple-500 text-coolGray-700 hover:text-purple-700" x-on:click="size = $refs.size4.textContent">L</div>
                        </div>
                        <div class="w-1/3 md:w-1/6 px-1 mb-1">
                          <div x-ref="size5" :class="{'border-purple-500 text-purple-700': $refs.size5.textContent == size, 'text-coolGray-700 border-coolGray-200': $refs.size5.textContent != size}" class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 border-coolGray-200 hover:border-purple-500 text-coolGray-700 hover:text-purple-700" x-on:click="size = $refs.size5.textContent">XL</div>
                        </div>
                        <div class="w-1/3 md:w-1/6 px-1 mb-1">
                          <div x-ref="size6" :class="{'border-purple-500 text-purple-700': $refs.size6.textContent == size, 'text-coolGray-700 border-coolGray-200': $refs.size6.textContent != size}" class="w-full border py-2 rounded-sm text-center text-sm cursor-pointer transition duration-200 border-coolGray-200 hover:border-purple-500 text-coolGray-700 hover:text-purple-700" x-on:click="size = $refs.size6.textContent">XXL</div>
                        </div>
                      </div>
                    </div>
                    <div class="mb-6">
                      <div class="py-3 border-b border-t border-coolGray-200" x-data="{ accordion: false }">
                        <div class="flex items-center flex-wrap justify-between gap-4 cursor-pointer" x-on:click="accordion = !accordion">
                          <p class="uppercase text-coolGray-700 font-bold text-xs tracking-widest">Choose color</p>
                          <span class="inline-block transform rotate-0" :class="{'rotate-180': accordion, 'rotate-0': !accordion }">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewbox="0 0 24 25" fill="none">
                              <path d="M12.2725 16.1666C12.1769 16.1667 12.0822 16.1479 11.9939 16.1113C11.9055 16.0747 11.8253 16.021 11.7578 15.9533L6.21332 10.4092C6.07681 10.2727 6.00012 10.0876 6.00012 9.89454C6.00012 9.70149 6.07681 9.51635 6.21332 9.37984C6.34983 9.24333 6.53497 9.16665 6.72802 9.16665C6.92107 9.16665 7.10621 9.24334 7.24271 9.37984L12.2725 14.4092L17.3023 9.37982C17.4388 9.24332 17.6239 9.16663 17.817 9.16663C18.01 9.16663 18.1952 9.24331 18.3317 9.37982C18.4682 9.51632 18.5449 9.70147 18.5449 9.89452C18.5449 10.0876 18.4682 10.2727 18.3317 10.4092L12.7872 15.9534C12.7197 16.0211 12.6394 16.0748 12.5511 16.1114C12.4628 16.148 12.3681 16.1667 12.2725 16.1666Z" fill="#A0A5B8"></path>
                            </svg>
                          </span>
                        </div>
                        <div x-ref="container" :style="accordion ? 'height: ' + $refs.container.scrollHeight + 'px' : ''" class="overflow-hidden h-0 duration-500" style="">
                          <p class="text-rhino-500 leading-7 text-sm mt-3">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab asperiores placeat officiis beatae tenetur, perferendis aliquam, voluptas laudantium nihil ut ex illo autem quibusdam</p>
                        </div>
                      </div>
                      <div class="py-3 border-b border-coolGray-200" x-data="{ accordion: false }">
                        <div class="flex items-center flex-wrap justify-between gap-4 cursor-pointer" x-on:click="accordion = !accordion">
                          <p class="uppercase text-coolGray-700 font-bold text-xs tracking-widest">options</p>
                          <span class="inline-block transform rotate-0" :class="{'rotate-180': accordion, 'rotate-0': !accordion }">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewbox="0 0 24 25" fill="none">
                              <path d="M12.2725 16.1666C12.1769 16.1667 12.0822 16.1479 11.9939 16.1113C11.9055 16.0747 11.8253 16.021 11.7578 15.9533L6.21332 10.4092C6.07681 10.2727 6.00012 10.0876 6.00012 9.89454C6.00012 9.70149 6.07681 9.51635 6.21332 9.37984C6.34983 9.24333 6.53497 9.16665 6.72802 9.16665C6.92107 9.16665 7.10621 9.24334 7.24271 9.37984L12.2725 14.4092L17.3023 9.37982C17.4388 9.24332 17.6239 9.16663 17.817 9.16663C18.01 9.16663 18.1952 9.24331 18.3317 9.37982C18.4682 9.51632 18.5449 9.70147 18.5449 9.89452C18.5449 10.0876 18.4682 10.2727 18.3317 10.4092L12.7872 15.9534C12.7197 16.0211 12.6394 16.0748 12.5511 16.1114C12.4628 16.148 12.3681 16.1667 12.2725 16.1666Z" fill="#A0A5B8"></path>
                            </svg>
                          </span>
                        </div>
                        <div x-ref="container" :style="accordion ? 'height: ' + $refs.container.scrollHeight + 'px' : ''" class="overflow-hidden h-0 duration-500" style="">
                          <p class="text-rhino-500 leading-7 text-sm mt-3">Lorem ipsum dolor sit amet consectetur adipisicing elit. Ab asperiores placeat officiis beatae tenetur, perferendis aliquam, voluptas laudantium nihil ut ex illo autem quibusdam</p>
                        </div>
                      </div>
                    </div>
                    <h2 class="text-rhino-700 text-4xl font-semibold font-heading mb-6">$109,79</h2>
                    <a class="inline-block w-full px-3 py-4 rounded-sm text-center text-white text-sm font-medium bg-purple-500 hover:bg-purple-600 transition duration-200" href="#">Add to cart</a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>