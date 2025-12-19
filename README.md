# 🛍️ Capocop - Plateforme E-commerce Laravel

Application e-commerce moderne construite avec Laravel 12, Livewire, Filament Admin et Jetstream.

## 🌟 Fonctionnalités

- 🛒 **Panier d'achat** complet avec gestion des sessions
- 💳 **Paiements en ligne** et paiements échelonnés
- 📦 **Gestion des commandes** avec suivi des expéditions
- ⭐ **Avis et notes** sur les produits
- ❤️ **Liste de favoris**
- 🏪 **Multi-vendeurs** avec gestion des boutiques
- 🎟️ **Coupons de réduction**
- 📊 **Tableau de bord Filament** pour l'administration
- 🔐 **Authentification sécurisée** avec Laravel Jetstream
- 📱 **Design responsive** avec Tailwind CSS

## 🚀 Déploiement

### Option 1 : Docker en Local (Développement)

Idéal pour le développement local avec MySQL 8.4, Redis, et phpMyAdmin.

```bash
# Windows
.\docker-start.ps1

# Linux/Mac
chmod +x docker-start.sh
./docker-start.sh
```

📖 **Documentation complète** : [DOCKER_SETUP.md](./DOCKER_SETUP.md)

**Services disponibles :**
- Application : http://localhost:8000
- phpMyAdmin : http://localhost:8080

### Option 2 : Render (Production)

Déploiement cloud professionnel avec infrastructure gérée.

**Démarrage rapide (5 minutes) :**

1. Push vers Git :
```bash
git push origin main
```

2. Sur [dashboard.render.com](https://dashboard.render.com) :
   - New + → Blueprint
   - Connecter votre dépôt
   - Apply

3. C'est tout ! ✅

📖 **Guides complets** :
- [RENDER_QUICKSTART.md](./RENDER_QUICKSTART.md) - Guide rapide
- [RENDER_DEPLOYMENT.md](./RENDER_DEPLOYMENT.md) - Guide détaillé

## 🛠️ Stack Technique

- **Framework** : Laravel 12
- **PHP** : 8.2+
- **Frontend** : Livewire 3, Tailwind CSS 3
- **Admin** : Filament 4
- **Base de données** : MySQL 8.4
- **Cache** : Redis 7
- **Server** : Nginx + PHP-FPM
- **Authentification** : Laravel Jetstream, Sanctum

## 📋 Prérequis

### Développement Local (Docker)
- Docker Desktop (Windows/Mac) ou Docker Engine (Linux)
- 4 GB RAM minimum

### Développement Sans Docker
- PHP 8.2+
- Composer
- Node.js 20+
- MySQL 8.x
- Redis (optionnel)

## 🔧 Installation Manuelle (Sans Docker)

```bash
# 1. Cloner le projet
git clone https://github.com/votre-repo/capocop.git
cd capocop

# 2. Installer les dépendances PHP
composer install

# 3. Installer les dépendances Node.js
npm install

# 4. Configurer l'environnement
cp .env.example .env
php artisan key:generate

# 5. Configurer la base de données dans .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=capocop
DB_USERNAME=root
DB_PASSWORD=

# 6. Exécuter les migrations
php artisan migrate

# 7. Créer le lien symbolique pour le storage
php artisan storage:link

# 8. Compiler les assets
npm run build

# 9. Démarrer le serveur
php artisan serve
```

## 📚 Structure du Projet

```
capocop/
├── app/
│   ├── Filament/         # Administration Filament
│   ├── Http/             # Controllers, Middleware
│   ├── Models/           # Modèles Eloquent
│   ├── Notifications/    # Notifications Email
│   ├── Observers/        # Model Observers
│   ├── Policies/         # Authorization Policies
│   └── Services/         # Services métier
├── database/
│   ├── migrations/       # Migrations de base de données
│   ├── seeders/          # Seeders
│   └── factories/        # Model Factories
├── docker/               # Configuration Docker
│   ├── nginx/            # Config Nginx (local)
│   ├── php/              # Config PHP (local)
│   ├── mysql/            # Config MySQL (local)
│   ├── supervisor/       # Config Supervisor (local)
│   └── render/           # Config Render (production)
├── resources/
│   ├── views/            # Vues Blade & Livewire
│   ├── css/              # Styles CSS
│   └── js/               # JavaScript
├── routes/               # Routes web et API
├── storage/              # Fichiers générés
├── public/               # Assets publics
├── tests/                # Tests PHPUnit
├── docker-compose.yml    # Orchestration Docker (local)
├── Dockerfile            # Image Docker (local)
├── Dockerfile.render     # Image Docker (Render)
└── render.yaml           # Infrastructure as Code (Render)
```

## 🔐 Sécurité

- ✅ Protection CSRF
- ✅ Validation des entrées
- ✅ Hachage des mots de passe (bcrypt)
- ✅ Authentification à deux facteurs disponible
- ✅ Sessions sécurisées
- ✅ Headers de sécurité (CSP, X-Frame-Options, etc.)
- ✅ Protection contre les injections SQL (Eloquent ORM)

## 🧪 Tests

```bash
# Exécuter tous les tests
php artisan test

# Tests avec couverture
php artisan test --coverage

# Tests spécifiques
php artisan test --filter=CartTest
```

## 📦 Commandes Artisan Utiles

```bash
# Vider les caches
php artisan optimize:clear

# Optimiser l'application
php artisan optimize

# Exécuter les queues
php artisan queue:work

# Exécuter le scheduler (cron)
php artisan schedule:run

# Créer un utilisateur admin
php artisan make:filament-user
```

## 🐛 Dépannage

### Erreur "No application encryption key"
```bash
php artisan key:generate
```

### Erreur de permissions (Linux/Mac)
```bash
sudo chown -R $USER:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### Problèmes avec les assets
```bash
npm run build
php artisan view:clear
```

### Base de données non accessible (Docker)
```bash
docker-compose down
docker-compose up -d
```

## 📊 Performance

### Optimisations recommandées pour la production :

```bash
# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Cache des événements
php artisan event:cache

# Autoloader optimisé
composer install --optimize-autoloader --no-dev
```

## 🌍 Localisation

L'application est configurée pour le français par défaut.

```env
APP_LOCALE=fr
APP_FALLBACK_LOCALE=en
```

## 📄 Licence

Ce projet est sous licence MIT.

## 🤝 Contribution

Les contributions sont les bienvenues !

1. Fork le projet
2. Créez une branche (`git checkout -b feature/AmazingFeature`)
3. Committez vos changements (`git commit -m 'Add AmazingFeature'`)
4. Poussez vers la branche (`git push origin feature/AmazingFeature`)
5. Ouvrez une Pull Request

## 📧 Support

Pour toute question ou problème :
- 📖 Consultez la documentation dans les fichiers .md
- 🐛 Ouvrez une issue sur GitHub
- 💬 Contactez l'équipe de développement

## 🙏 Remerciements

- Laravel - Framework PHP élégant
- Filament - Panneau d'administration moderne
- Livewire - Composants réactifs
- Tailwind CSS - Framework CSS utility-first
- Jetstream - Scaffolding d'authentification

---

**Développé avec ❤️ pour l'e-commerce moderne**
