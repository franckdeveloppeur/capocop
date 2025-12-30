<?php

namespace App\Filament\MyAccount\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;
use Livewire\Attributes\On;
use Carbon\Carbon;
use BackedEnum;

class Profile extends Page
{
    protected string $view = 'filament.my-account.pages.profile';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;

    protected static ?string $navigationLabel = 'Mon Profil';

    protected static ?int $navigationSort = 100;

    // Profile information
    public string $name = '';
    public string $email = '';
    public $photo;

    // Password update
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // 2FA
    public bool $enabled = false;
    public bool $showingQrCode = false;
    public bool $showingRecoveryCodes = false;
    public bool $showingConfirmation = false;
    public string $code = '';

    // Browser sessions
    public array $sessions = [];
    public bool $confirmingLogout = false;
    public string $logoutPassword = '';

    // Account deletion
    public bool $confirmingUserDeletion = false;
    public string $deletePassword = '';

    public bool $verificationLinkSent = false;

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->enabled = $user->two_factor_enabled ?? false;
        $this->loadSessions();
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);

        if (isset($validated['photo'])) {
            $user->updateProfilePhoto($validated['photo']);
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        if ($validated['email'] !== $user->getOriginal('email') && $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail) {
            $user->sendEmailVerificationNotification();
            $this->verificationLinkSent = true;
        }

        $this->dispatch('saved');
    }

    public function deleteProfilePhoto(): void
    {
        Auth::user()->deleteProfilePhoto();
    }

    public function sendEmailVerification(): void
    {
        Auth::user()->sendEmailVerificationNotification();
        $this->verificationLinkSent = true;
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            throw ValidationException::withMessages([
                'current_password' => [__('The provided password does not match your current password.')],
            ]);
        }

        Auth::user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);
        $this->dispatch('saved');
    }

    public function enableTwoFactorAuthentication(): void
    {
        $user = Auth::user();
        
        // Utiliser les méthodes Jetstream si disponibles
        if (method_exists($user, 'enableTwoFactorAuthentication')) {
            $user->enableTwoFactorAuthentication();
        } else {
            // Fallback manuel
            if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
                $user->forceFill([
                    'two_factor_secret' => encrypt((new \PragmaRX\Google2FA\Google2FA)->generateSecretKey()),
                    'two_factor_recovery_codes' => encrypt(json_encode(
                        \Illuminate\Support\Collection::times(8, fn () => \Illuminate\Support\Str::random(10))->all()
                    )),
                ])->save();
            }
        }

        $this->enabled = true;
        $this->showingQrCode = true;
        $this->showingConfirmation = true;
    }

    public function confirmTwoFactorAuthentication(): void
    {
        if (!$this->code) {
            throw ValidationException::withMessages([
                'code' => [__('The two factor authentication code cannot be empty.')],
            ]);
        }

        $user = Auth::user();
        
        // Utiliser les méthodes Jetstream si disponibles
        if (method_exists($user, 'confirmTwoFactorAuthentication')) {
            $user->confirmTwoFactorAuthentication($this->code);
        } else {
            // Fallback manuel
            if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
                $valid = (new \PragmaRX\Google2FA\Google2FA)->verifyKey(
                    decrypt($user->two_factor_secret),
                    $this->code
                );

                if (!$valid) {
                    throw ValidationException::withMessages([
                        'code' => [__('The provided two factor authentication code was invalid.')],
                    ]);
                }

                $user->forceFill([
                    'two_factor_enabled' => true,
                ])->save();
            }
        }

        $this->showingConfirmation = false;
        $this->showingRecoveryCodes = true;
    }

    public function disableTwoFactorAuthentication(): void
    {
        $user = Auth::user();
        $user->forceFill([
            'two_factor_enabled' => false,
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        $this->enabled = false;
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->showingConfirmation = false;
    }

    public function showRecoveryCodes(): void
    {
        $this->showingRecoveryCodes = true;
    }

    public function regenerateRecoveryCodes(): void
    {
        $user = Auth::user();
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode(
                \Illuminate\Support\Collection::times(8, fn () => \Illuminate\Support\Str::random(10))->all()
            )),
        ])->save();

        $this->showingRecoveryCodes = true;
    }

    public function getTwoFactorQrCodeSvg(): string
    {
        $user = Auth::user();
        // Utiliser la méthode de Jetstream si disponible
        if (method_exists($user, 'twoFactorQrCodeSvg')) {
            return $user->twoFactorQrCodeSvg();
        }
        
        // Fallback : utiliser Google2FA directement si disponible
        if (class_exists(\PragmaRX\Google2FA\Google2FA::class)) {
            $qrCodeUrl = (new \PragmaRX\Google2FA\Google2FA)->getQRCodeUrl(
                config('app.name'),
                $user->email,
                decrypt($user->two_factor_secret)
            );
            
            return '<img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($qrCodeUrl) . '" alt="QR Code" />';
        }
        
        return '<div class="text-sm text-gray-600">QR Code non disponible</div>';
    }

    public function loadSessions(): void
    {
        if (config('session.driver') !== 'database') {
            $this->sessions = [];
            return;
        }

        try {
            $sessions = \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
                ->where('user_id', Auth::id())
                ->orderBy('last_activity', 'desc')
                ->get();

            $this->sessions = $sessions->map(function ($session) {
                $agentInfo = $this->parseUserAgent($session->user_agent ?? null);
                return [
                    'agent' => $agentInfo,
                    'ip_address' => $session->ip_address ?? 'N/A',
                    'is_current_device' => ($session->id ?? null) === session()->getId(),
                    'last_active' => isset($session->last_activity) 
                        ? Carbon::createFromTimestamp($session->last_activity)->diffForHumans() 
                        : 'N/A',
                ];
            })->toArray();
        } catch (\Exception $e) {
            $this->sessions = [];
        }
    }

    protected function parseUserAgent(?string $userAgent): array
    {
        $userAgent = $userAgent ?? request()->userAgent();
        
        $isDesktop = true;
        $platform = 'Inconnu';
        $browser = 'Inconnu';

        $mobileAgents = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
        foreach ($mobileAgents as $mobile) {
            if (stripos($userAgent, $mobile) !== false) {
                $isDesktop = false;
                break;
            }
        }

        if (stripos($userAgent, 'Windows') !== false) {
            $platform = 'Windows';
        } elseif (stripos($userAgent, 'Mac') !== false || stripos($userAgent, 'Darwin') !== false) {
            $platform = 'macOS';
        } elseif (stripos($userAgent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (stripos($userAgent, 'Android') !== false) {
            $platform = 'Android';
        } elseif (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            $platform = 'iOS';
        }

        if (stripos($userAgent, 'Chrome') !== false && stripos($userAgent, 'Edg') === false) {
            $browser = 'Chrome';
        } elseif (stripos($userAgent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (stripos($userAgent, 'Safari') !== false && stripos($userAgent, 'Chrome') === false) {
            $browser = 'Safari';
        } elseif (stripos($userAgent, 'Edg') !== false) {
            $browser = 'Edge';
        } elseif (stripos($userAgent, 'Opera') !== false || stripos($userAgent, 'OPR') !== false) {
            $browser = 'Opera';
        }

        return [
            'is_desktop' => $isDesktop,
            'platform' => $platform,
            'browser' => $browser,
        ];
    }

    public function confirmLogout(): void
    {
        $this->confirmingLogout = true;
    }

    public function logoutOtherBrowserSessions(): void
    {
        if (!Hash::check($this->logoutPassword, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'logoutPassword' => [__('This password does not match our records.')],
            ]);
        }

        \Illuminate\Support\Facades\DB::table(config('session.table', 'sessions'))
            ->where('user_id', Auth::id())
            ->where('id', '!=', session()->getId())
            ->delete();

        $this->confirmingLogout = false;
        $this->logoutPassword = '';
        $this->loadSessions();
        $this->dispatch('loggedOut');
    }

    public function confirmUserDeletion(): void
    {
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser(): void
    {
        if (!Hash::check($this->deletePassword, Auth::user()->password)) {
            throw ValidationException::withMessages([
                'deletePassword' => [__('This password does not match our records.')],
            ]);
        }

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        redirect()->to('/');
    }
}

