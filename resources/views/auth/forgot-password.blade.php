<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('Forgot Password') }}</h2>
        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('No problem. Just let us know your email address and we will email you a password reset link.') }}</p>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-sm">{{ session('status') }}</div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="example@domain.com" />
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-button class="w-full">{{ __('Email Password Reset Link') }}</x-button>

            <p class="text-center text-sm text-coolGray-700 mt-4">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">{{ __('Back to login') }}</a>
            </p>
        </form>
    </x-auth-panel>
</x-guest-layout>
