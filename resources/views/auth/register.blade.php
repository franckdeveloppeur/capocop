<x-guest-layout>
    <x-auth-panel>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <x-label for="name" value="{{ __('auth.name') }}" />
                <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" :placeholder="__('auth.placeholder_name')" />
                @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <x-label for="email" value="{{ __('auth.email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" :placeholder="__('auth.placeholder_email')" />
                @error('email')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <x-label for="password" value="{{ __('auth.password') }}" />
                <x-password-input id="password" name="password" required autocomplete="new-password" :placeholder="__('auth.placeholder_password')" />
                @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-6">
                <x-label for="password_confirmation" value="{{ __('auth.confirm_password') }}" />
                <x-password-input id="password_confirmation" name="password_confirmation" required autocomplete="new-password" :placeholder="__('auth.placeholder_confirm_password')" />
                @error('password_confirmation')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mb-6 flex items-start gap-3">
                    <x-checkbox name="terms" id="terms" required />
                    <label for="terms" class="text-sm text-coolGray-700">
                        {{ __('auth.i_agree_to') }}
                        <a target="_blank" href="{{ route('terms.show') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.terms_of_service') }}</a>
                        {{ __('auth.and') }}
                        <a target="_blank" href="{{ route('policy.show') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.privacy_policy') }}</a>
                    </label>
                </div>
            @endif

            <x-button class="w-full mb-4">{{ __('auth.register') }}</x-button>

            <p class="text-center text-sm text-coolGray-700">
                {{ __('auth.already_registered') }}
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">{{ __('auth.sign_in') }}</a>
            </p>
        </form>
    </x-auth-panel>
</x-guest-layout>
