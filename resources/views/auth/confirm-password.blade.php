<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('auth.confirm_password_title') }}</h2>
        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('auth.confirm_password_description') }}</p>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-6">
                <x-label for="password" value="{{ __('auth.password') }}" />
                <x-password-input id="password" name="password" required autocomplete="current-password" autofocus :placeholder="__('auth.placeholder_password')" />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-button class="w-full">{{ __('auth.confirm') }}</x-button>
        </form>
    </x-auth-panel>
</x-guest-layout>
