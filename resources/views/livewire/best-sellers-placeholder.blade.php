<section class="bg-white py-12 md:py-24 lg:py-32">
    <div class="container px-4 mx-auto">
        {{-- Header Skeleton --}}
        <div class="flex flex-wrap -mx-4 mb-14 justify-between">
            <div class="w-full md:w-1/2 px-4 mb-12 md:mb-0">
                <div class="h-10 w-64 bg-gray-200 rounded-lg animate-pulse"></div>
            </div>
            <div class="w-full md:w-1/2 px-4 md:text-right">
                <div class="h-12 w-32 bg-gray-200 rounded-sm animate-pulse inline-block"></div>
            </div>
        </div>

        {{-- Products Grid Skeleton --}}
        <div class="flex flex-wrap -mx-4">
            {{-- Section principale (2/3) --}}
            <div class="w-full xl:w-2/3 px-4 mb-8">
                <div class="flex flex-wrap -mx-4">
                    {{-- Carte produit 1 --}}
                    <div class="w-full sm:w-1/2 px-4 mb-8">
                        <div class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl animate-pulse">
                            <div class="h-6 w-16 bg-gray-300 rounded-full mb-4"></div>
                            <div class="absolute top-0 left-1/2 mt-16 transform -translate-x-1/2">
                                <div class="w-28 h-28 bg-gray-300 rounded-lg"></div>
                            </div>
                            <div class="relative z-10 w-full px-8 mt-auto text-center space-y-2">
                                <div class="h-5 w-32 bg-gray-300 rounded mx-auto"></div>
                                <div class="h-4 w-24 bg-gray-300 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Carte produit 2 --}}
                    <div class="w-full sm:w-1/2 px-4 mb-8">
                        <div class="relative flex flex-col items-start h-72 py-6 px-6 bg-coolGray-100 rounded-xl animate-pulse">
                            <div class="absolute top-0 left-1/2 mt-8 transform -translate-x-1/2">
                                <div class="w-32 h-32 bg-gray-300 rounded-lg"></div>
                            </div>
                            <div class="relative z-10 w-full px-8 mt-auto text-center space-y-2">
                                <div class="h-5 w-28 bg-gray-300 rounded mx-auto"></div>
                                <div class="h-4 w-20 bg-gray-300 rounded mx-auto"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Banner CTA Skeleton --}}
                    <div class="w-full px-4">
                        <div class="relative rounded-xl bg-gradient-to-r from-gray-200 to-gray-300 overflow-hidden animate-pulse">
                            <div class="relative z-10 px-6 py-20 sm:py-6">
                                <div class="space-y-3 mb-14">
                                    <div class="h-10 w-40 bg-gray-400/30 rounded"></div>
                                    <div class="h-10 w-28 bg-gray-400/30 rounded"></div>
                                    <div class="h-10 w-36 bg-gray-400/30 rounded"></div>
                                </div>
                                <div class="h-12 w-40 bg-gray-400/30 rounded-sm"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Produit vedette (1/3) --}}
            <div class="w-full xl:w-1/3 px-4 mb-8">
                <div class="relative flex flex-col items-start h-128 xl:h-full py-6 px-6 bg-coolGray-100 rounded-xl animate-pulse min-h-[32rem]">
                    <div class="flex gap-2">
                        <div class="h-6 w-14 bg-gray-300 rounded-full"></div>
                        <div class="h-6 w-14 bg-gray-300 rounded-full"></div>
                    </div>
                    <div class="absolute top-0 left-1/2 mt-20 transform -translate-x-1/2">
                        <div class="w-48 h-64 bg-gray-300 rounded-lg"></div>
                    </div>
                    <div class="relative z-10 w-full mt-auto space-y-2">
                        <div class="h-5 w-40 bg-gray-300 rounded"></div>
                        <div class="h-4 w-28 bg-gray-300 rounded"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Loading indicator --}}
        <div class="flex items-center justify-center mt-8">
            <div class="flex items-center gap-3 text-gray-400">
                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm font-medium">Chargement des meilleures ventes...</span>
            </div>
        </div>
    </div>
</section>





