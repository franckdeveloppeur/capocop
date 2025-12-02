<x-guest-layout>
    <x-auth-panel>

        <h2 class="text-rhino-700 text-2xl font-semibold mb-4 font-heading text-center">{{ __('Verify Email') }}</h2>
        <p class="text-center text-sm text-coolGray-600 mb-6">{{ __('Before continuing, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-sm text-center">
                {{ __('A new verification link has been sent to the email address you provided in your profile settings.') }}
            </div>
        @endif

        <form method="POST" action="{{ route('verification.send') }}" class="mb-4">
            @csrf
            <x-button class="w-full">{{ __('Resend Verification Email') }}</x-button>
        </form>

        <div class="flex items-center justify-center gap-4 text-sm">
            <a href="{{ route('profile.show') }}" class="text-indigo-600 hover:underline font-medium">{{ __('Edit Profile') }}</a>
            <span class="text-coolGray-300">|</span>
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf
                <button type="submit" class="text-indigo-600 hover:underline font-medium">{{ __('Log Out') }}</button>
            </form>
        </div>
    </x-auth-panel>
</x-guest-layout>
