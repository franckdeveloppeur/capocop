# 🌍 Système de traduction - Capocop

Ce dossier contient les fichiers de traduction pour l'application Capocop.

## 📁 Structure

```
lang/
├── fr/           # Traductions françaises (par défaut)
│   └── auth.php  # Traductions d'authentification
└── en/           # Traductions anglaises
    └── auth.php  # Traductions d'authentification
```

## 🇫🇷 Langue par défaut

Le **français** est configuré comme langue par défaut de l'application.

### Configuration dans `config/app.php`

```php
'locale' => 'fr',              // Langue par défaut
'fallback_locale' => 'en',     // Langue de secours
'faker_locale' => 'fr_FR',     // Locale Faker
```

## 🔄 Changer la langue

### Méthode 1 : Dans les vues (dynamique)

```blade
<!-- Changer temporairement la langue -->
@php
    App::setLocale('en');
@endphp

<!-- Ou via une route -->
Route::get('/lang/{locale}', function ($locale) {
    App::setLocale($locale);
    session(['locale' => $locale]);
    return redirect()->back();
});
```

### Méthode 2 : Dans le .env (permanent)

```env
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
```

## 📝 Utiliser les traductions

### Dans les vues Blade

```blade
<!-- Syntaxe courte -->
{{ __('auth.sign_in') }}

<!-- Syntaxe avec paramètres -->
{{ __('auth.welcome_user', ['name' => $user->name]) }}

<!-- Syntaxe @ -->
@lang('auth.sign_in')
```

### Dans les contrôleurs

```php
use Illuminate\Support\Facades\Lang;

// Méthode 1
$message = __('auth.sign_in');

// Méthode 2
$message = Lang::get('auth.sign_in');

// Avec paramètres
$message = __('auth.welcome_user', ['name' => $user->name]);
```

### Dans les fichiers JavaScript

```javascript
// Via une route API
fetch('/api/translations/fr')
    .then(response => response.json())
    .then(translations => {
        console.log(translations.auth.sign_in);
    });
```

## ➕ Ajouter une nouvelle traduction

### 1. Ajouter la clé dans `lang/fr/auth.php`

```php
return [
    // ...
    'welcome_message' => 'Bienvenue sur Capocop !',
];
```

### 2. Ajouter la traduction anglaise dans `lang/en/auth.php`

```php
return [
    // ...
    'welcome_message' => 'Welcome to Capocop!',
];
```

### 3. Utiliser dans une vue

```blade
<h1>{{ __('auth.welcome_message') }}</h1>
```

## 🗂️ Créer un nouveau fichier de traduction

### Exemple : Créer `lang/fr/produits.php`

```php
<?php

return [
    'title' => 'Nos produits',
    'add_to_cart' => 'Ajouter au panier',
    'price' => 'Prix',
    'in_stock' => 'En stock',
    'out_of_stock' => 'Rupture de stock',
];
```

### Utilisation

```blade
{{ __('produits.add_to_cart') }}
```

## 🎨 Traductions dans les pages d'authentification

Toutes les pages d'authentification utilisent le système de traduction :

- ✅ **Connexion** (`/login`)
- ✅ **Inscription** (`/register`)
- ✅ **Mot de passe oublié** (`/forgot-password`)
- ✅ **Réinitialisation** (`/reset-password`)
- ✅ **Vérification email** (`/verify-email`)
- ✅ **Confirmation mot de passe** (`/confirm-password`)
- ✅ **Authentification 2FA** (`/two-factor-challenge`)

## 📚 Documentation Laravel

Pour plus d'informations, consultez la [documentation officielle Laravel sur la localisation](https://laravel.com/docs/12.x/localization).

## 🌐 Langues supportées

- 🇫🇷 **Français** (par défaut)
- 🇬🇧 **Anglais**

## ✨ Bonnes pratiques

1. **Utilisez des clés descriptives** : `auth.sign_in` plutôt que `auth.btn1`
2. **Groupez par fonctionnalité** : `auth.php`, `produits.php`, `commandes.php`
3. **Maintenez la cohérence** : Même structure dans `fr/` et `en/`
4. **Commentez les traductions complexes** pour faciliter la maintenance
5. **Utilisez des placeholders** pour les valeurs dynamiques : `:name`, `:count`

## 🔧 Commandes utiles

```bash
# Publier les fichiers de langue Laravel
php artisan lang:publish

# Vider le cache des traductions
php artisan cache:clear

# Vérifier la locale actuelle
php artisan tinker
>>> App::getLocale()
```

## 🤝 Contribution

Pour ajouter une nouvelle langue :

1. Créez un nouveau dossier dans `lang/` (ex: `lang/es/`)
2. Copiez les fichiers de `lang/fr/`
3. Traduisez tous les textes
4. Mettez à jour ce README

---

**Développé avec ❤️ pour Capocop**



