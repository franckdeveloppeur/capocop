<?php

namespace App\Filament\Pages;

use BackedEnum;
use Carbon\Carbon;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;
use Laravel\Jetstream\Jetstream;

class Profile extends Page
{

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserCircle;
    protected string $view = 'filament.pages.profile';
    protected static ?string $title = 'Profil';
    protected static ?string $navigationLabel = 'Profil';
    protected static ?string $slug = 'profile';

    // État pour les informations de profil
    public $name;
    public $email;
    public $photo;
    public $photoPreview;
    public $verificationLinkSent = false;

    // État pour le mot de passe
    public $current_password;
    public $password;
    public $password_confirmation;

    // État pour la 2FA
    public $enabled = false;
    public $showingQrCode = false;
    public $showingRecoveryCodes = false;
    public $showingConfirmation = false;
    public $code;

    // État pour les sessions
    public $sessions = [];
    public $confirmingLogout = false;
    public $logoutPassword;

    // État pour la suppression
    public $confirmingUserDeletion = false;
    public $deletePassword;

    public function mount(): void
    {
        $user = Auth::user();
        
        $this->name = $user->name;
        $this->email = $user->email;
        $this->enabled = !is_null($user->two_factor_secret);
        $this->showingQrCode = !is_null($user->two_factor_secret) && !is_null($user->two_factor_confirmed_at);
        
        if (Features::canManageTwoFactorAuthentication()) {
            $this->loadSessions();
        }
    }


    public function updateProfileInformation(): void
    {
        $user = Auth::user();
        
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'photo' => ['nullable', 'image', 'max:1024'],
        ]);

        if (isset($this->photo)) {
            $user->updateProfilePhoto($this->photo);
        }

        $user->forceFill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ])->save();

        if ($user->wasChanged('email') && Features::enabled(Features::emailVerification())) {
            $user->sendEmailVerificationNotification();
            $this->verificationLinkSent = true;
        }

        Notification::make()
            ->title('Enregistré')
            ->success()
            ->send();

        $this->reset(['photo', 'photoPreview']);
    }

    public function deleteProfilePhoto(): void
    {
        Auth::user()->deleteProfilePhoto();

        Notification::make()
            ->title('Photo supprimée')
            ->success()
            ->send();
    }

    public function sendEmailVerification(): void
    {
        Auth::user()->sendEmailVerificationNotification();

        $this->verificationLinkSent = true;

        Notification::make()
            ->title('Lien de vérification envoyé')
            ->success()
            ->send();
    }

    public function updatePassword(): void
    {
        $validated = $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], Auth::user()->password)) {
            Notification::make()
                ->title('Le mot de passe actuel est incorrect')
                ->danger()
                ->send();
            return;
        }

        Auth::user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        Notification::make()
            ->title('Mot de passe mis à jour')
            ->success()
            ->send();
    }

    public function enableTwoFactorAuthentication(): void
    {
        $user = Auth::user();
        
        // Générer une clé secrète
        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $secret = $google2fa->generateSecretKey();
        
        // Générer des codes de récupération
        $recoveryCodes = collect(array_map(fn () => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10), range(1, 8)));
        
        $user->forceFill([
            'two_factor_secret' => encrypt($secret),
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes->toArray())),
        ])->save();

        $this->enabled = true;
        $this->showingQrCode = true;
        $this->showingConfirmation = true;

        Notification::make()
            ->title('Authentification à deux facteurs activée')
            ->success()
            ->send();
    }

    public function confirmTwoFactorAuthentication(): void
    {
        $user = Auth::user();
        
        if (empty($this->code)) {
            Notification::make()
                ->title('Le code est requis')
                ->danger()
                ->send();
            return;
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        
        if (!$google2fa->verifyKey(decrypt($user->two_factor_secret), $this->code)) {
            Notification::make()
                ->title('Code invalide')
                ->danger()
                ->send();
            return;
        }

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
        ])->save();

        $this->showingConfirmation = false;
        $this->code = '';

        Notification::make()
            ->title('Authentification à deux facteurs confirmée')
            ->success()
            ->send();
    }

    public function disableTwoFactorAuthentication(): void
    {
        $user = Auth::user();
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        $this->enabled = false;
        $this->showingQrCode = false;
        $this->showingRecoveryCodes = false;
        $this->showingConfirmation = false;

        Notification::make()
            ->title('Authentification à deux facteurs désactivée')
            ->success()
            ->send();
    }

    public function showRecoveryCodes(): void
    {
        $this->showingRecoveryCodes = true;
    }

    public function regenerateRecoveryCodes(): void
    {
        $user = Auth::user();
        
        // Générer de nouveaux codes de récupération
        $recoveryCodes = collect(array_map(fn () => \Illuminate\Support\Str::random(10) . '-' . \Illuminate\Support\Str::random(10), range(1, 8)));
        
        $user->forceFill([
            'two_factor_recovery_codes' => encrypt(json_encode($recoveryCodes->toArray())),
        ])->save();

        $this->showingRecoveryCodes = true;

        Notification::make()
            ->title('Codes de récupération régénérés')
            ->success()
            ->send();
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
                $userAgent = $session->user_agent ?? request()->userAgent();
                $agentInfo = $this->parseUserAgent($userAgent);
                
                return [
                    'is_desktop' => $agentInfo['is_desktop'],
                    'platform' => $agentInfo['platform'],
                    'browser' => $agentInfo['browser'],
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
        $userAgent = $userAgent ?? '';
        
        // Détecter si c'est un desktop
        $mobileAgents = ['Mobile', 'Android', 'iPhone', 'iPad', 'iPod', 'BlackBerry', 'Windows Phone'];
        $isDesktop = true;
        foreach ($mobileAgents as $mobile) {
            if (stripos($userAgent, $mobile) !== false) {
                $isDesktop = false;
                break;
            }
        }
        
        // Détecter la plateforme
        $platform = null;
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
        
        // Détecter le navigateur
        $browser = null;
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
        if (empty($this->logoutPassword)) {
            Notification::make()
                ->title('Le mot de passe est requis')
                ->danger()
                ->send();
            return;
        }

        if (!Hash::check($this->logoutPassword, Auth::user()->password)) {
            Notification::make()
                ->title('Mot de passe incorrect')
                ->danger()
                ->send();
            return;
        }

        Auth::logoutOtherDevices($this->logoutPassword);

        $this->confirmingLogout = false;
        $this->logoutPassword = '';

        Notification::make()
            ->title('Déconnexion effectuée')
            ->success()
            ->send();
    }

    public function confirmUserDeletion(): void
    {
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser(): void
    {
        if (empty($this->deletePassword)) {
            Notification::make()
                ->title('Le mot de passe est requis')
                ->danger()
                ->send();
            return;
        }

        if (!Hash::check($this->deletePassword, Auth::user()->password)) {
            Notification::make()
                ->title('Mot de passe incorrect')
                ->danger()
                ->send();
            return;
        }

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        redirect()->to('/');
    }

    public function getData(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function getTwoFactorQrCodeSvg(): string
    {
        $user = Auth::user();
        
        if (empty($user->two_factor_secret)) {
            return '';
        }

        $google2fa = new \PragmaRX\Google2FA\Google2FA();
        $qrCodeUrl = $google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            decrypt($user->two_factor_secret)
        );

        return \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrCodeUrl);
    }
}

