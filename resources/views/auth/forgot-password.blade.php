<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('auth.forgot_password_title') }}</h2>
        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('auth.forgot_password_description') }}</p>

        @if (session('status'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-sm">{{ __('auth.reset_link_sent') }}</div>
        @endif

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="mb-4">
                <x-label for="email" value="{{ __('auth.email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-button class="w-full">{{ __('auth.send_reset_link') }}</x-button>

            <p class="text-center text-sm text-coolGray-700 mt-4">
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.back_to_login') }}</a>
            </p>
        </form>
    </x-auth-panel>
</x-guest-layout>
