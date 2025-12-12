# 🚀 Déploiement sur Render - Guide Complet

Ce guide vous explique comment déployer votre application Laravel Capocop sur [Render](https://render.com), une plateforme cloud moderne et facile à utiliser.

## 📋 Prérequis

- Un compte Render (gratuit pour commencer)
- Votre code sur un dépôt Git (GitHub, GitLab, ou Bitbucket)
- Environ 15-20 minutes pour le premier déploiement

## 🎯 Avantages de Render

✅ **Déploiement automatique** depuis Git  
✅ **SSL/HTTPS gratuit** avec certificats automatiques  
✅ **MySQL 8.x géré** avec backups automatiques  
✅ **Redis géré** pour cache et queues  
✅ **Scaling horizontal** facile  
✅ **Logs en temps réel**  
✅ **Environnements de staging** gratuits  
✅ **Infrastructure as Code** avec `render.yaml`

## 🏗️ Architecture sur Render

Votre application sera composée de :

1. **Web Service** - Application Laravel (PHP 8.2 + Nginx)
2. **Worker Service** - Traitement des queues Laravel
3. **Cron Job** - Tâches planifiées (`schedule:run`)
4. **MySQL Database** - Base de données gérée (8.x)
5. **Redis** - Cache et gestion des queues

## 📦 Option 1 : Déploiement avec render.yaml (Recommandé)

### Étape 1 : Préparer votre dépôt Git

Assurez-vous que tous les fichiers de configuration Render sont dans votre dépôt :

```bash
git add .
git commit -m "Ajouter configuration Render"
git push origin main
```

### Étape 2 : Créer un Blueprint sur Render

1. Connectez-vous à [Render Dashboard](https://dashboard.render.com)
2. Cliquez sur **"New +"** → **"Blueprint"**
3. Connectez votre dépôt Git
4. Render détectera automatiquement le fichier `render.yaml`
5. Cliquez sur **"Apply"**

Render va créer automatiquement :
- Le service web
- Le worker
- Le cron job
- La base de données MySQL
- Le cache Redis

### Étape 3 : Configuration des variables d'environnement

Après le déploiement initial, ajoutez ces variables dans le Dashboard :

**Dans l'onglet "Environment" de votre service web :**

```env
APP_URL=https://votre-app.onrender.com
SANCTUM_STATEFUL_DOMAINS=votre-app.onrender.com

# Mail (exemple avec Mailtrap)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=587
MAIL_USERNAME=votre_username
MAIL_PASSWORD=votre_password
MAIL_ENCRYPTION=tls
```

### Étape 4 : Vérifier le déploiement

Render va :
1. ✅ Construire l'image Docker
2. ✅ Créer la base de données MySQL
3. ✅ Créer Redis
4. ✅ Démarrer les services
5. ✅ Exécuter les migrations
6. ✅ Optimiser l'application

Surveillez les logs en temps réel dans le Dashboard.

## 📦 Option 2 : Déploiement Manuel (Services individuels)

Si vous préférez créer les services un par un :

### 1. Créer la base de données MySQL

1. **New +** → **MySQL**
2. Nom : `capocop-db`
3. Plan : **Starter** (gratuit pour commencer)
4. Région : **Frankfurt** (Europe)
5. Créer

⏳ Attendez 5-10 minutes que la base soit prête.

### 2. Créer Redis

1. **New +** → **Redis**
2. Nom : `capocop-redis`
3. Plan : **Starter**
4. Région : **Frankfurt**
5. Créer

### 3. Créer le service Web

1. **New +** → **Web Service**
2. Connecter votre dépôt Git
3. Configuration :
   - **Name** : `capocop-app`
   - **Region** : Frankfurt
   - **Branch** : main
   - **Runtime** : Docker
   - **Dockerfile Path** : `./Dockerfile.render`
   - **Plan** : Starter (ou Standard pour production)

4. Variables d'environnement (cliquez "Add Environment Variable") :

```env
APP_NAME=Capocop
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_URL=https://votre-app.onrender.com

# Database (utilisez les valeurs de connexion depuis votre DB MySQL)
DB_CONNECTION=mysql
DB_HOST=<internal-url-from-mysql-service>
DB_PORT=3306
DB_DATABASE=capocop
DB_USERNAME=capocop
DB_PASSWORD=<from-mysql-service>

# Redis (utilisez les valeurs depuis votre Redis service)
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=<internal-url-from-redis-service>
REDIS_PORT=6379
REDIS_PASSWORD=<from-redis-service>

# Autres
FILESYSTEM_DISK=public
LOG_CHANNEL=stack
LOG_LEVEL=error
```

5. **Advanced** :
   - **Health Check Path** : `/health`
   - **Auto-Deploy** : Yes

6. **Créer le service**

### 4. Créer le Worker (Queues)

1. **New +** → **Background Worker**
2. Connecter le même dépôt
3. Configuration :
   - **Name** : `capocop-worker`
   - **Region** : Frankfurt
   - **Branch** : main
   - **Runtime** : Docker
   - **Dockerfile Path** : `./Dockerfile.render`
   - **Docker Command** : `php artisan queue:work --tries=3 --timeout=90 --sleep=3`

4. Ajoutez les **mêmes variables d'environnement** que le service web

5. **Créer le service**

### 5. Créer le Cron Job (Scheduler)

1. **New +** → **Cron Job**
2. Connecter le même dépôt
3. Configuration :
   - **Name** : `capocop-scheduler`
   - **Region** : Frankfurt
   - **Branch** : main
   - **Runtime** : Docker
   - **Dockerfile Path** : `./Dockerfile.render`
   - **Command** : `php artisan schedule:run`
   - **Schedule** : `* * * * *` (toutes les minutes)

4. Ajoutez les **mêmes variables d'environnement**

5. **Créer le cron job**

## 🔧 Configuration Post-Déploiement

### 1. Configurer le domaine personnalisé (Optionnel)

1. Allez dans votre service web → **Settings** → **Custom Domain**
2. Ajoutez votre domaine : `www.capocop.com`
3. Configurez les DNS selon les instructions Render
4. Render génère automatiquement un certificat SSL

### 2. Mettre à jour APP_URL

```env
APP_URL=https://www.capocop.com
SANCTUM_STATEFUL_DOMAINS=www.capocop.com,capocop.com
```

### 3. Configurer le stockage persistant

Si vous utilisez le disque local pour les uploads :

1. Service web → **Settings** → **Disks**
2. **Add Disk** :
   - Name : `storage`
   - Mount Path : `/var/www/storage`
   - Size : 10 GB

⚠️ **Note** : Pour la production, il est recommandé d'utiliser S3/DO Spaces.

### 4. Configurer le mail

Ajoutez votre configuration SMTP :

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net  # ou autre fournisseur
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=votre_api_key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@capocop.com
MAIL_FROM_NAME=Capocop
```

Fournisseurs recommandés :
- **SendGrid** (gratuit jusqu'à 100 emails/jour)
- **Mailgun** (gratuit les 3 premiers mois)
- **Postmark** (gratuit jusqu'à 100 emails/mois)

## 📊 Monitoring et Logs

### Voir les logs en temps réel

```bash
# Via le Dashboard Render
Dashboard → Votre service → Logs (onglet)

# Via Render CLI
render logs -s capocop-app --tail
```

### Installer Render CLI

```bash
# macOS/Linux
brew install render-cli

# Windows
scoop install render-cli

# Ou via npm
npm install -g @render/cli
```

### Exécuter des commandes

```bash
# Se connecter
render login

# Exécuter une commande
render exec -s capocop-app -- php artisan migrate

# Ouvrir un shell
render shell -s capocop-app
```

## 🔒 Sécurité

### 1. Variables d'environnement sensibles

✅ **TOUJOURS** utiliser les variables d'environnement Render  
❌ **JAMAIS** hardcoder les mots de passe dans le code  

### 2. Accès à la base de données

Render fournit deux URLs de connexion :
- **Internal URL** : Utilisez celle-ci depuis vos services (gratuit, rapide)
- **External URL** : Pour vous connecter depuis votre machine (payant après les premiers Go)

### 3. Sauvegardes automatiques

Render effectue des backups automatiques de MySQL :
- Plan Starter : 1 backup/jour, rétention 7 jours
- Plan Standard : 1 backup/jour, rétention 30 jours
- Plan Pro : Backups continus

### 4. Limiter l'accès IP (Optionnel)

Pour MySQL et Redis, vous pouvez ajouter des IP allowlist :

Database → Settings → Allowed IP Addresses

## ⚡ Optimisations de Performance

### 1. Activer le cache applicatif

```bash
render exec -s capocop-app -- php artisan config:cache
render exec -s capocop-app -- php artisan route:cache
render exec -s capocop-app -- php artisan view:cache
```

Ces caches sont automatiquement créés au déploiement via `start.sh`.

### 2. Utiliser un CDN

Pour les assets statiques, utilisez un CDN :
- **Cloudflare** (gratuit)
- **BunnyCDN** (très bon rapport qualité/prix)
- **AWS CloudFront**

### 3. Scaling horizontal

Augmentez le nombre d'instances dans les moments de forte charge :

Service → Settings → Scaling → Number of Instances : 2-5

### 4. Monitoring avec New Relic (Gratuit)

Render s'intègre gratuitement avec New Relic :

1. Créez un compte [New Relic](https://newrelic.com)
2. Ajoutez la variable : `NEW_RELIC_LICENSE_KEY`
3. Installez l'agent PHP New Relic dans le Dockerfile

## 🐛 Dépannage

### Le service ne démarre pas

1. **Vérifiez les logs** : Dashboard → Logs
2. **Erreurs communes** :
   - `APP_KEY` non définie → Générez-la : `php artisan key:generate --show`
   - Migrations échouées → Vérifiez la connexion DB
   - Build timeout → Augmentez le plan ou optimisez le Dockerfile

### Base de données non accessible

```bash
# Tester la connexion
render exec -s capocop-app -- php artisan db:show

# Vérifier les variables
render exec -s capocop-app -- env | grep DB_
```

### Le worker ne traite pas les jobs

1. Vérifiez que le service worker est actif
2. Vérifiez les logs du worker
3. Testez Redis : `php artisan queue:work --once`

### "502 Bad Gateway"

- Le service est en train de démarrer (attendez 2-3 minutes)
- PHP-FPM a crashé (vérifiez les logs)
- Nginx mal configuré (vérifiez `docker/render/default.conf`)

### Migrations ne s'exécutent pas

```bash
# Forcer les migrations manuellement
render exec -s capocop-app -- php artisan migrate --force
```

## 💰 Coûts Estimés

### Plan gratuit (Starter)

- **Web Service** : Gratuit (spin down après 15 min d'inactivité)
- **Worker** : $7/mois
- **MySQL** : Gratuit (1 GB)
- **Redis** : Gratuit (25 MB)
- **Cron Job** : Gratuit

**Total** : ~$7/mois + frais de transfert

### Plan production recommandé

- **Web Service (Standard)** : $25/mois (toujours actif, 2 GB RAM)
- **Worker (Standard)** : $25/mois
- **MySQL (Standard)** : $25/mois (10 GB, backups 30j)
- **Redis (Standard)** : $10/mois (256 MB)
- **Cron Job** : Gratuit

**Total** : ~$85/mois + données transférées

### Réduire les coûts

1. **Commencez avec le plan Starter** pour tester
2. **Utilisez une seule instance** de worker
3. **Passez à Standard** uniquement quand nécessaire
4. **Utilisez un CDN** pour réduire la bande passante

## 🚀 Mise à jour et CI/CD

### Déploiement automatique

Render redéploie automatiquement à chaque push sur la branche configurée.

```bash
git add .
git commit -m "Nouvelle fonctionnalité"
git push origin main
# 🎉 Render déploie automatiquement !
```

### Environnements multiples

Créez des environnements staging/production :

1. Branche `main` → Production
2. Branche `staging` → Environnement de test
3. Blueprint différent pour chaque environnement

### Rollback

Si un déploiement pose problème :

1. Dashboard → Service → Events
2. Trouvez le déploiement précédent
3. Cliquez "Redeploy"

Ou via CLI :

```bash
render rollback -s capocop-app
```

## 📚 Ressources

- [Documentation Render](https://render.com/docs)
- [Guide Laravel sur Render](https://render.com/docs/deploy-php-laravel-docker)
- [Render Community](https://community.render.com)
- [Status Render](https://status.render.com)

## 🆘 Support

- **Email** : support@render.com
- **Community** : [community.render.com](https://community.render.com)
- **Twitter** : [@render](https://twitter.com/render)

## ✅ Checklist de déploiement

- [ ] Code poussé sur Git (GitHub/GitLab/Bitbucket)
- [ ] Fichier `render.yaml` configuré
- [ ] Variables d'environnement configurées
- [ ] MySQL et Redis créés
- [ ] Service web déployé et accessible
- [ ] Worker actif et traitant les jobs
- [ ] Cron job planifié
- [ ] Domaine personnalisé configuré (optionnel)
- [ ] SSL/HTTPS actif
- [ ] Emails de test envoyés
- [ ] Backups de base de données vérifiés
- [ ] Monitoring activé
- [ ] Documentation mise à jour

## 🎉 Félicitations !

Votre application Capocop est maintenant déployée sur Render avec :

✅ Déploiement continu automatique  
✅ MySQL 8.x sécurisé  
✅ Redis pour les performances  
✅ HTTPS automatique  
✅ Scaling facile  
✅ Monitoring intégré  

**Bon déploiement ! 🚀**

