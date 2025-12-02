<x-guest-layout>
    <section data-section-id="1" data-share="" data-category="sign-in" data-component-id="fed93509_05_awz" class="relative overflow-hidden">
        <div class="relative flex flex-wrap">
            <div class="w-full lg:w-1/2 px-4">
                <div class="flex items-center justify-center w-full h-full">
                    <div class="max-w-sm pt-24 pb-20 lg:pt-20 mx-auto">
                        <x-validation-errors class="mb-4" />
                        @if (session('status'))
                            <div class="mb-4 font-medium text-sm text-green-600">{{ session('status') }}</div>
                        @endif

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <p class="uppercase text-rhino-300 text-xs font-bold tracking-widest mb-1 text-center">SIGN IN</p>
                            <h1 class="font-heading font-semibold text-4xl text-rhino-700 text-center mb-8">Join our community</h1>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="email">Email Address</label>
                                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="example@domain.com" />
                                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex flex-col gap-1 mb-4">
                                <label class="text-coolGray-700 text-sm font-medium mb-2" for="password">Password</label>
                                <x-password-input id="password" name="password" required autocomplete="current-password" placeholder="Password" />
                                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="flex items-center justify-between mb-6">
                                <label for="remember_me" class="flex items-center gap-2">
                                    <x-checkbox id="remember_me" name="remember" />
                                    <span class="text-sm text-coolGray-700">{{ __('Remember me') }}</span>
                                </label>
                                @if (Route::has('password.request'))
                                    <a class="text-sm text-indigo-600 hover:underline font-medium" href="{{ route('password.request') }}">{{ __('Forgot password?') }}</a>
                                @endif
                            </div>

                            <x-button class="rounded-sm py-3 px-4 bg-purple-500 shadow-md text-white font-medium text-sm w-full hover:bg-purple-600 focus:outline-none focus:ring-2 focus:ring-purple-400 focus:ring-offset-2 transition duration-200 mb-4">{{ __('Log in') }}</x-button>

                            <a class="mb-4 w-full rounded-sm border border-coolGray-200 py-3 px-6 flex items-center justify-center gap-4 text-coolGray-700 hover:bg-purple-500 hover:text-white transition duration-200" href="#">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                                    <circle cx="12.5" cy="12" r="12" fill="#DDDCFE"></circle>
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M13.044 18V12.5266H15.107L15.4165 10.3929H13.044V9.03088C13.044 8.41332 13.2358 7.99246 14.2318 7.99246L15.5 7.99199V6.08354C15.2807 6.05817 14.5278 6 13.6516 6C11.8219 6 10.5693 6.99418 10.5693 8.81956V10.3929H8.5V12.5266H10.5693V18H13.044Z" fill="#416BE6"></path>
                                </svg>
                                <span class="text-sm font-medium">Sign In with Facebook</span>
                            </a>

                            <a class="w-full rounded-sm border border-coolGray-200 py-3 px-6 flex items-center justify-center gap-4 text-coolGray-700 hover:bg-purple-500 hover:text-white transition duration-200" href="#">
                                <img src="/coleos-assets/logos/google-logo.svg" alt="Google logo">
                                <span class="text-sm font-medium">Sign In with Google</span>
                            </a>

                            <p class="text-center text-sm text-coolGray-700 mt-4">
                                {{ __('Don\'t have an account?') }}
                                <a href="{{ route('register') }}" class="text-indigo-600 hover:underline font-medium">{{ __('Sign up') }}</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            <div class="relative lg:absolute top-0 right-0 w-full lg:w-1/2 block h-112 lg:h-full bg-orange-200 overflow-hidden">
                <img class="absolute left-1/2 bottom-0 transform -translate-x-1/2 z-10 h-full lg:h-auto" src="/coleos-assets/sign-in/bg-image2.png" alt="">
                <img class="absolute left-1/2 top-1/2 transform -translate-y-1/2 -translate-x-1/2 h-full lg:h-auto" src="/coleos-assets/sign-in/white-circle-bg.png" alt="">
                <img class="absolute left-0 top-0" src="/coleos-assets/sign-in/white-circle-part.png" alt="">
                <img class="absolute right-0 bottom-0 z-30" src="/coleos-assets/sign-in/orange-triangle-small.png" alt="">
                <img class="absolute right-24 bottom-0 w-20 lg:w-auto z-20" src="/coleos-assets/sign-in/orange-triangle-large.png" alt="">
            </div>
        </div>
    </section>
</x-guest-layout>
