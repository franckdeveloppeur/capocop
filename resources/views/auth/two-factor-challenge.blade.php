<x-guest-layout>
    <x-auth-panel>

        <div x-data="{ recovery: false }">
            <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('auth.two_factor_authentication') }}</h2>

            <div class="text-center text-sm text-coolGray-600 mb-6" x-show="!recovery">
                {{ __('auth.two_factor_description') }}
            </div>

            <div class="text-center text-sm text-coolGray-600 mb-6" x-cloak x-show="recovery">
                {{ __('auth.two_factor_recovery_description') }}
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('two-factor.login') }}">
                @csrf

                <div class="mb-6" x-show="!recovery">
                    <x-label for="code" value="{{ __('auth.code') }}" />
                    <x-input id="code" type="text" inputmode="numeric" name="code" autofocus x-ref="code" autocomplete="one-time-code" :placeholder="__('auth.placeholder_code')" />
                    @error('code')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="mb-6" x-cloak x-show="recovery">
                    <x-label for="recovery_code" value="{{ __('auth.recovery_code') }}" />
                    <x-input id="recovery_code" type="text" name="recovery_code" x-ref="recovery_code" autocomplete="one-time-code" :placeholder="__('auth.placeholder_recovery_code')" />
                    @error('recovery_code')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-between gap-4 mb-4">
                    <button type="button" class="text-sm text-indigo-600 hover:underline cursor-pointer font-medium" x-show="!recovery" @click="recovery = true; $nextTick(() => { $refs.recovery_code.focus() })">
                        {{ __('auth.use_recovery_code') }}
                    </button>

                    <button type="button" class="text-sm text-indigo-600 hover:underline cursor-pointer font-medium" x-cloak x-show="recovery" @click="recovery = false; $nextTick(() => { $refs.code.focus() })">
                        {{ __('auth.use_authentication_code') }}
                    </button>
                </div>

                <x-button class="w-full">{{ __('auth.verify') }}</x-button>
            </form>
        </div>
    </x-auth-panel>
</x-guest-layout>
