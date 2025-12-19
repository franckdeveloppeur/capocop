<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-6 font-heading text-center">{{ __('auth.reset_password_title') }}</h2>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <div class="mb-4">
                <x-label for="email" value="{{ __('auth.email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <x-label for="password" value="{{ __('auth.new_password') }}" />
                <x-password-input id="password" name="password" required autocomplete="new-password" :placeholder="__('auth.placeholder_password')" />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <x-label for="password_confirmation" value="{{ __('auth.confirm_password') }}" />
                <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :placeholder="__('auth.placeholder_confirm_password')" />
                @error('password_confirmation')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <x-button class="w-full">{{ __('auth.reset_password_button') }}</x-button>
        </form>
    </x-auth-panel>
</x-guest-layout>
