<x-filament-panels::page>
    @php
        $user = \Illuminate\Support\Facades\Auth::user();
    @endphp

    <div class="space-y-8">
        {{-- Section Informations du Profil --}}
        @if (Laravel\Fortify\Features::canUpdateProfileInformation())
            <x-filament::section>
                <x-slot name="heading">
                    Informations du Profil
                </x-slot>
                
                <x-slot name="description">
                    Mettez à jour les informations de votre compte et votre adresse e-mail.
                </x-slot>

                <form wire:submit="updateProfileInformation" class="space-y-6">
                    {{-- Photo de profil --}}
                    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                        <div x-data="{ photoName: null, photoPreview: null }" class="space-y-4">
                            <div>
                                <x-label value="Photo" />
                                
                                <div class="mt-2 flex items-center gap-4">
                                    <!-- Photo actuelle -->
                                    <div x-show="!photoPreview">
                                        <img src="{{ $user->profile_photo_url }}" 
                                             alt="{{ $user->name }}" 
                                             class="h-20 w-20 rounded-full object-cover">
                                    </div>
                                    
                                    <!-- Aperçu nouvelle photo -->
                                    <div x-show="photoPreview" style="display: none;">
                                        <span class="block h-20 w-20 rounded-full bg-cover bg-center bg-no-repeat"
                                              x-bind:style="'background-image: url(\'' + photoPreview + '\');'">
                                        </span>
                                    </div>
                                    
                                    <div class="flex gap-2">
                                        <input type="file" 
                                               id="photo" 
                                               class="hidden"
                                               wire:model.live="photo"
                                               x-ref="photo"
                                               x-on:change="
                                                   photoName = $refs.photo.files[0]?.name;
                                                   const reader = new FileReader();
                                                   reader.onload = (e) => {
                                                       photoPreview = e.target.result;
                                                   };
                                                   if ($refs.photo.files[0]) {
                                                       reader.readAsDataURL($refs.photo.files[0]);
                                                   }
                                               ">
                                        
                                        <x-filament::button 
                                            type="button" 
                                            color="gray"
                                            x-on:click.prevent="$refs.photo.click()">
                                            Sélectionner une nouvelle photo
                                        </x-filament::button>
                                        
                                        @if ($user->profile_photo_path)
                                            <x-filament::button 
                                                type="button" 
                                                color="danger"
                                                wire:click="deleteProfilePhoto">
                                                Supprimer la photo
                                            </x-filament::button>
                                        @endif
                                    </div>
                                </div>
                                
                                <x-input-error for="photo" class="mt-2" />
                            </div>
                        </div>
                    @endif

                    {{-- Nom --}}
                    <div>
                        <x-label for="name" value="Nom" />
                        <x-input 
                            id="name"
                            type="text"
                            wire:model="name"
                            required
                            autocomplete="name"
                            class="mt-1 block w-full" />
                        <x-input-error for="name" class="mt-2" />
                    </div>

                    {{-- Email --}}
                    <div>
                        <x-label for="email" value="E-mail" />
                        <x-input 
                            id="email"
                            type="email"
                            wire:model="email"
                            required
                            autocomplete="username"
                            class="mt-1 block w-full" />
                        <x-input-error for="email" class="mt-2" />
                        
                        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::emailVerification()) && ! $user->hasVerifiedEmail())
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                Votre adresse e-mail n'est pas vérifiée.
                                <button type="button" 
                                        class="underline text-sm text-gray-900 hover:text-gray-700 dark:text-gray-300 dark:hover:text-gray-100"
                                        wire:click.prevent="sendEmailVerification">
                                    Cliquez ici pour renvoyer l'e-mail de vérification.
                                </button>
                            </p>

                            @if ($this->verificationLinkSent)
                                <p class="mt-2 text-sm font-medium text-success-600">
                                    Un nouveau lien de vérification a été envoyé à votre adresse e-mail.
                                </p>
                            @endif
                        @endif
                    </div>

                    <div class="flex justify-end pt-4">
                        <x-filament::button type="submit" wire:loading.attr="disabled">
                            Enregistrer
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        @endif

        {{-- Section Mot de passe --}}
        @if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
            <x-filament::section>
                <x-slot name="heading">
                    Mise à jour du mot de passe
                </x-slot>
                
                <x-slot name="description">
                    Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé.
                </x-slot>

                <form wire:submit="updatePassword" class="space-y-6">
                    <div>
                        <x-label for="current_password" value="Mot de passe actuel" />
                        <x-input 
                            id="current_password"
                            type="password"
                            wire:model="current_password"
                            autocomplete="current-password"
                            class="mt-1 block w-full" />
                        <x-input-error for="current_password" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="password" value="Nouveau mot de passe" />
                        <x-input 
                            id="password"
                            type="password"
                            wire:model="password"
                            autocomplete="new-password"
                            class="mt-1 block w-full" />
                        <x-input-error for="password" class="mt-2" />
                    </div>

                    <div>
                        <x-label for="password_confirmation" value="Confirmer le mot de passe" />
                        <x-input 
                            id="password_confirmation"
                            type="password"
                            wire:model="password_confirmation"
                            autocomplete="new-password"
                            class="mt-1 block w-full" />
                        <x-input-error for="password_confirmation" class="mt-2" />
                    </div>

                    <div class="flex justify-end pt-4">
                        <x-filament::button type="submit">
                            Enregistrer
                        </x-filament::button>
                    </div>
                </form>
            </x-filament::section>
        @endif

        {{-- Section Authentification à deux facteurs --}}
        @if (Laravel\Fortify\Features::canManageTwoFactorAuthentication())
            <x-filament::section>
                <x-slot name="heading">
                    Authentification à deux facteurs
                </x-slot>
                
                <x-slot name="description">
                    Ajoutez une sécurité supplémentaire à votre compte en utilisant l'authentification à deux facteurs.
                </x-slot>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                            @if ($this->enabled)
                                @if ($showingConfirmation)
                                    Terminer l'activation de l'authentification à deux facteurs.
                                @else
                                    Vous avez activé l'authentification à deux facteurs.
                                @endif
                            @else
                                Vous n'avez pas activé l'authentification à deux facteurs.
                            @endif
                        </h3>

                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Lorsque l'authentification à deux facteurs est activée, vous serez invité à saisir un jeton sécurisé et aléatoire lors de l'authentification. Vous pouvez récupérer ce jeton depuis l'application Google Authenticator de votre téléphone.
                        </p>
                    </div>

                    @if ($this->enabled)
                        @if ($showingQrCode)
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    @if ($showingConfirmation)
                                        Pour terminer l'activation de l'authentification à deux facteurs, scannez le code QR suivant à l'aide de l'application d'authentification de votre téléphone ou entrez la clé de configuration et fournissez le code OTP généré.
                                    @else
                                        L'authentification à deux facteurs est maintenant activée. Scannez le code QR suivant à l'aide de l'application d'authentification de votre téléphone ou entrez la clé de configuration.
                                    @endif
                                </p>
                            </div>

                            <div class="mt-4 inline-block rounded-lg bg-white p-2">
                                {!! $this->getTwoFactorQrCodeSvg() !!}
                            </div>

                            <div class="mt-4">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    Clé de configuration : {{ decrypt($user->two_factor_secret) }}
                                </p>
                            </div>

                            @if ($showingConfirmation)
                                <div class="mt-4">
                                    <x-label for="code" value="Code" />
                                    <x-input 
                                        id="code"
                                        type="text"
                                        wire:model="code"
                                        inputmode="numeric"
                                        autofocus
                                        autocomplete="one-time-code"
                                        wire:keydown.enter="confirmTwoFactorAuthentication"
                                        class="mt-1 block w-full" />
                                    <x-input-error for="code" class="mt-2" />
                                </div>
                            @endif
                        @endif

                        @if ($showingRecoveryCodes)
                            <div class="mt-4">
                                <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                    Stockez ces codes de récupération dans un gestionnaire de mots de passe sécurisé. Ils peuvent être utilisés pour récupérer l'accès à votre compte si votre appareil d'authentification à deux facteurs est perdu.
                                </p>
                            </div>

                            <div class="mt-4 grid gap-1 rounded-lg bg-gray-100 dark:bg-gray-800 px-4 py-4 font-mono text-sm">
                                @foreach (json_decode(decrypt($user->two_factor_recovery_codes), true) as $code)
                                    <div class="text-gray-900 dark:text-gray-100">{{ $code }}</div>
                                @endforeach
                            </div>
                        @endif
                    @endif

                    <div class="mt-5 flex gap-3">
                        @if (! $this->enabled)
                            <x-filament::button 
                                type="button" 
                                wire:click="enableTwoFactorAuthentication"
                                wire:loading.attr="disabled">
                                Activer
                            </x-filament::button>
                        @else
                            @if ($showingRecoveryCodes)
                                <x-filament::button 
                                    type="button" 
                                    color="gray"
                                    wire:click="regenerateRecoveryCodes">
                                    Régénérer les codes de récupération
                                </x-filament::button>
                            @elseif ($showingConfirmation)
                                <x-filament::button 
                                    type="button" 
                                    wire:click="confirmTwoFactorAuthentication"
                                    wire:loading.attr="disabled">
                                    Confirmer
                                </x-filament::button>
                            @else
                                <x-filament::button 
                                    type="button" 
                                    color="gray"
                                    wire:click="showRecoveryCodes">
                                    Afficher les codes de récupération
                                </x-filament::button>
                            @endif

                            @if ($showingConfirmation)
                                <x-filament::button 
                                    type="button" 
                                    color="gray"
                                    wire:click="disableTwoFactorAuthentication"
                                    wire:loading.attr="disabled">
                                    Annuler
                                </x-filament::button>
                            @else
                                <x-filament::button 
                                    type="button" 
                                    color="danger"
                                    wire:click="disableTwoFactorAuthentication"
                                    wire:loading.attr="disabled">
                                    Désactiver
                                </x-filament::button>
                            @endif
                        @endif
                    </div>
                </div>
            </x-filament::section>
        @endif

        {{-- Section Sessions navigateur --}}
        <x-filament::section>
            <x-slot name="heading">
                Sessions du navigateur
            </x-slot>
            
            <x-slot name="description">
                Gérez et déconnectez vos sessions actives sur d'autres navigateurs et appareils.
            </x-slot>

            <div class="space-y-6">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Si nécessaire, vous pouvez vous déconnecter de toutes vos autres sessions de navigateur sur tous vos appareils. Certaines de vos sessions récentes sont listées ci-dessous ; cependant, cette liste peut ne pas être exhaustive. Si vous pensez que votre compte a été compromis, vous devriez également mettre à jour votre mot de passe.
                </p>

                @if (count($this->sessions) > 0)
                    <div class="mt-5 space-y-6">
                        @foreach ($this->sessions as $session)
                            <div class="flex items-center">
                                <div>
                                    @if ($session['agent']['is_desktop'] ?? true)
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                        </svg>
                                    @endif
                                </div>

                                <div class="ml-3">
                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        {{ $session['agent']['platform'] ?? 'Inconnu' }} - {{ $session['agent']['browser'] ?? 'Inconnu' }}
                                    </div>

                                    <div class="text-xs text-gray-500 dark:text-gray-500">
                                        {{ $session['ip_address'] ?? 'N/A' }},
                                        @if ($session['is_current_device'] ?? false)
                                            <span class="font-semibold text-success-600">Cet appareil</span>
                                        @else
                                            Dernière activité {{ $session['last_active'] ?? 'N/A' }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-5">
                    <x-filament::button 
                        type="button"
                        wire:click="confirmLogout"
                        wire:loading.attr="disabled">
                        Déconnecter les autres sessions de navigateur
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        {{-- Section Suppression de compte --}}
        @if (Laravel\Jetstream\Jetstream::hasAccountDeletionFeatures())
            <x-filament::section>
                <x-slot name="heading">
                    Supprimer le compte
                </x-slot>
                
                <x-slot name="description">
                    Supprimez définitivement votre compte.
                </x-slot>

                <div class="space-y-6">
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.
                    </p>

                    <div class="mt-5">
                        <x-filament::button 
                            type="button"
                            color="danger"
                            wire:click="confirmUserDeletion"
                            wire:loading.attr="disabled">
                            Supprimer le compte
                        </x-filament::button>
                    </div>
                </div>
            </x-filament::section>
        @endif
    </div>

    {{-- Modales --}}
    
    {{-- Modal de confirmation de déconnexion --}}
    <x-filament::modal 
        id="logout-confirmation"
        wire:model="confirmingLogout"
        heading="Déconnecter les autres sessions de navigateur">
        
        <x-slot name="description">
            Veuillez entrer votre mot de passe pour confirmer que vous souhaitez vous déconnecter de vos autres sessions de navigateur sur tous vos appareils.
        </x-slot>

        <div>
            <x-label for="logout_password" value="Mot de passe" />
            <x-input 
                id="logout_password"
                type="password"
                wire:model="logoutPassword"
                autocomplete="current-password"
                wire:keydown.enter="logoutOtherBrowserSessions"
                class="mt-1 block w-full" />
            <x-input-error for="logoutPassword" class="mt-2" />
        </div>

        <x-slot name="footer">
            <x-filament::button 
                color="gray"
                wire:click="$set('confirmingLogout', false)"
                wire:loading.attr="disabled">
                Annuler
            </x-filament::button>

            <x-filament::button 
                wire:click="logoutOtherBrowserSessions"
                wire:loading.attr="disabled">
                Déconnecter les autres sessions de navigateur
            </x-filament::button>
        </x-slot>
    </x-filament::modal>

    {{-- Modal de confirmation de suppression --}}
    <x-filament::modal 
        id="delete-account-confirmation"
        wire:model="confirmingUserDeletion"
        heading="Supprimer le compte">
        
        <x-slot name="description">
            Êtes-vous sûr de vouloir supprimer votre compte ? Une fois votre compte supprimé, toutes ses ressources et données seront définitivement supprimées. Veuillez entrer votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.
        </x-slot>

        <div>
            <x-label for="delete_password" value="Mot de passe" />
            <x-input 
                id="delete_password"
                type="password"
                wire:model="deletePassword"
                autocomplete="current-password"
                wire:keydown.enter="deleteUser"
                class="mt-1 block w-full" />
            <x-input-error for="deletePassword" class="mt-2" />
        </div>

        <x-slot name="footer">
            <x-filament::button 
                color="gray"
                wire:click="$set('confirmingUserDeletion', false)"
                wire:loading.attr="disabled">
                Annuler
            </x-filament::button>

            <x-filament::button 
                color="danger"
                wire:click="deleteUser"
                wire:loading.attr="disabled">
                Supprimer le compte
            </x-filament::button>
        </x-slot>
    </x-filament::modal>
</x-filament-panels::page>

