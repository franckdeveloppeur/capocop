<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('Confirm Password') }}</h2>
        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('This is a secure area of the application. Please confirm your password before continuing.') }}</p>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-6">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-password-input id="password" name="password" required autocomplete="current-password" autofocus />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-button class="w-full">{{ __('Confirm') }}</x-button>
        </form>
    </x-auth-panel>
</x-guest-layout>
