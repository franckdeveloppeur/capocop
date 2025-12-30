# 📧 Guide de Configuration des Emails

## ✅ Vérification de la Configuration Email dans le fichier `.env`

Pour que les emails fonctionnent correctement dans votre application, vous devez vérifier les variables suivantes dans votre fichier `.env` (lignes 1-66) :

### Variables Requises pour SMTP

```env
# Type de mailer (smtp, log, sendmail, mailgun, ses, postmark, resend)
MAIL_MAILER=smtp

# Configuration SMTP
MAIL_HOST=smtp.mailtrap.io          # ou votre serveur SMTP
MAIL_PORT=2525                      # Port SMTP (587 pour TLS, 465 pour SSL, 2525 pour Mailtrap)
MAIL_USERNAME=votre_username        # Votre nom d'utilisateur SMTP
MAIL_PASSWORD=votre_password        # Votre mot de passe SMTP
MAIL_ENCRYPTION=tls                 # tls ou ssl (optionnel)
MAIL_FROM_ADDRESS=noreply@capocop.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Configuration pour différents services

#### 1. **Mailtrap (Développement/Test)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username_mailtrap
MAIL_PASSWORD=votre_password_mailtrap
MAIL_ENCRYPTION=tls
```

#### 2. **Gmail**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre_email@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_app  # Utiliser un mot de passe d'application
MAIL_ENCRYPTION=tls
```

#### 3. **SendGrid**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=votre_api_key_sendgrid
MAIL_ENCRYPTION=tls
```

#### 4. **Mailgun**
```env
MAIL_MAILER=mailgun
# Configuration dans config/services.php
MAILGUN_DOMAIN=votre_domaine.mailgun.org
MAILGUN_SECRET=votre_secret_key
```

#### 5. **Mode Log (Développement - emails dans les logs)**
```env
MAIL_MAILER=log
# Les emails seront enregistrés dans storage/logs/laravel.log
```

### Variables Actuellement Utilisées par l'Application

D'après `config/mail.php`, votre application utilise :

- **Mailer par défaut** : `env('MAIL_MAILER', 'log')` - Si non défini, utilise 'log'
- **Host SMTP** : `env('MAIL_HOST', '127.0.0.1')` - Par défaut 127.0.0.1
- **Port SMTP** : `env('MAIL_PORT', 2525)` - Par défaut 2525
- **Adresse expéditeur** : `env('MAIL_FROM_ADDRESS', 'hello@example.com')`
- **Nom expéditeur** : `env('MAIL_FROM_NAME', 'Example')`

## 🔍 Comment Vérifier Votre Configuration

### Méthode 1 : Vérification manuelle

1. Ouvrez votre fichier `.env`
2. Vérifiez que les variables `MAIL_*` sont présentes et correctement configurées
3. Assurez-vous que `MAIL_MAILER` n'est pas sur `log` en production

### Méthode 2 : Test via Artisan

Exécutez la commande suivante pour tester l'envoi d'email :

```bash
php artisan tinker
```

Puis dans Tinker :
```php
Mail::raw('Test email', function ($message) {
    $message->to('votre-email@test.com')
            ->subject('Test de configuration email');
});
```

### Méthode 3 : Utiliser le script de test

Un script de test est disponible dans `tests/EmailTest.php`

## ⚠️ Problèmes Courants

1. **Emails non envoyés** : Vérifiez que `MAIL_MAILER` n'est pas sur `log`
2. **Erreur d'authentification** : Vérifiez `MAIL_USERNAME` et `MAIL_PASSWORD`
3. **Port bloqué** : Vérifiez que le port SMTP n'est pas bloqué par le firewall
4. **Gmail** : Utilisez un "Mot de passe d'application" et non votre mot de passe Gmail

## 📝 Notifications Utilisées dans l'Application

Votre application envoie des emails pour :
- ✅ Création de commande (`OrderCreatedNotification`)
- ✅ Changement de statut de commande (`OrderStatusChangedNotification`)
- ✅ Paiement réussi (`PaymentSuccessNotification`)
- ✅ Plan de paiement échelonné (`InstallmentPlanCreatedNotification`)
- ✅ Échéance de paiement (`InstallmentDueNotification`)
- ✅ Paiement d'échéance (`InstallmentPaidNotification`)

Toutes ces notifications utilisent la configuration email définie dans `.env`.


