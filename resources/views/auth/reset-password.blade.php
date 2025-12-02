<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-6 font-heading text-center">{{ __('Reset Password') }}</h2>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" placeholder="example@domain.com" />
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-password-input id="password" name="password" required autocomplete="new-password" placeholder="Password" />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="Confirm password" />
                @error('password_confirmation')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-button class="w-full">{{ __('Reset Password') }}</x-button>
        </form>
    </x-auth-panel>
</x-guest-layout>
