@extends('layouts._layout')
@section('body')
<section class="py-12">
    <div class="container px-4 mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-center gap-4 lg:gap-0 justify-between flex-wrap mb-6">
            <div>
                <h1 class="font-heading text-rhino-700 text-2xl font-semibold" data-config-id="txt-6110d8-1">10 produits en favoris trouves</h1>
                <p class="text-rhino-300" data-config-id="txt-6110d8-2">Summer sneakers</p>
            </div>
            <div class="flex gap-4 flex-wrap">
                <a class="rounded-sm border border-coolGray-200 py-2 px-4 flex items-center flex-wrap justify-between gap-12 hover:bg-coolGray-100 transition duration-200" href="#">
                    <div class="flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" data-config-id="svg-6110d8-1">
                            <path d="M10 17.5L10 2.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M5 17.5L5 2.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M15 17.5L15 2.5" stroke="#9CA3AF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                            <ellipse cx="5.00004" cy="7.49992" rx="1.66667" ry="1.66667" fill="#9CA3AF"></ellipse>
                            <ellipse cx="15" cy="7.49992" rx="1.66667" ry="1.66667" fill="#9CA3AF"></ellipse>
                            <ellipse cx="10" cy="13.3334" rx="1.66667" ry="1.66667" fill="#9CA3AF"></ellipse>
                        </svg>
                        <span class="text-sm text-coolGray-800 font-medium" data-config-id="txt-6110d8-27">Filters</span>
                    </div>
                    <div class="bg-rhino-600 py-1 px-3 text-center rounded-full flex items-center justify-center">
                        <span class="text-white text-xs font-bold block" data-config-id="txt-6110d8-28">2</span>
                    </div>
                </a>
                <div class="border border-gray-200 rounded-sm flex bg-white">
                    <a class="flex-1 py-1 px-5 flex items-center justify-center bg-coolGray-100 hover:bg-coolGray-200 transition duration-200" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" data-config-id="svg-6110d8-2">
                            <path d="M12.6667 0.333252H0.666667C0.489856 0.333252 0.320286 0.40349 0.195262 0.528514C0.0702379 0.653538 0 0.823108 0 0.999919V12.9999C0 13.1767 0.0702379 13.3463 0.195262 13.4713C0.320286 13.5963 0.489856 13.6666 0.666667 13.6666H12.6667C12.8435 13.6666 13.013 13.5963 13.1381 13.4713C13.2631 13.3463 13.3333 13.1767 13.3333 12.9999V0.999919C13.3333 0.823108 13.2631 0.653538 13.1381 0.528514C13.013 0.40349 12.8435 0.333252 12.6667 0.333252ZM4 12.3333H1.33333V9.66659H4V12.3333ZM4 8.33325H1.33333V5.66659H4V8.33325ZM4 4.33325H1.33333V1.66659H4V4.33325ZM8 12.3333H5.33333V9.66659H8V12.3333ZM8 8.33325H5.33333V5.66659H8V8.33325ZM8 4.33325H5.33333V1.66659H8V4.33325ZM12 12.3333H9.33333V9.66659H12V12.3333ZM12 8.33325H9.33333V5.66659H12V8.33325ZM12 4.33325H9.33333V1.66659H12V4.33325Z" fill="currentColor"></path>
                        </svg>
                    </a>
                    <a class="flex-1 py-1 px-5 flex items-center justify-center group hover:bg-coolGray-100 transition duration-200" href="#">
                        <div class="text-coolGray-400 group-hover:text-coolGray-800">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" data-config-id="svg-6110d8-3">
                                <path d="M12.6667 7.66659H1.00004C0.82323 7.66659 0.65366 7.73682 0.528636 7.86185C0.403612 7.98687 0.333374 8.15644 0.333374 8.33325V12.9999C0.333374 13.1767 0.403612 13.3463 0.528636 13.4713C0.65366 13.5963 0.82323 13.6666 1.00004 13.6666H12.6667C12.8435 13.6666 13.0131 13.5963 13.1381 13.4713C13.2631 13.3463 13.3334 13.1767 13.3334 12.9999V8.33325C13.3334 8.15644 13.2631 7.98687 13.1381 7.86185C13.0131 7.73682 12.8435 7.66659 12.6667 7.66659ZM12 12.3333H1.66671V8.99992H12V12.3333ZM12.6667 0.333252H1.00004C0.82323 0.333252 0.65366 0.40349 0.528636 0.528514C0.403612 0.653538 0.333374 0.823108 0.333374 0.999919V5.66659C0.333374 5.8434 0.403612 6.01297 0.528636 6.13799C0.65366 6.26301 0.82323 6.33325 1.00004 6.33325H12.6667C12.8435 6.33325 13.0131 6.26301 13.1381 6.13799C13.2631 6.01297 13.3334 5.8434 13.3334 5.66659V0.999919C13.3334 0.823108 13.2631 0.653538 13.1381 0.528514C13.0131 0.40349 12.8435 0.333252 12.6667 0.333252ZM12 4.99992H1.66671V1.66659H12V4.99992Z" fill="currentColor"></path>
                            </svg>
                        </div>
                    </a>
                </div>
                <select class="rounded-sm border border-coolGray-200 py-3 px-4 text-coolGray-400 text-sm outline-none" data-config-id="input-6110d8-1">
                    <option value="" data-config-id="txt-6110d8-29">
                        Sort by
                        Newest
                    </option>
                    <option value="" data-config-id="txt-6110d8-30">
                        Sort by
                        Limited
                    </option>
                    <option value="" data-config-id="txt-6110d8-31">
                        Sort by
                        Sale
                    </option>
                </select>
            </div>
        </div>
        <div class="flex flex-wrap -mx-4">
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-orange-500 py-1 px-3 rounded-full text-white text-xs font-bold text-center" data-config-id="div-6110d8-1">New</div>
                        <img src="coleos-assets/product-list/product1.png" alt="" data-config-id="img-6110d8-1">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-3">Nike Sport Shoes V2.04</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-4">$ 199.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-rhino-600 py-1 px-3 rounded-full text-white text-xs font-bold text-center" data-config-id="div-6110d8-2">Limited</div>
                        <img src="coleos-assets/product-list/product2.png" alt="" data-config-id="img-6110d8-2">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-5">White Label Cap</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-6">$ 48.99</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-white py-1 px-3 rounded-full text-rhino-700 text-xs font-bold text-center" data-config-id="div-6110d8-3">Sale</div>
                        <img src="coleos-assets/product-list/product3.png" alt="" data-config-id="img-6110d8-3">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-7">Nike Sport Shoes V2.04</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-8">$ 199.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <img src="coleos-assets/product-list/product4.png" alt="" data-config-id="img-6110d8-4">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-9">Summer Slim Shorts</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-10">$ 79.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <img src="coleos-assets/product-list/product5.png" alt="" data-config-id="img-6110d8-5">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-11">Nike Sport Shoes V2.04</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-12">$ 199.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-orange-500 py-1 px-3 rounded-full text-white text-xs font-bold text-center" data-config-id="div-6110d8-4">New</div>
                        <img src="coleos-assets/product-list/product6.png" alt="" data-config-id="img-6110d8-6">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-13">Brown Original 64’s Jacket</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-14">$ 249.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <img src="coleos-assets/product-list/product7.png" alt="" data-config-id="img-6110d8-7">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-15">Set of colorful t-shirts</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-16">$ 98.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-orange-500 py-1 px-3 rounded-full text-white text-xs font-bold text-center" data-config-id="div-6110d8-5">New</div>
                        <img src="coleos-assets/product-list/product8.png" alt="" data-config-id="img-6110d8-8">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-17">Blue High School Hoodie</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-18">$ 65.90</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-white py-1 px-3 rounded-full text-rhino-700 text-xs font-bold text-center" data-config-id="div-6110d8-6">Sale</div>
                        <img src="coleos-assets/product-list/product9.png" alt="" data-config-id="img-6110d8-9">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-19">BlackSeries Nike SuperSport</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-20">$ 319.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <img src="coleos-assets/product-list/product10.png" alt="" data-config-id="img-6110d8-10">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-21">Nike Sport Shoes V2.04</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-22">$ 199.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <img src="coleos-assets/product-list/product11.png" alt="" data-config-id="img-6110d8-11">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-23">TriBlend Crew T-Shirt</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-24">$ 199.00</p>
                </a>
            </div>
            <div class="w-full xs:w-1/2 md:w-1/3 lg:w-1/4 px-4">
                <a class="block mb-10 group" href="#">
                    <div class="w-full h-64 bg-coolGray-100 rounded-xl mb-3 flex items-center justify-center relative flex-1 p-6 border-2 border-transparent group-hover:border-purple-500 transition duration-150">
                        <div class="absolute left-5 top-5 uppercase bg-orange-500 py-1 px-3 rounded-full text-white text-xs font-bold text-center" data-config-id="div-6110d8-7">New</div>
                        <img src="coleos-assets/product-list/product12.png" alt="" data-config-id="img-6110d8-12">
                    </div>
                    <p class="text-rhino-700" data-config-id="txt-6110d8-25">Nike Sport Shoes V2.04</p>
                    <p class="text-rhino-300" data-config-id="txt-6110d8-26">$ 199.00</p>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
@section('headItems')
<title>mes produits favoris</title>
@endsection