<x-guest-layout>
    <x-auth-panel>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="mb-4">
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Enter name" />
                @error('name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="example@domain.com" />
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

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mb-6 flex items-start gap-3">
                    <x-checkbox name="terms" id="terms" required />
                    <label for="terms" class="text-sm text-coolGray-700">
                        {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="text-indigo-600 hover:underline font-medium">'.__('Terms of Service').'</a>',
                                'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="text-indigo-600 hover:underline font-medium">'.__('Privacy Policy').'</a>',
                        ]) !!}
                    </label>
                </div>
            @endif

            <x-button class="w-full mb-4">{{ __('Register') }}</x-button>

            <p class="text-center text-sm text-coolGray-700">
                {{ __('Already registered?') }}
                <a href="{{ route('login') }}" class="text-indigo-600 hover:underline font-medium">{{ __('Sign in') }}</a>
            </p>
        </form>
    </x-auth-panel>
</x-guest-layout>
