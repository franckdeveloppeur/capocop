# 🔧 Résolution du Problème d'Envoi d'Emails

## 🎯 Diagnostic Rapide

### Étape 1 : Tester la Configuration

Visitez cette URL dans votre navigateur :
```
http://localhost:8000/test-email
```

Cette route va :
- ✅ Afficher votre configuration email actuelle
- ✅ Tester l'envoi d'un email (sans queue)
- ✅ Vérifier les jobs en attente/échoués

### Étape 2 : Vérifier les Résultats

Si vous voyez `"email_send": "✅ Email envoyé avec succès"`, alors la configuration fonctionne et le problème vient des **queues**.

## 🚨 Problème Principal : Les Notifications Utilisent des Queues

**Toutes vos notifications** (`OrderCreatedNotification`, `OrderStatusChangedNotification`, etc.) implémentent `ShouldQueue`, ce qui signifie qu'elles sont **mises en file d'attente** au lieu d'être envoyées immédiatement.

### Solution Immédiate

Ajoutez dans votre fichier `.env` :

```env
QUEUE_CONNECTION=sync
```

Puis videz le cache :
```bash
php artisan config:clear
```

**Cela enverra les emails immédiatement sans passer par la queue.**

### Alternative : Traiter les Jobs en Queue

Si vous préférez garder `QUEUE_CONNECTION=database`, vous devez démarrer un worker :

```bash
php artisan queue:work
```

**⚠️ Important :** Le worker doit rester en cours d'exécution pour traiter les emails.

## 📋 Checklist Complète

### 1. Configuration `.env`

Vérifiez que votre `.env` contient :

```env
# Email
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=8025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@capocop.com"
MAIL_FROM_NAME="${APP_NAME}"

# Queue (IMPORTANT pour le développement)
QUEUE_CONNECTION=sync
```

### 2. MailHog/Mailpit en Cours d'Exécution

Le port 8025 est utilisé par MailHog ou Mailpit. Vérifiez que le service est démarré :

- **MailHog** : http://localhost:8025
- **Mailpit** : http://localhost:8025

Si le service n'est pas démarré, les emails ne pourront pas être envoyés.

### 3. Vider le Cache

Après chaque modification du `.env` :

```bash
php artisan config:clear
php artisan cache:clear
```

### 4. Vérifier les Jobs Échoués

Si des jobs ont échoué :

```bash
php artisan queue:failed
```

Pour voir les détails d'un job échoué :

```bash
php artisan queue:failed-table
```

## 🧪 Tests

### Test 1 : Email Direct (Sans Queue)

```bash
php artisan tinker
```

Puis :
```php
Mail::raw('Test', function ($m) {
    $m->to('test@example.com')->subject('Test');
});
```

### Test 2 : Via la Route de Diagnostic

Visitez : http://localhost:8000/test-email

### Test 3 : Via la Commande Artisan

```bash
php artisan email:test votre@email.com
```

## 🔍 Diagnostic des Erreurs Courantes

### "Connection refused" ou "Connection timeout"

**Cause :** MailHog/Mailpit n'est pas en cours d'exécution.

**Solution :**
1. Démarrez MailHog ou Mailpit
2. Vérifiez que le port 8025 est libre
3. Vérifiez l'URL : http://localhost:8025

### "Emails ne sont pas envoyés" (sans erreur)

**Cause :** Les notifications sont en queue et aucun worker ne les traite.

**Solution :**
```env
QUEUE_CONNECTION=sync
```

Puis :
```bash
php artisan config:clear
```

### "Failed to authenticate on SMTP server"

**Cause :** `MAIL_USERNAME` et `MAIL_PASSWORD` sont incorrects.

**Solution :** Pour MailHog/Mailpit, laissez-les à `null`.

### "Table 'jobs' doesn't exist"

**Cause :** Les migrations de queue n'ont pas été exécutées.

**Solution :**
```bash
php artisan migrate
```

## 💡 Solutions par Scénario

### Scénario 1 : Développement Local avec MailHog

```env
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=8025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
QUEUE_CONNECTION=sync
```

### Scénario 2 : Développement avec Mailtrap

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_ENCRYPTION=tls
QUEUE_CONNECTION=sync
```

### Scénario 3 : Voir les Emails dans les Logs

```env
MAIL_MAILER=log
QUEUE_CONNECTION=sync
```

Les emails seront dans `storage/logs/laravel.log`.

## ✅ Après Avoir Appliqué les Solutions

1. Videz le cache : `php artisan config:clear`
2. Testez avec : http://localhost:8000/test-email
3. Créez une commande pour déclencher une notification
4. Vérifiez MailHog/Mailpit ou les logs

## 🗑️ Nettoyage

Après avoir résolu le problème, vous pouvez supprimer la route de test dans `routes/web.php` :

```php
// Supprimez cette route en production
Route::get('/test-email', ...)
```

